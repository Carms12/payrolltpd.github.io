<?php
/**
 * BonusController - Bonus management with employee assignment.
 *
 * Request handling, data access and business logic for views/admin/bonus.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';

// ================================================================
// CRUD FUNCTIONS
// ================================================================

function getBonuses($pdo) {
    $stmt = $pdo->query("
        SELECT b.*, e.first_name, e.last_name, e.employee_code 
        FROM bonuses b 
        INNER JOIN employees e ON b.employee_id = e.employee_id 
        ORDER BY b.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getBonus($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT b.*, e.first_name, e.last_name, e.employee_code 
        FROM bonuses b 
        INNER JOIN employees e ON b.employee_id = e.employee_id 
        WHERE b.bonus_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createBonus($pdo, $employee_id, $name, $reason, $amount) {
    $stmt = $pdo->prepare("
        INSERT INTO bonuses 
        (employee_id, bonus_name, reason, amount) 
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$employee_id, $name, $reason, $amount]);
}

function updateBonus($pdo, $id, $employee_id, $name, $reason, $amount) {
    $stmt = $pdo->prepare("
        UPDATE bonuses 
        SET employee_id = ?, bonus_name = ?, reason = ?, amount = ? 
        WHERE bonus_id = ?
    ");
    return $stmt->execute([$employee_id, $name, $reason, $amount, $id]);
}

function deleteBonus($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM bonuses WHERE bonus_id = ?");
    return $stmt->execute([$id]);
}

function searchBonuses($pdo, $search) {
    $stmt = $pdo->prepare("
        SELECT b.*, e.first_name, e.last_name, e.employee_code 
        FROM bonuses b 
        INNER JOIN employees e ON b.employee_id = e.employee_id 
        WHERE b.bonus_name LIKE ? 
        OR b.reason LIKE ? 
        OR e.first_name LIKE ? 
        OR e.last_name LIKE ? 
        OR e.employee_code LIKE ?
        ORDER BY b.created_at DESC
    ");
    $searchTerm = '%' . $search . '%';
    $stmt->execute([
        $searchTerm, 
        $searchTerm, 
        $searchTerm, 
        $searchTerm, 
        $searchTerm
    ]);
    return $stmt->fetchAll();
}

function getEmployees($pdo) {
    $stmt = $pdo->query("
        SELECT employee_id, first_name, last_name, employee_code 
        FROM employees 
        ORDER BY last_name, first_name
    ");
    return $stmt->fetchAll();
}

function getBonusCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM bonuses");
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function getBonusesByEmployee($pdo, $employee_id) {
    $stmt = $pdo->prepare("
        SELECT b.*, e.first_name, e.last_name, e.employee_code 
        FROM bonuses b 
        INNER JOIN employees e ON b.employee_id = e.employee_id 
        WHERE b.employee_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

function tableExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'bonuses'");
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

// ================================================================
// INITIALIZE VARIABLES
// ================================================================

$bonuses = [];
$employees = [];
$search = '';
$message = '';
$messageType = '';
$tableExists = false;
$bonusCount = 0;
$filterEmployeeId = null;

// ================================================================
// MAIN PROCESSING
// ================================================================

try {
    $pdo = getDBConnection();
    $tableExists = tableExists($pdo);

    if ($tableExists) {
        try {
            $employees = getEmployees($pdo);
        } catch(PDOException $e) {
            $message = 'Error loading employees: ' . $e->getMessage();
            $messageType = 'error';
        }

        // Handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $submissionToken = $_POST['submission_token'] ?? '';
            $sessionToken = $_SESSION['last_submission_token'] ?? '';

            // Prevent duplicate submissions
            if ($submissionToken && $submissionToken !== $sessionToken) {
                $_SESSION['last_submission_token'] = $submissionToken;

                if ($action === 'create') {
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $name = trim($_POST['bonus_name'] ?? '');
                    $reason = trim($_POST['reason'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);

                    if ($employee_id > 0 && !empty($name) && !empty($reason) && $amount > 0) {
                        if (createBonus($pdo, $employee_id, $name, $reason, $amount)) {
                            $message = 'Bonus created successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to create bonus.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Please fill in all fields correctly and select an employee.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'update') {
                    $id = intval($_POST['bonus_id'] ?? 0);
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $name = trim($_POST['bonus_name'] ?? '');
                    $reason = trim($_POST['reason'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);

                    if ($id > 0 && $employee_id > 0 && !empty($name) && !empty($reason) && $amount > 0) {
                        if (updateBonus($pdo, $id, $employee_id, $name, $reason, $amount)) {
                            $message = 'Bonus updated successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to update bonus.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Please fill in all fields correctly and select an employee.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'delete') {
                    $id = intval($_POST['bonus_id'] ?? 0);
                    if ($id > 0) {
                        if (deleteBonus($pdo, $id)) {
                            $message = 'Bonus deleted successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to delete bonus.';
                            $messageType = 'error';
                        }
                    }
                }
            }
        }

        // Handle GET parameters
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = trim($_GET['search']);
            $bonuses = searchBonuses($pdo, $search);
        } elseif (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
            $filterEmployeeId = intval($_GET['employee_id']);
            $bonuses = getBonusesByEmployee($pdo, $filterEmployeeId);
        } else {
            $bonuses = getBonuses($pdo);
        }

        $bonusCount = getBonusCount($pdo);
    }

} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
    $messageType = 'error';
}

// Generate unique token
$submissionToken = bin2hex(random_bytes(16));
$_SESSION['page_token'] = $submissionToken;

// Calculate statistics
$totalAmount = 0;
$avgAmount = 0;
$maxAmount = 0;
$minAmount = 0;

if (!empty($bonuses)) {
    $amounts = array_column($bonuses, 'amount');
    $totalAmount = array_sum($amounts);
    $avgAmount = $totalAmount / count($bonuses);
    $maxAmount = max($amounts);
    $minAmount = min($amounts);
}

// ================================================================
// HTML OUTPUT
// ================================================================
