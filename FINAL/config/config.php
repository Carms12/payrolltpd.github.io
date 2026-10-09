<?php
/**
 * ================================================================
 * PAYROLL-TPD  -  SINGLE APPLICATION CONFIG
 * ================================================================
 * Everything that used to be copy-pasted at the top of every page
 * (error handling, session, database, security limits, log paths)
 * lives here and nowhere else.
 *
 * Load it with:  require_once __DIR__ . '/../config/config.php';
 * (controllers and the root index.php / logout.php already do this)
 * ================================================================
 */

// ----------------------------------------------------------------
// ENVIRONMENT
// ----------------------------------------------------------------
define('APP_DEBUG', false);                // never show PHP errors to visitors (they are written to logs/error.log)
define('APP_ROOT', dirname(__DIR__));      // project root folder
define('LOG_DIR',  APP_ROOT . '/logs');    // security.log / error.log

// ----------------------------------------------------------------
// ERROR HANDLING
// ----------------------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? 1 : 0);
ini_set('display_startup_errors', APP_DEBUG ? 1 : 0);
ini_set('log_errors', 1);

if (!is_dir(LOG_DIR)) {
    @mkdir(LOG_DIR, 0755, true);
}
ini_set('error_log', LOG_DIR . '/error.log');

// ----------------------------------------------------------------
// SESSION / LOGIN SECURITY SETTINGS
// ----------------------------------------------------------------
define('SESSION_IDLE_TIMEOUT',     900);    // auto logout after 15 minutes without activity
define('SESSION_ABSOLUTE_TIMEOUT', 28800);  // never stay logged in longer than 8 hours
define('LOGIN_MAX_FAILS',          5);      // wrong passwords (per IP) before a lock-out
define('LOGIN_LOCKOUT_SECONDS',    900);    // lock-out length (15 minutes)

