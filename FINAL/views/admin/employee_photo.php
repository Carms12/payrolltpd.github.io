<?php
/**
 * Streams an employee's picture from employees.photo (database).  Usage: employee_photo.php?id=EMPLOYEE_ID
 * Only for logged-in admins (same session as the rest of the admin pages).
 */
require_once __DIR__ . '/../../config/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(404); exit; }

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT photo_mime AS mime, photo AS data, UNIX_TIMESTAMP(photo_updated) AS ts FROM employees WHERE employee_id = ? AND photo IS NOT NULL LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
} catch (Exception $e) {
    $row = false;
}

if (!$row) { http_response_code(404); exit; }

$etag = '"' . $id . '-' . $row['ts'] . '"';
header('ETag: ' . $etag);
header('Cache-Control: private, max-age=86400');
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}

header('Content-Type: ' . $row['mime']);
header('X-Content-Type-Options: nosniff');
header('Content-Length: ' . strlen($row['data']));
echo $row['data'];
