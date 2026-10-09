<?php
/**
 * CashAdvanceController - Cash advance management.
 *
 * Request handling, data access and business logic for views/admin/cash_advance.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

// ================================================================
// FIX: Reset rate limiting for testing/development
// ================================================================
// Uncomment the line below to reset rate limit manually via URL parameter
if (isset($_GET['reset_rate_limit'])) {
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'rate_limit_') === 0) {
            unset($_SESSION[$key]);
        }
    }
    header("Location: " . str_replace('?reset_rate_limit=1', '', $_SERVER['REQUEST_URI']));
    exit;
}

// Auto-reset rate limits on localhost for easier testing
if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1') {
    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'rate_limit_') === 0) {
            unset($_SESSION[$key]);
        }
    }
}


// ================================================================
// DATABASE OPERATIONS
// ================================================================

function getEmployees($pdo) {
    $stmt = $pdo->prepare("SELECT employee_id, employee_code, first_name, last_name, position FROM employees ORDER BY last_name ASC, first_name ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCashAdvances($pdo, $statusFilter = null, $employeeFilter = null) {
    $sql = "
        SELECT ca.*, e.first_name, e.last_name, e.employee_code, e.position 
        FROM cash_advances ca
        LEFT JOIN employees e ON ca.employee_id = e.employee_id
        WHERE 1=1
    ";
    $params = [];

    if ($statusFilter) {
        $sql .= " AND ca.status = ?";
        $params[] = $statusFilter;
    }

    if ($employeeFilter) {
        $sql .= " AND ca.employee_id = ?";
        $params[] = $employeeFilter;
    }

    $sql .= " ORDER BY ca.date_requested DESC, ca.created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getCashAdvance($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT ca.*, e.first_name, e.last_name, e.employee_code, e.position 
        FROM cash_advances ca
        LEFT JOIN employees e ON ca.employee_id = e.employee_id
        WHERE ca.cash_advance_id = ? LIMIT 1
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function createCashAdvance($pdo, $data) {
    $stmt = $pdo->prepare("
        INSERT INTO cash_advances (employee_id, amount, repayment_amount, remaining_balance, date_requested, purpose, status, remarks) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['employee_id'],
        $data['amount'],
        $data['repayment_amount'],
        $data['remaining_balance'],
        $data['date_requested'],
        $data['purpose'],
        $data['status'],
        $data['remarks']
    ]);
}

function updateCashAdvance($pdo, $id, $data) {
    $stmt = $pdo->prepare("
        UPDATE cash_advances 
        SET employee_id = ?, amount = ?, repayment_amount = ?, remaining_balance = ?, 
            date_requested = ?, purpose = ?, status = ?, remarks = ? 
        WHERE cash_advance_id = ? LIMIT 1
    ");
    return $stmt->execute([
        $data['employee_id'],
        $data['amount'],
        $data['repayment_amount'],
        $data['remaining_balance'],
        $data['date_requested'],
        $data['purpose'],
        $data['status'],
        $data['remarks'],
        $id
    ]);
}

function deleteCashAdvance($pdo, $id) {
    $stmt = $pdo->prepare("DELETE FROM cash_advances WHERE cash_advance_id = ? LIMIT 1");
    return $stmt->execute([$id]);
}

function getCashAdvanceCount($pdo, $statusFilter = null) {
    $sql = "SELECT COUNT(*) as total FROM cash_advances";
    if ($statusFilter) {
        $sql .= " WHERE status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$statusFilter]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $result = $stmt->fetch();
    return (int)($result['total'] ?? 0);
}

function getTotalCashAdvanceAmount($pdo, $statusFilter = null) {
    $sql = "SELECT COALESCE(SUM(amount), 0) as total FROM cash_advances";
    if ($statusFilter) {
        $sql .= " WHERE status = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$statusFilter]);
    } else {
        $stmt = $pdo->query($sql);
    }
    $result = $stmt->fetch();
    return (float)($result['total'] ?? 0);
}

function getTotalRemainingBalance($pdo) {
    $stmt = $pdo->query("SELECT COALESCE(SUM(remaining_balance), 0) as total FROM cash_advances WHERE status != 'Fully Paid' AND status != 'Rejected'");
    $result = $stmt->fetch();
    return (float)($result['total'] ?? 0);
}

function tableExists($pdo, $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        return $stmt->rowCount() > 0;
    } catch(PDOException $e) {
        return false;
    }
}

// ================================================================
// CASH ADVANCE DEDUCTION ON SALARY
// ================================================================

function processCashAdvanceDeduction($pdo, $employeeId, $salaryAmount) {
    // Get all pending/approved cash advances for this employee
    $stmt = $pdo->prepare("
        SELECT cash_advance_id, amount, remaining_balance, status
        FROM cash_advances 
        WHERE employee_id = ? 
        AND status IN ('Pending', 'Approved')
        AND remaining_balance > 0
        ORDER BY date_requested ASC
    ");
    $stmt->execute([$employeeId]);
    $advances = $stmt->fetchAll();

    if (empty($advances)) {
        return ['success' => true, 'message' => 'No cash advances to deduct', 'total_deducted' => 0];
    }

    $totalDeduction = 0;
    $processedAdvances = [];

    foreach ($advances as $advance) {
        // Deduct the full remaining balance from salary
        $deductionAmount = $advance['remaining_balance'];

        // Check if salary can cover this deduction
        if ($salaryAmount < $totalDeduction + $deductionAmount) {
            // If salary can't cover all, deduct what's available
            $availableForDeduction = $salaryAmount - $totalDeduction;
            if ($availableForDeduction <= 0) {
                break;
            }
            $deductionAmount = $availableForDeduction;
        }

        // Calculate new remaining balance
        $newBalance = $advance['remaining_balance'] - $deductionAmount;
        $newStatus = ($newBalance <= 0) ? 'Fully Paid' : 'Approved';

        // Update the cash advance
        $updateStmt = $pdo->prepare("
            UPDATE cash_advances 
            SET remaining_balance = ?,
                repayment_amount = repayment_amount + ?,
                status = ?,
                remarks = CONCAT(IFNULL(remarks, ''), ' | Deducted ', ?, ' from salary on ', NOW())
            WHERE cash_advance_id = ?
        ");
        $updateStmt->execute([
            max(0, $newBalance),
            $deductionAmount,
            $newStatus,
            $deductionAmount,
            $advance['cash_advance_id']
        ]);

        $totalDeduction += $deductionAmount;
        $processedAdvances[] = $advance['cash_advance_id'];

        // If the advance is now fully paid, log it
        if ($newStatus == 'Fully Paid') {
            logSecurityEvent(
                "Cash advance fully paid: ID={$advance['cash_advance_id']}, Employee ID={$employeeId}, Total Amount={$advance['amount']}",
                'info'
            );
        }
    }

    // Log the deduction
    logSecurityEvent(
        "Cash advance deduction processed: Employee ID={$employeeId}, Total Deducted={$totalDeduction}, Advances=" . implode(',', $processedAdvances),
        'info'
    );

    return [
        'success' => true,
        'message' => "Successfully deducted {$totalDeduction} from employee's salary",
        'total_deducted' => $totalDeduction,
        'processed_advances' => $processedAdvances
    ];
}

// ================================================================
// REQUEST HANDLING
// ================================================================

$cashAdvances = [];
$employees = [];
$search = '';
$statusFilter = '';
$employeeFilter = '';
$message = '';
$messageType = '';
$tableExists = false;
$employeeCount = 0;
$csrfToken = '';
$totalAmount = 0;
$totalRemaining = 0;
$pendingCount = 0;
$approvedCount = 0;
$paidCount = 0;

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateCsrfToken();
}
$csrfToken = $_SESSION['csrf_token'];

try {
    $pdo = getDBConnection('secure');
    $tableExists = tableExists($pdo, 'cash_advances');

    if ($tableExists) {
        $employees = getEmployees($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            if (!checkRateLimit($action, CASH_ADVANCE_MAX_ATTEMPTS, CASH_ADVANCE_RATE_WINDOW)) {
                $message = 'Too many attempts. Please wait a few minutes and try again.';
                $messageType = 'error';
            } else {
                $action = sanitizeInput($action, 'string');
                $postToken = $_POST['csrf_token'] ?? '';

                if ($action === 'create') {
                    $amount = sanitizeInput($_POST['amount'] ?? 0, 'float');

                    $data = [
                        'employee_id' => sanitizeInput($_POST['employee_id'] ?? 0, 'int'),
                        'amount' => $amount,
                        'repayment_amount' => 0,
                        'remaining_balance' => $amount,
                        'date_requested' => sanitizeInput($_POST['date_requested'] ?? '', 'date'),
                        'purpose' => sanitizeInput($_POST['purpose'] ?? '', 'string'),
                        'status' => sanitizeInput($_POST['status'] ?? 'Pending', 'string'),
                        'remarks' => sanitizeInput($_POST['remarks'] ?? '', 'string')
                    ];

                    $errors = [];
                    if ($data['employee_id'] <= 0) $errors[] = 'Please select an employee.';
                    if ($data['amount'] <= 0) $errors[] = 'Amount must be greater than 0.';
                    if (empty($data['date_requested'])) $errors[] = 'Date requested is required.';
                    if (empty($data['status'])) $errors[] = 'Status is required.';

                    if (!empty($errors)) {
                        $message = implode(' ', $errors);
                        $messageType = 'error';
                    } elseif (createCashAdvance($pdo, $data)) {
                        $message = 'Cash advance created successfully!';
                        $messageType = 'success';
                        logSecurityEvent("Cash advance created: Employee ID={$data['employee_id']}, Amount={$data['amount']}", 'info');
                    } else {
                        $message = 'Failed to create cash advance. Please try again.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'update') {
                    $id = sanitizeInput($_POST['cash_advance_id'] ?? 0, 'int');
                    $amount = sanitizeInput($_POST['amount'] ?? 0, 'float');

                    $data = [
                        'employee_id' => sanitizeInput($_POST['employee_id'] ?? 0, 'int'),
                        'amount' => $amount,
                        'repayment_amount' => 0,
                        'remaining_balance' => $amount,
                        'date_requested' => sanitizeInput($_POST['date_requested'] ?? '', 'date'),
                        'purpose' => sanitizeInput($_POST['purpose'] ?? '', 'string'),
                        'status' => sanitizeInput($_POST['status'] ?? 'Pending', 'string'),
                        'remarks' => sanitizeInput($_POST['remarks'] ?? '', 'string')
                    ];

                    // If status is Fully Paid, set remaining balance to 0
                    if ($data['status'] === 'Fully Paid') {
                        $data['remaining_balance'] = 0;
                    }

                    $errors = [];
                    if ($id <= 0) $errors[] = 'Invalid cash advance ID.';
                    if ($data['employee_id'] <= 0) $errors[] = 'Please select an employee.';
                    if ($data['amount'] <= 0) $errors[] = 'Amount must be greater than 0.';
                    if (empty($data['date_requested'])) $errors[] = 'Date requested is required.';
                    if (empty($data['status'])) $errors[] = 'Status is required.';

                    if (!empty($errors)) {
                        $message = implode(' ', $errors);
                        $messageType = 'error';
                    } elseif (updateCashAdvance($pdo, $id, $data)) {
                        $message = 'Cash advance updated successfully!';
                        $messageType = 'success';
                        logSecurityEvent("Cash advance updated: ID=$id", 'info');
                    } else {
                        $message = 'Failed to update cash advance. Please try again.';
                        $messageType = 'error';
                    }

                } elseif ($action === 'delete') {
                    $id = sanitizeInput($_POST['cash_advance_id'] ?? 0, 'int');

                    if ($id <= 0) {
                        $message = 'Invalid cash advance ID.';
                        $messageType = 'error';
                    } else {
                        $cashAdvance = getCashAdvance($pdo, $id);
                        if (!$cashAdvance) {
                            $message = 'Cash advance not found.';
                            $messageType = 'error';
                        } elseif (deleteCashAdvance($pdo, $id)) {
                            $message = 'Cash advance deleted successfully!';
                            $messageType = 'success';
                            logSecurityEvent("Cash advance deleted: ID=$id", 'info');
                        } else {
                            $message = 'Failed to delete cash advance. Please try again.';
                            $messageType = 'error';
                        }
                    }
                } elseif ($action === 'process_deduction') {
                    // Manual trigger for deduction (for testing or manual processing)
                    $employeeId = sanitizeInput($_POST['employee_id'] ?? 0, 'int');
                    $salaryAmount = sanitizeInput($_POST['salary_amount'] ?? 0, 'float');

                    if ($employeeId <= 0 || $salaryAmount <= 0) {
                        $message = 'Invalid employee or salary amount.';
                        $messageType = 'error';
                    } else {
                        $result = processCashAdvanceDeduction($pdo, $employeeId, $salaryAmount);
                        if ($result['success']) {
                            $message = $result['message'];
                            $messageType = 'success';
                        } else {
                            $message = 'Failed to process deduction.';
                            $messageType = 'error';
                        }
                    }
                }

                $_SESSION['csrf_token'] = generateCsrfToken();
                $csrfToken = $_SESSION['csrf_token'];
            }
        }

        if (isset($_GET['status']) && !empty(trim($_GET['status']))) {
            $statusFilter = sanitizeInput(trim($_GET['status']), 'string');
        }

        if (isset($_GET['employee']) && !empty(trim($_GET['employee']))) {
            $employeeFilter = sanitizeInput(trim($_GET['employee']), 'int');
        }

        $cashAdvances = getCashAdvances($pdo, $statusFilter ?: null, $employeeFilter ?: null);
        $totalAmount = getTotalCashAdvanceAmount($pdo);
        $totalRemaining = getTotalRemainingBalance($pdo);
        $pendingCount = getCashAdvanceCount($pdo, 'Pending');
        $approvedCount = getCashAdvanceCount($pdo, 'Approved');
        $paidCount = getCashAdvanceCount($pdo, 'Fully Paid');
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

$editCashAdvance = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $pdo = getDBConnection('secure');
        $editCashAdvance = getCashAdvance($pdo, intval($_GET['edit']));
    } catch(Exception $e) {
        // Silent fail
    }
}

// Remove 'Partially Paid' from status options
$statusOptions = ['Pending', 'Approved', 'Rejected', 'Fully Paid'];
