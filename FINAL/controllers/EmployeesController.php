<?php
/**
 * EmployeesController - Employee management.
 *
 * Request handling, data access and business logic for views/admin/employees.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

function getEmployees($pdo, $positionFilter = null) {
    if ($positionFilter) {
        $stmt = $pdo->prepare(employeeSelect($pdo) . " WHERE position = ? ORDER BY last_name ASC, first_name ASC");
        $stmt->execute([$positionFilter]);
    } else {
        $stmt = $pdo->prepare(employeeSelect($pdo) . " ORDER BY last_name ASC, first_name ASC");
        $stmt->execute();
    }
    return $stmt->fetchAll();
}

function getEmployee($pdo, $id) {
    $stmt = $pdo->prepare("SELECT " . employeeColumnsNoPhoto($pdo, 'employees') . " FROM employees WHERE employee_id = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

// ---------------------------------------------------------------
// EMPLOYEE PHOTO - stored INSIDE the employees table (columns: photo MEDIUMBLOB, photo_mime, photo_updated).
// Shown through views/admin/employee_photo.php?id=...  (lists never load the image bytes, see employeeSelect()).
// ---------------------------------------------------------------
define('EMP_PHOTO_MAX_BYTES', 2 * 1024 * 1024);   // 2 MB upload limit
define('EMP_PHOTO_MAX_SIDE', 600);                // stored picture is scaled down to this many pixels (needs PHP GD)
define('EMP_PHOTO_LEGACY_DIR', __DIR__ . '/../assets/uploads/employees/');

// Makes sure employees has the photo columns (safe to run every time) and moves older photos
// (files from the first version, or the employee_photos table from the second) into the employees table.
function ensurePhotoColumns($pdo) {
    try {
        $cols = [];
        foreach ($pdo->query("SHOW COLUMNS FROM employees")->fetchAll() as $c) $cols[$c['Field']] = strtolower($c['Type']);

        // Old version stored a FILE NAME in `photo` (varchar). Remember it, clear it, then turn the column into a blob.
        $legacyFiles = [];
        if (isset($cols['photo']) && strpos($cols['photo'], 'blob') === false) {
            $legacyFiles = $pdo->query("SELECT employee_id, photo FROM employees WHERE photo IS NOT NULL AND photo != ''")->fetchAll();
            $pdo->exec("UPDATE employees SET photo = NULL");
            $pdo->exec("ALTER TABLE employees MODIFY photo MEDIUMBLOB NULL DEFAULT NULL");
        } elseif (!isset($cols['photo'])) {
            $pdo->exec("ALTER TABLE employees ADD COLUMN photo MEDIUMBLOB NULL DEFAULT NULL");
        }
        if (!isset($cols['photo_mime']))    $pdo->exec("ALTER TABLE employees ADD COLUMN photo_mime VARCHAR(50) NULL DEFAULT NULL");
        if (!isset($cols['photo_updated'])) $pdo->exec("ALTER TABLE employees ADD COLUMN photo_updated DATETIME NULL DEFAULT NULL");

        foreach ($legacyFiles as $r) {
            $file = EMP_PHOTO_LEGACY_DIR . basename($r['photo']);
            if (is_file($file) && ($info = @getimagesize($file)) && !empty($info['mime'])) {
                $stmt = $pdo->prepare("UPDATE employees SET photo = ?, photo_mime = ?, photo_updated = NOW() WHERE employee_id = ?");
                $stmt->bindValue(1, file_get_contents($file), PDO::PARAM_LOB);
                $stmt->bindValue(2, $info['mime']);
                $stmt->bindValue(3, (int)$r['employee_id'], PDO::PARAM_INT);
                if ($stmt->execute()) @unlink($file);
            }
        }

        // Second version used a separate employee_photos table: copy into employees, then drop it.
        if ($pdo->query("SHOW TABLES LIKE 'employee_photos'")->rowCount() > 0) {
            $pdo->exec("UPDATE employees e JOIN employee_photos p ON p.employee_id = e.employee_id
                        SET e.photo = p.data, e.photo_mime = p.mime, e.photo_updated = p.updated_at
                        WHERE e.photo IS NULL");
            $pdo->exec("DROP TABLE employee_photos");
        }
    } catch (PDOException $e) {
        logSecurityEvent('Photo column setup failed: ' . $e->getMessage(), 'error');
    }
}

// Validates an uploaded picture, scales it down when GD is available.
// Returns [['mime'=>..., 'data'=>...] | null, errorMessage | null]. null + null = no file chosen.
function readEmployeePhotoUpload($file) {
    if (!$file || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) return [null, null];
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) return [null, 'Photo is too large (max 2 MB).'];
    if ($file['error'] !== UPLOAD_ERR_OK) return [null, 'Photo upload failed. Please try again.'];
    if ($file['size'] > EMP_PHOTO_MAX_BYTES) return [null, 'Photo is too large (max 2 MB).'];

    // Check the real file content, not the name the browser sent.
    $info = @getimagesize($file['tmp_name']);
    $allowed = [IMAGETYPE_JPEG => 'image/jpeg', IMAGETYPE_PNG => 'image/png', IMAGETYPE_WEBP => 'image/webp'];
    if (!$info || !isset($allowed[$info[2]])) return [null, 'Photo must be a JPG, PNG or WEBP image.'];

    $data = file_get_contents($file['tmp_name']);
    if ($data === false) return [null, 'Cannot read the uploaded photo.'];
    $mime = $allowed[$info[2]];

    // Keep the database small: scale big pictures down to EMP_PHOTO_MAX_SIDE and save as JPEG.
    if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')
        && ($info[0] > EMP_PHOTO_MAX_SIDE || $info[1] > EMP_PHOTO_MAX_SIDE || strlen($data) > 300 * 1024)) {
        $src = @imagecreatefromstring($data);
        if ($src) {
            $ratio = min(1, EMP_PHOTO_MAX_SIDE / max($info[0], $info[1]));
            $w = max(1, (int)round($info[0] * $ratio)); $h = max(1, (int)round($info[1] * $ratio));
            $dst = imagecreatetruecolor($w, $h);
            imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));   // white behind transparent PNG/WEBP
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $info[0], $info[1]);
            ob_start(); imagejpeg($dst, null, 85); $jpeg = ob_get_clean();
            imagedestroy($src); imagedestroy($dst);
            if ($jpeg !== '' && $jpeg !== false) { $data = $jpeg; $mime = 'image/jpeg'; }
        }
    }
    return [['mime' => $mime, 'data' => $data], null];
}

function saveEmployeePhoto($pdo, $employeeId, $photo) {
    $stmt = $pdo->prepare("UPDATE employees SET photo = ?, photo_mime = ?, photo_updated = NOW() WHERE employee_id = ?");
    $stmt->bindValue(1, $photo['data'], PDO::PARAM_LOB);
    $stmt->bindValue(2, $photo['mime']);
    $stmt->bindValue(3, (int)$employeeId, PDO::PARAM_INT);
    return $stmt->execute();
}

function deleteEmployeePhoto($pdo, $employeeId) {
    $pdo->prepare("UPDATE employees SET photo = NULL, photo_mime = NULL, photo_updated = NULL WHERE employee_id = ?")->execute([(int)$employeeId]);
}

// "SELECT <every employee column EXCEPT the image bytes>, photo_v FROM employees"
// photo_v = unix time the photo was saved (NULL = no photo). Lists never carry the image itself.
function employeeSelect($pdo) {
    return "SELECT " . employeeColumnsNoPhoto($pdo, 'employees') . ", UNIX_TIMESTAMP(employees.photo_updated) AS photo_v FROM employees";
}

function createEmployee($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO employees (employee_code, first_name, last_name, position, hourly_rate, status, date_hired) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['employee_code'],
        $data['first_name'],
        $data['last_name'],
        $data['position'],
        $data['hourly_rate'],
        $data['status'],
        $data['date_hired']
    ]);
}

function updateEmployee($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE employees 
        SET employee_code = ?, first_name = ?, last_name = ?, position = ?, 
            hourly_rate = ?, status = ?, date_hired = ? 
        WHERE employee_id = ? LIMIT 1
    ");
    return $stmt->execute([
        $data['employee_code'],
        $data['first_name'],
        $data['last_name'],
        $data['position'],
        $data['hourly_rate'],
        $data['status'],
        $data['date_hired'],
        $id
    ]);
}

function deleteEmployee($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM employees WHERE employee_id = ? LIMIT 1");
    return $stmt->execute([$id]);
}

function searchEmployees($pdo, $search, $positionFilter = null) {
    if ($positionFilter) {
        $stmt = $pdo->prepare(employeeSelect($pdo) . "
            WHERE (first_name LIKE ? OR last_name LIKE ? OR employee_code LIKE ? OR position LIKE ?)
            AND position = ?
            ORDER BY last_name ASC, first_name ASC
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $positionFilter]);
    } else {
        $stmt = $pdo->prepare(employeeSelect($pdo) . "
            WHERE first_name LIKE ? OR last_name LIKE ? OR employee_code LIKE ? OR position LIKE ?
            ORDER BY last_name ASC, first_name ASC
        ");
        $searchTerm = '%' . $search . '%';
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    return $stmt->fetchAll();
}

function getEmployeeCount($pdo, $positionFilter = null) {
    if ($positionFilter) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM employees WHERE position = ?");
        $stmt->execute([$positionFilter]);
    } else {
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM employees");
    }
    $result = $stmt->fetch();
    return (int)($result['total'] ?? 0);
}

function employeeExists($pdo, $code, $excludeId = null) {
    if ($excludeId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE employee_code = ? AND employee_id != ?");
        $stmt->execute([$code, $excludeId]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE employee_code = ?");
        $stmt->execute([$code]);
    }
    return $stmt->fetchColumn() > 0;
}

function tableExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'employees'");
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

function getPositionsList($pdo) {
    try {
        $stmt = $pdo->query("SELECT position_id, position_name, hourly_rate FROM positions ORDER BY position_name ASC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function getPositionRate($pdo, $positionName) {
    try {
        $stmt = $pdo->prepare("SELECT hourly_rate FROM positions WHERE position_name = ? LIMIT 1");
        $stmt->execute([$positionName]);
        $result = $stmt->fetch();
        return $result ? (float)$result['hourly_rate'] : 0;
    } catch(PDOException $e) {
        return 0;
    }
}

function generateEmployeeCode($pdo) {
    $year = date('Y');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM employees WHERE employee_code LIKE ?");
    $stmt->execute([$year . '%']);
    $count = $stmt->fetchColumn() + 1;
    return $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
}

function getUniquePositions($pdo) {
    try {
        $stmt = $pdo->query("SELECT DISTINCT position FROM employees WHERE position IS NOT NULL AND position != '' ORDER BY position ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch(PDOException $e) {
        return [];
    }
}

$employees = [];
$search = '';
$positionFilter = '';
$message = '';
$messageType = '';
$tableExists = false;
$employeeCount = 0;
$csrfToken = '';
$positions = [];
$uniquePositions = [];

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCsrfToken();
}
$csrfToken = $_SESSION['csrf_token'];

try {
    $pdo = getDBConnection('secure');
    $tableExists = tableExists($pdo);

    if ($tableExists) {
        ensurePhotoColumns($pdo);
        $positions = getPositionsList($pdo);
        $uniquePositions = getUniquePositions($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if (!checkRateLimit($action)) {
                $message = 'Too many attempts. Please wait a few minutes and try again.';
                $messageType = 'error';
            } else {
                $action = sanitizeInput($action, 'string');

                if ($action === 'create') {
                    $positionName = sanitizeInput($_POST['position'] ?? '', 'string');
                    $hourlyRate = getPositionRate($pdo, $positionName);

                    $data = [
                        'employee_code' => sanitizeInput($_POST['employee_code'] ?? '', 'string'),
                        'first_name' => sanitizeInput($_POST['first_name'] ?? '', 'string'),
                        'last_name' => sanitizeInput($_POST['last_name'] ?? '', 'string'),
                        'position' => $positionName,
                        'hourly_rate' => $hourlyRate,
                        'status' => sanitizeInput($_POST['status'] ?? '', 'string'),
                        'date_hired' => sanitizeInput($_POST['date_hired'] ?? '', 'date')
                    ];

                    $errors = [];
                    if (empty($data['first_name'])) $errors[] = 'First name is required.';
                    if (empty($data['last_name'])) $errors[] = 'Last name is required.';
                    if (empty($data['position'])) $errors[] = 'Position is required.';
                    if ($data['hourly_rate'] <= 0) $errors[] = 'Hourly rate must be greater than 0. Please set a rate for this position in the Positions table.';
                    if (empty($data['status'])) $errors[] = 'Status is required.';
                    if (empty($data['date_hired'])) $errors[] = 'Date hired is required.';
                    if (empty($data['employee_code'])) {
                        $data['employee_code'] = generateEmployeeCode($pdo);
                    } elseif (employeeExists($pdo, $data['employee_code'])) {
                        $errors[] = 'Employee code already exists.';
                    }

                    $newPhoto = null;
                    if (empty($errors)) {
                        list($newPhoto, $photoError) = readEmployeePhotoUpload($_FILES['photo'] ?? null);
                        if ($photoError) $errors[] = $photoError;
                    }

                    if (!empty($errors)) {
                        $message = implode(' ', $errors);
                        $messageType = 'error';
                    } elseif (createEmployee($pdo, $data)) {
                        if ($newPhoto) saveEmployeePhoto($pdo, $pdo->lastInsertId(), $newPhoto);
                        $message = 'Employee created successfully!';
                        $messageType = 'success';
                        logSecurityEvent("Employee created: {$data['first_name']} {$data['last_name']}", 'info');
                    } else {
                        $message = 'Failed to create employee. Please try again.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'update') {
                    $id = sanitizeInput($_POST['employee_id'] ?? 0, 'int');
                    $positionName = sanitizeInput($_POST['position'] ?? '', 'string');
                    $hourlyRate = getPositionRate($pdo, $positionName);

                    $data = [
                        'employee_code' => sanitizeInput($_POST['employee_code'] ?? '', 'string'),
                        'first_name' => sanitizeInput($_POST['first_name'] ?? '', 'string'),
                        'last_name' => sanitizeInput($_POST['last_name'] ?? '', 'string'),
                        'position' => $positionName,
                        'hourly_rate' => $hourlyRate,
                        'status' => sanitizeInput($_POST['status'] ?? '', 'string'),
                        'date_hired' => sanitizeInput($_POST['date_hired'] ?? '', 'date')
                    ];

                    $errors = [];
                    if ($id <= 0) $errors[] = 'Invalid employee ID.';
                    if (empty($data['first_name'])) $errors[] = 'First name is required.';
                    if (empty($data['last_name'])) $errors[] = 'Last name is required.';
                    if (empty($data['position'])) $errors[] = 'Position is required.';
                    if ($data['hourly_rate'] <= 0) $errors[] = 'Hourly rate must be greater than 0. Please set a rate for this position in the Positions table.';
                    if (empty($data['status'])) $errors[] = 'Status is required.';
                    if (empty($data['date_hired'])) $errors[] = 'Date hired is required.';
                    if (empty($data['employee_code'])) {
                        $data['employee_code'] = generateEmployeeCode($pdo);
                    } elseif (employeeExists($pdo, $data['employee_code'], $id)) {
                        $errors[] = 'Employee code already exists.';
                    }

                    $newPhoto = null;
                    if (empty($errors)) {
                        list($newPhoto, $photoError) = readEmployeePhotoUpload($_FILES['photo'] ?? null);
                        if ($photoError) $errors[] = $photoError;
                    }

                    if (!empty($errors)) {
                        $message = implode(' ', $errors);
                        $messageType = 'error';
                    } elseif (updateEmployee($pdo, $id, $data)) {
                        if ($newPhoto) saveEmployeePhoto($pdo, $id, $newPhoto);               // add / replace
                        elseif (!empty($_POST['remove_photo'])) deleteEmployeePhoto($pdo, $id); // remove
                        $message = 'Employee updated successfully!';
                        $messageType = 'success';
                        logSecurityEvent("Employee updated: ID=$id, {$data['first_name']} {$data['last_name']}", 'info');
                    } else {
                        $message = 'Failed to update employee. Please try again.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'delete') {
                    $id = sanitizeInput($_POST['employee_id'] ?? 0, 'int');

                    if ($id <= 0) {
                        $message = 'Invalid employee ID.';
                        $messageType = 'error';
                    } else {
                        $employee = getEmployee($pdo, $id);
                        if (!$employee) {
                            $message = 'Employee not found.';
                            $messageType = 'error';
                        } elseif (deleteEmployee($pdo, $id)) {
                            deleteEmployeePhoto($pdo, $id);
                            $message = 'Employee deleted successfully!';
                            $messageType = 'success';
                            logSecurityEvent("Employee deleted: ID=$id, {$employee['first_name']} {$employee['last_name']}", 'info');
                        } else {
                            $message = 'Failed to delete employee. Please try again.';
                            $messageType = 'error';
                        }
                    }
                }

                $_SESSION['csrf_token'] = generateCsrfToken();
                $csrfToken = $_SESSION['csrf_token'];
            }
        }

        if (isset($_GET['position']) && !empty(trim($_GET['position']))) {
            $positionFilter = sanitizeInput(trim($_GET['position']), 'string');
        }

        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = sanitizeInput(trim($_GET['search']), 'string');
            $employees = searchEmployees($pdo, $search, $positionFilter ?: null);
        } else {
            $employees = getEmployees($pdo, $positionFilter ?: null);
        }

        $employeeCount = getEmployeeCount($pdo, $positionFilter ?: null);
    }

} catch(PDOException $e) {
    logSecurityEvent("Database error: " . $e->getMessage(), 'error');
    $message = 'A database error occurred. Please try again later.';
    $messageType = 'error';
} catch(Exception $e) {
    logSecurityEvent("General error: " . $e->getMessage(), 'error');
    $message = 'An unexpected error occurred. Please try again later.';
    $messageType = 'error';
}

$editEmployee = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $pdo = getDBConnection('secure');
        $editEmployee = getEmployee($pdo, intval($_GET['edit']));
    } catch(Exception $e) {
        // Silent fail
    }
}

$statusOptions = ['Regular', 'Project-Based'];
$submissionToken = bin2hex(random_bytes(16));
$_SESSION['page_token'] = $submissionToken;
