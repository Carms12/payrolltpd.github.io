<?php
require_once __DIR__ . '/helpers.php';

class AuthController {
    private $pdo;
    private $redirectPath;

    public function __construct($pdo = null, $redirectPath = 'index.php') {
        $this->pdo = $pdo;
        $this->redirectPath = $redirectPath;
    }

    /** If the user is already logged in, send them to $url and stop. */
    public function redirectIfLoggedIn($url) {
        if ($this->isLoggedIn()) {
            header('Location: ' . $url);
            exit();
        }
    }

    /** Login page opened while still logged in -> end that login so every visit needs a fresh sign-in. */
    public function forceFreshLogin() {
        if (!empty($_SESSION['admin_logged_in'])) {
            securityLog('Session ended: login page opened while logged in (user: ' . ($_SESSION['admin_username'] ?? '?') . ')', 'info');
            resetSession();
        }
    }

    /** One-time token that the login form must send back (login CSRF protection). */
    public function loginToken() {
        if (empty($_SESSION['login_token'])) {
            $_SESSION['login_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['login_token'];
    }

    /** Handle the login form POST. */
    public function handleLoginRequest($successUrl) {
        enforceSameOriginRequest();

        $username = trim((string)($_POST['username'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $token    = $_POST['login_token'] ?? '';

        if (empty($_SESSION['login_token']) || !is_string($token) || !hash_equals($_SESSION['login_token'], $token)) {
            $_SESSION['error'] = 'Your page expired. Please try again.';
        } else {
            unset($_SESSION['login_token']);   // one-time use

            $wait = $this->lockoutRemaining($username);
            if ($wait > 0) {
                $_SESSION['error'] = 'Too many failed attempts. Try again in ' . ceil($wait / 60) . ' minute(s).';
                securityLog('Login blocked (locked out): ' . $username, 'warning');
            } elseif ($username === '' || $password === '') {
                $_SESSION['error'] = 'Please enter both username and password';
            } elseif ($this->login($username, $password)) {
                $this->clearFailures($username);
                header('Location: ' . $successUrl);
                exit();
            } else {
                $this->recordFailure($username);
                securityLog('Failed login for: ' . $username, 'warning');
                usleep(300000);   // small delay slows down password guessing
                $_SESSION['error'] = 'Invalid username or password';
            }
        }

        header('Location: ' . $this->redirectPath);
        exit();
    }

    /** Login with username/email and password. Passwords are verified against a hash. */
    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
            $stmt->execute([':username' => trim($username), ':email' => trim($username)]);
            $user = $stmt->fetch();

            if (!$user) {
                // same amount of work whether or not the user exists (no username guessing by timing)
                static $dummy = null;
                if ($dummy === null) $dummy = password_hash('not-a-real-password', PASSWORD_DEFAULT);
                password_verify($password, $dummy);
                return false;
            }

            $stored = (string)$user['password'];
            $legacyPlain = false;
            if (!$this->passwordMatches($password, $stored, $legacyPlain)) {
                return false;
            }

            // Start a brand-new session id (prevents session fixation)
            session_regenerate_id(true);

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = (int)$user['id'];
            $_SESSION['admin_username']  = $user['username'];
            $_SESSION['admin_email']     = $user['email'];
            $_SESSION['admin_role']      = $user['role'] ?? 'admin';
            $_SESSION['login_time']      = time();
            $_SESSION['last_activity']   = time();
            $_SESSION['fingerprint']     = sessionFingerprint();
            $_SESSION['last_regeneration'] = time();

            securityLog('Login OK: ' . $user['username'], 'info');

            // Old plain-text password? Store it as a secure hash from now on.
            if ($legacyPlain) {
                $this->upgradeLegacyPassword((int)$user['id'], $password);
            }
            return true;
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
    }

    private function passwordMatches($password, $stored, &$legacyPlain) {
        $info = password_get_info($stored);
        if (!empty($info['algo'])) {
            $legacyPlain = false;
            return password_verify($password, $stored);
        }
        $legacyPlain = true;               // still stored as plain text
        return hash_equals($stored, $password);
    }

    /** Replace a plain-text password by a hash. Restores the old value if anything looks wrong, so nobody gets locked out. */
    private function upgradeLegacyPassword($userId, $plain) {
        try {
            $col  = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'password'")->fetch();
            $type = strtolower($col['Type'] ?? '');
            if (preg_match('/^(var)?char\((\d+)\)/', $type, $m) && (int)$m[2] < 255) {
                $this->pdo->exec("ALTER TABLE users MODIFY `password` VARCHAR(255) NOT NULL");
            }
            $upd = $this->pdo->prepare("UPDATE users SET password = :p WHERE id = :id");
            $upd->execute([':p' => password_hash($plain, PASSWORD_DEFAULT), ':id' => $userId]);

            $chk = $this->pdo->prepare("SELECT password FROM users WHERE id = :id");
            $chk->execute([':id' => $userId]);
            if (!password_verify($plain, (string)$chk->fetchColumn())) {
                $upd->execute([':p' => $plain, ':id' => $userId]);   // restore
                error_log('Password hash upgrade rolled back for user ' . $userId);
            } else {
                securityLog('Password upgraded to secure hash for user id ' . $userId, 'info');
            }
        } catch (Throwable $e) {
            error_log('Password upgrade skipped: ' . $e->getMessage());
        }
    }

    // ------------------------------------------------------------
    // Brute-force protection (file based, so clearing cookies does not reset it)
    // ------------------------------------------------------------
    private function throttleKeys($username) {
        return [
            'ip:' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') => LOGIN_MAX_FAILS,
            'user:' . strtolower($username)                 => LOGIN_MAX_FAILS * 4,
        ];
    }
    private function throttlePath($key) {
        $dir = LOG_DIR . '/login_attempts';
        if (!is_dir($dir)) @mkdir($dir, 0750, true);
        return $dir . '/' . hash('sha256', $key) . '.json';
    }
    private function throttleRead($key) {
        $f = $this->throttlePath($key);
        $d = is_file($f) ? json_decode((string)@file_get_contents($f), true) : null;
        return is_array($d) ? $d : ['count' => 0, 'first' => 0, 'until' => 0];
    }
    private function lockoutRemaining($username) {
        $now = time(); $wait = 0;
        foreach ($this->throttleKeys($username) as $key => $limit) {
            $d = $this->throttleRead($key);
            if (($d['until'] ?? 0) > $now) $wait = max($wait, $d['until'] - $now);
        }
        return $wait;
    }
    private function recordFailure($username) {
        $now = time();
        foreach ($this->throttleKeys($username) as $key => $limit) {
            $d = $this->throttleRead($key);
            if (empty($d['first']) || $now - $d['first'] > LOGIN_LOCKOUT_SECONDS) {
                $d = ['count' => 0, 'first' => $now, 'until' => 0];
            }
            $d['count']++;
            if ($d['count'] >= $limit) $d['until'] = $now + LOGIN_LOCKOUT_SECONDS;
            @file_put_contents($this->throttlePath($key), json_encode($d), LOCK_EX);
        }
    }
    private function clearFailures($username) {
        foreach ($this->throttleKeys($username) as $key => $limit) {
            @unlink($this->throttlePath($key));
        }
    }

    /** Is the user logged in with a valid (not expired / not idle) session? */
    public function isLoggedIn() {
        return isSessionValid();
    }

    /** Logout user and destroy session */
    public function logout() {
        if (!empty($_SESSION['admin_logged_in'])) {
            securityLog('Logout: ' . ($_SESSION['admin_username'] ?? '?'), 'info');
        }
        destroySession();
        sendNoCacheHeaders();
        header('Location: ' . $this->redirectPath);
        exit();
    }

    /** Get current user data */
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'] ?? null,
                'username' => $_SESSION['admin_username'] ?? null,
                'email' => $_SESSION['admin_email'] ?? null,
                'role' => $_SESSION['admin_role'] ?? 'user',
                'login_time' => $_SESSION['login_time'] ?? null
            ];
        }
        return null;
    }

    /** Require user to be logged in, redirect if not */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ' . $this->redirectPath);
            exit();
        }
    }

    /** Check if user has admin role */
    public function isAdmin() {
        return $this->isLoggedIn() && ($_SESSION['admin_role'] ?? 'user') === 'admin';
    }

    /** Require admin role, redirect if not */
    public function requireAdmin() {
        if (!$this->isAdmin()) {
            header('Location: ' . $this->redirectPath);
            exit();
        }
    }

    /** Update user password (always stored as a hash) */
    public function updatePassword($userId, $newPassword) {
        try {
            $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            return $stmt->execute([
                ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
                ':id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }
}
