<?php
/**
 * IncentivesController - Incentives management.
 *
 * Request handling, data access and business logic for views/admin/incentives.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';

function getIncentives($pdo) {
    $stmt = $pdo->query("
        SELECT i.*, e.first_name, e.last_name, e.employee_code 
        FROM incentives i 
        INNER JOIN employees e ON i.employee_id = e.employee_id 
        ORDER BY i.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getIncentive($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT i.*, e.first_name, e.last_name, e.employee_code 
        FROM incentives i 
        INNER JOIN employees e ON i.employee_id = e.employee_id 
        WHERE i.incentive_id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createIncentive($pdo, $employee_id, $name, $reason, $amount) {
    $stmt = $pdo->prepare("INSERT INTO incentives (employee_id, incentive_name, reason, amount) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$employee_id, $name, $reason, $amount]);
}

function updateIncentive($pdo, $id, $employee_id, $name, $reason, $amount) {
    $stmt = $pdo->prepare("UPDATE incentives SET employee_id = ?, incentive_name = ?, reason = ?, amount = ? WHERE incentive_id = ?");
    return $stmt->execute([$employee_id, $name, $reason, $amount, $id]);
}

function deleteIncentive($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM incentives WHERE incentive_id = ?");
    return $stmt->execute([$id]);
}

function searchIncentives($pdo, $search) {
    $stmt = $pdo->prepare("
        SELECT i.*, e.first_name, e.last_name, e.employee_code 
        FROM incentives i 
        INNER JOIN employees e ON i.employee_id = e.employee_id 
        WHERE i.incentive_name LIKE ? OR i.reason LIKE ? OR e.first_name LIKE ? OR e.last_name LIKE ? OR e.employee_code LIKE ?
        ORDER BY i.created_at DESC
    ");
    $searchTerm = '%' . $search . '%';
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

function getEmployees($pdo) {
    $stmt = $pdo->query("SELECT employee_id, first_name, last_name, employee_code FROM employees ORDER BY last_name, first_name");
    return $stmt->fetchAll();
}

function getIncentiveCount($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM incentives");
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

function getIncentivesByEmployee($pdo, $employee_id) {
    $stmt = $pdo->prepare("
        SELECT i.*, e.first_name, e.last_name, e.employee_code 
        FROM incentives i 
        INNER JOIN employees e ON i.employee_id = e.employee_id 
        WHERE i.employee_id = ?
        ORDER BY i.created_at DESC
    ");
    $stmt->execute([$employee_id]);
    return $stmt->fetchAll();
}

function tableExists($pdo) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE 'incentives'");
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

$incentives = [];
$employees = [];
$search = '';
$message = '';
$messageType = '';
$tableExists = false;
$incentiveCount = 0;
$filterEmployeeId = null;

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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $submissionToken = $_POST['submission_token'] ?? '';
            $sessionToken = $_SESSION['last_submission_token'] ?? '';

            if ($submissionToken && $submissionToken !== $sessionToken) {
                $_SESSION['last_submission_token'] = $submissionToken;

                if ($action === 'create') {
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $name = trim($_POST['incentive_name'] ?? '');
                    $reason = trim($_POST['reason'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);

                    if ($employee_id > 0 && !empty($name) && !empty($reason) && $amount > 0) {
                        if (createIncentive($pdo, $employee_id, $name, $reason, $amount)) {
                            $message = 'Incentive created successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to create incentive.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Please fill in all fields correctly and select an employee.';
                        $messageType = 'error';
                    }
                } elseif ($action === 'update') {
                    $id = intval($_POST['incentive_id'] ?? 0);
                    $employee_id = intval($_POST['employee_id'] ?? 0);
                    $name = trim($_POST['incentive_name'] ?? '');
                    $reason = trim($_POST['reason'] ?? '');
                    $amount = floatval($_POST['amount'] ?? 0);

                    if ($id > 0 && $employee_id > 0 && !empty($name) && !empty($reason) && $amount > 0) {
                        if (updateIncentive($pdo, $id, $employee_id, $name, $reason, $amount)) {
                            $message = 'Incentive updated successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to update incentive.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Please fill in all fields correctly and select an employee.';
                        $messageType = 'error';
                    }
                } elseif ($action === 'delete') {
                    $id = intval($_POST['incentive_id'] ?? 0);
                    if ($id > 0) {
                        if (deleteIncentive($pdo, $id)) {
                            $message = 'Incentive deleted successfully!';
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to delete incentive.';
                            $messageType = 'error';
                        }
                    }
                }
            }
        }

        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = trim($_GET['search']);
            $incentives = searchIncentives($pdo, $search);
        } elseif (isset($_GET['employee_id']) && !empty($_GET['employee_id'])) {
            $filterEmployeeId = intval($_GET['employee_id']);
            $incentives = getIncentivesByEmployee($pdo, $filterEmployeeId);
        } else {
            $incentives = getIncentives($pdo);
        }

        $incentiveCount = getIncentiveCount($pdo);
    }

} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
    $messageType = 'error';
}

$submissionToken = bin2hex(random_bytes(16));
$_SESSION['page_token'] = $submissionToken;

$totalAmount = 0;
$avgAmount = 0;
$maxAmount = 0;
$minAmount = 0;

if (!empty($incentives)) {
    $amounts = array_column($incentives, 'amount');
    $totalAmount = array_sum($amounts);
    $avgAmount = $totalAmount / count($incentives);
    $maxAmount = max($amounts);
    $minAmount = min($amounts);
}