function isHttpsRequest() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443)
        || (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

// ----------------------------------------------------------------
// SESSION (secure defaults, started once for the whole app)
// ----------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isHttpsRequest() ? 1 : 0);   // automatically on when served over HTTPS
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.cookie_lifetime', 0);        // cookie dies when the browser is closed
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_trans_sid', 0);
    ini_set('session.gc_maxlifetime', SESSION_ABSOLUTE_TIMEOUT);

    session_start();

    if (!isset($_SESSION['last_regeneration'])) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// ----------------------------------------------------------------
// DATABASE
// ----------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'payroll-tpd');
define('DB_USER', 'root');
define('DB_PASS', '');


/**
 * Open a database connection.
 *
 * Two profiles exist because the original pages were written against
 * two different connection setups. They are kept as-is so no query
 * changes behaviour:
 *
 *   'standard'  used by dashboard, attendance, bonus, incentives,
 *               position, deduction, salary, and login/logout.
 *               Exceptions on error, associative fetch.
 *
 *   'secure'    used by employees and cash_advance.
 *               utf8mb4, real prepared statements (no emulation),
 *               and errors are logged instead of shown to the user.
 *
 * @param string $profile 'standard' | 'secure'
 * @return PDO
 */
function getDBConnection($profile = 'standard') {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

    if ($profile === 'secure') {
        try {
            return new PDO(
                $dsn . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            if (function_exists('logSecurityEvent')) {
                logSecurityEvent("Database connection failed: " . $e->getMessage(), 'error');
            } else {
                error_log("Database connection failed: " . $e->getMessage());
            }
            die("System temporarily unavailable. Please try again later.");
        }
    }

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// ----------------------------------------------------------------
// SECURITY / RATE LIMITING
// ----------------------------------------------------------------
define('CSRF_TOKEN_LENGTH', 32);

// Default limit (used by employees)
define('MAX_LOGIN_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW', 300);          // seconds

// Cash advance uses looser limits (raised while testing)
define('CASH_ADVANCE_MAX_ATTEMPTS', 100);
define('CASH_ADVANCE_RATE_WINDOW', 60);    // seconds


// ================================================================
// ACCESS CONTROL  -  every admin page requires a valid login
// ================================================================
// Nothing under views/admin/ (or controllers/) can be opened without logging in:
// config.php is loaded first by every one of those scripts and the guard at the bottom
// of this file runs automatically. No page can forget to check.

function sendSecurityHeaders() {
    if (headers_sent()) return;
    header('X-Frame-Options: DENY');                       // cannot be put inside someone else's iframe
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (isHttpsRequest()) header('Strict-Transport-Security: max-age=31536000');
    header_remove('X-Powered-By');
}

function sendNoCacheHeaders() {
    // so the Back button cannot show a cached admin page after logout
    if (headers_sent()) return;
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

function securityLog($message, $type = 'warning') {
    if (!function_exists('logSecurityEvent')) {
        require_once __DIR__ . '/../controllers/helpers.php';
    }
    logSecurityEvent($message, $type);
}

function sessionFingerprint() {
    return hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? 'none');
}

/** True only for a logged-in session that is not expired, not idle too long and from the same browser. */
function isSessionValid() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) return false;
    if (!isset($_SESSION['login_time'], $_SESSION['last_activity'], $_SESSION['fingerprint'])) return false;
    $now = time();
    if ($now - (int)$_SESSION['login_time']    > SESSION_ABSOLUTE_TIMEOUT) return false;
    if ($now - (int)$_SESSION['last_activity'] > SESSION_IDLE_TIMEOUT)     return false;
    if (!hash_equals((string)$_SESSION['fingerprint'], sessionFingerprint())) return false;
    return true;
}

function destroySession() {
    $_SESSION = [];
    if (ini_get('session.use_cookies') && !headers_sent()) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    if (session_status() === PHP_SESSION_ACTIVE) session_destroy();
}

/** Wipe the login but keep a working (new, empty) session, e.g. to show the login page again. */
function resetSession() {
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

/** Blocks form posts that come from another website (CSRF protection for every POST/PUT/DELETE). */
function enforceSameOriginRequest() {
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) return;
    $source = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    if ($source === '') return;
    $srcHost = parse_url($source, PHP_URL_HOST);
    $myHost  = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
    if (!$srcHost || strcasecmp($srcHost, $myHost) !== 0) {
        securityLog('Blocked cross-site ' . $method . ' request from ' . $source, 'warning');
        http_response_code(403);
        exit('Forbidden');
    }
}

function adminLoginUrl() {
    $base = preg_replace('#/(views/admin|controllers)/[^?]*$#', '', $_SERVER['SCRIPT_NAME'] ?? '');
    return rtrim($base, '/') . '/index.php';
}

function requireAdminLogin() {
    sendNoCacheHeaders();

    if (isSessionValid()) {
        enforceSameOriginRequest();
        $_SESSION['last_activity'] = time();
        return;
    }

    $expired = !empty($_SESSION['admin_logged_in']);
    if (!$expired) securityLog('Blocked access without login: ' . ($_SERVER['REQUEST_URI'] ?? ''), 'warning');
    destroySession();

    $isPage = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
        && stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== false
        && ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'XMLHttpRequest';

    if ($isPage) {
        header('Location: ' . adminLoginUrl() . ($expired ? '?expired=1' : ''));
        exit;
    }
    http_response_code(401);
    exit('Authentication required');
}

sendSecurityHeaders();

$__script = isset($_SERVER['SCRIPT_FILENAME']) ? realpath($_SERVER['SCRIPT_FILENAME']) : false;
if ($__script) {
    foreach ([APP_ROOT . '/views/admin', APP_ROOT . '/controllers'] as $__d) {
        $__dir = realpath($__d);
        if ($__dir && strpos($__script, $__dir . DIRECTORY_SEPARATOR) === 0 && basename($__script) !== 'logout.php') {
            requireAdminLogin();
            break;
        }
    }
}
unset($__script, $__d, $__dir);
