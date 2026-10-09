<?php
/**
 * DashboardController - Payroll dashboard (totals, recent employees/attendance, payroll summary).
 *
 * Request handling, data access and business logic for views/admin/dashboard.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';

// Get total employees
function getTotalEmployees($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM employees");
    $result = $stmt->fetch();
    return $result['total'] ?? 0;
}

// Get total attendance today
function getTodayAttendance($pdo) {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present, SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent, SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) as late FROM attendance WHERE attendance_date = ?");
    $stmt->execute([$today]);
    return $stmt->fetch();
}

// Get total bonuses
function getTotalBonuses($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount FROM bonuses");
    return $stmt->fetch();
}

// Get total incentives
function getTotalIncentives($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount FROM incentives");
    return $stmt->fetch();
}

// Get total cash advances
function getTotalCashAdvances($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount FROM cash_advances WHERE status IN ('Approved', 'Partially Paid')");
    return $stmt->fetch();
}

// Get total deductions (monthly deductions table)
function getTotalDeductions($pdo) {
    try {
        $currentMonth = date('Y-m');
        $stmt = $pdo->prepare("SELECT COUNT(*) as total, COALESCE(SUM(amount), 0) as total_amount FROM deductions WHERE deduction_month = ?");
        $stmt->execute([$currentMonth]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return ['total' => 0, 'total_amount' => 0];
    }
}

// Get recent employees
function getRecentEmployees($pdo, $limit = 5) {
    $stmt = $pdo->query("SELECT employee_id, first_name, last_name, position, status, date_hired FROM employees ORDER BY date_hired DESC LIMIT $limit");
    return $stmt->fetchAll();
}

// Get recent attendance
function getRecentAttendance($pdo, $limit = 5) {
    $stmt = $pdo->query("SELECT a.*, e.first_name, e.last_name, e.employee_code FROM attendance a INNER JOIN employees e ON a.employee_id = e.employee_id ORDER BY a.created_at DESC LIMIT $limit");
    return $stmt->fetchAll();
}

// Get monthly attendance summary (last 30 days)
function getMonthlyAttendanceSummary($pdo) {
    $stmt = $pdo->query("SELECT attendance_date, COUNT(*) as total, SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) as present, SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) as absent FROM attendance WHERE attendance_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY attendance_date ORDER BY attendance_date ASC");
    return $stmt->fetchAll();
}

// Get employee count by status
function getEmployeeCountByStatus($pdo) {
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM employees GROUP BY status");
    return $stmt->fetchAll();
}

// Get payroll summary (current month)
function getPayrollSummary($pdo) {
    $currentMonth = date('Y-m');

    $stmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT a.employee_id) as employees_with_attendance,
            COUNT(*) as total_attendance,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_days
        FROM attendance a
        WHERE DATE_FORMAT(a.attendance_date, '%Y-%m') = ?
    ");
    $stmt->execute([$currentMonth]);
    $result = $stmt->fetch();

    // Get total monthly deductions for current month
    $stmt2 = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_deductions FROM deductions WHERE deduction_month = ?");
    $stmt2->execute([$currentMonth]);
    $deductions = $stmt2->fetch();

    return [
        'employees_with_attendance' => $result['employees_with_attendance'] ?? 0,
        'total_attendance' => $result['total_attendance'] ?? 0,
        'present_days' => $result['present_days'] ?? 0,
        'total_deductions' => $deductions['total_deductions'] ?? 0
    ];
}

$pdo = null;
$totalEmployees = 0;
$todayAttendance = null;
$totalBonuses = null;
$totalIncentives = null;
$totalCashAdvances = null;
$totalDeductions = null;
$recentEmployees = [];
$recentAttendance = [];
$monthlySummary = [];
$employeeStatus = [];
$payrollSummary = null;
$message = '';
$messageType = '';

try {
    $pdo = getDBConnection();

    $totalEmployees = getTotalEmployees($pdo);
    $todayAttendance = getTodayAttendance($pdo);
    $totalBonuses = getTotalBonuses($pdo);
    $totalIncentives = getTotalIncentives($pdo);
    $totalCashAdvances = getTotalCashAdvances($pdo);
    $totalDeductions = getTotalDeductions($pdo);
    $recentEmployees = getRecentEmployees($pdo);
    $recentAttendance = getRecentAttendance($pdo);
    $monthlySummary = getMonthlyAttendanceSummary($pdo);
    $employeeStatus = getEmployeeCountByStatus($pdo);
    $payrollSummary = getPayrollSummary($pdo);

} catch(PDOException $e) {
    $message = 'Database error: ' . $e->getMessage();
    $messageType = 'error';
}

$regularCount = 0;
$projectBasedCount = 0;
foreach ($employeeStatus as $status) {
    if ($status['status'] === 'Regular') {
        $regularCount = $status['count'];
    } elseif ($status['status'] === 'Project-Based') {
        $projectBasedCount = $status['count'];
    }
}

$today = date('F d, Y');
$currentTime = date('h:i A');
