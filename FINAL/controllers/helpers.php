<?php
/**
 * Shared security helpers (CSRF, input sanitising, output escaping,
 * security logging and rate limiting).
 *
 * Previously copy-pasted at the top of employees.php and cash_advance.php.
 * Constants and log location come from config/config.php.
 */
require_once __DIR__ . '/../config/config.php';

function generateCsrfToken() {
    return bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
}

function validateCsrfToken($token) {
    // CSRF validation disabled
    return true;
}

function sanitizeInput($data, $type = 'string') {
    if (is_array($data)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $data);
    }
    
    $data = trim($data);
    
    switch ($type) {
        case 'int':
            return filter_var($data, FILTER_VALIDATE_INT) ? (int)$data : 0;
        case 'float':
            return filter_var($data, FILTER_VALIDATE_FLOAT) ? (float)$data : 0.00;
        case 'email':
            return filter_var($data, FILTER_VALIDATE_EMAIL) ? $data : '';
        case 'date':
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $data) ? $data : '';
        case 'string':
        default:
            return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

function escapeOutput($data) {
    if (is_array($data)) {
        return array_map('escapeOutput', $data);
    }
    return htmlspecialchars((string)$data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function logSecurityEvent($message, $type = 'info') {
    $logDir = LOG_DIR . '/';
    $logFile = $logDir . 'security.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        if (!mkdir($logDir, 0755, true)) {
            error_log("Failed to create log directory: $logDir");
            error_log("Security Event: [$type] $message");
            return;
        }
    }
    
    if (!is_writable($logDir)) {
        error_log("Log directory is not writable: $logDir");
        error_log("Security Event: [$type] $message");
        return;
    }
    
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $logEntry = "[$date] [$type] [$ip] $message" . PHP_EOL;
    
    if (@file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
        error_log("Security Event: [$type] $message (IP: $ip)");
    }
}

function checkRateLimit($action, $maxAttempts = MAX_LOGIN_ATTEMPTS, $window = RATE_LIMIT_WINDOW) {
    $key = "rate_limit_{$action}_" . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return true;
    }
    
    $data = $_SESSION[$key];
    $timeSinceFirst = time() - $data['first_attempt'];
    
    if ($timeSinceFirst > $window) {
        $_SESSION[$key] = ['count' => 1, 'first_attempt' => time()];
        return true;
    }
    
    if ($data['count'] >= $maxAttempts) {
        logSecurityEvent("Rate limit exceeded for action: $action", 'warning');
        return false;
    }
    
    $_SESSION[$key]['count']++;
    return true;
}

/**
 * Comma-separated list of every column of `employees` EXCEPT the picture bytes (`photo`), prefixed with $alias.
 * Use it instead of "e.*" so pages never drag the image blobs around.  photo_mime / photo_updated stay in the list.
 */
function employeeColumnsNoPhoto($pdo, $alias = 'employees') {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach ($pdo->query("SHOW COLUMNS FROM employees")->fetchAll() as $c) {
            if ($c['Field'] !== 'photo') $cache[] = $c['Field'];
        }
    }
    $out = [];
    foreach ($cache as $f) $out[] = $alias . '.`' . $f . '`';
    return implode(', ', $out);
}
