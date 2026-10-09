<?php
/**
 * SalaryController - Salary computation and payroll period logic (main page + print/export view).
 *
 * Request handling, data access and business logic for views/admin/salary.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

// --- SALARY PERIOD FUNCTIONS ---
function getPeriodDetails($period_id, $base_date = null, $custom_start = null, $custom_end = null) {
    if (!$base_date) $base_date = date('Y-m-d');

    if ($custom_start && $custom_end) {
        $start_date = $custom_start;
        $end_date = $custom_end;
        $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;
        return ['name' => 'Custom Period', 'days' => $days, 'start_date' => $start_date, 'end_date' => $end_date, 'type' => 'custom', 'period_id' => 6];
    }

    $timestamp = strtotime($base_date);
    $start_date = date('Y-m-01', $timestamp);
    $end_date = date('Y-m-d', $timestamp);
    $days = (strtotime($end_date) - strtotime($start_date)) / (60 * 60 * 24) + 1;

    return ['name' => 'Custom Period', 'days' => $days, 'start_date' => $start_date, 'end_date' => $end_date, 'type' => 'custom', 'period_id' => 6];
}

// --- Count working days (Mon-Fri) in a date range ---
function countWorkingDays($start_date, $end_date) {
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    $count = 0;
    foreach ($period as $dt) {
        $n = (int)$dt->format('N');
        if ($n >= 1 && $n <= 5) $count++; // Mon-Fri
    }
    return $count;
}

// --- Count total working days in the month(s) that the period spans ---
function countWorkingDaysInMonths($start_date, $end_date) {
    $start = new DateTime(date('Y-m-01', strtotime($start_date)));
    $end = new DateTime(date('Y-m-t', strtotime($end_date)));
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    $period = new DatePeriod($start, $interval, $end);

    $count = 0;
    foreach ($period as $dt) {
        $n = (int)$dt->format('N');
        if ($n >= 1 && $n <= 5) $count++;
    }
    return $count;
}

// Overtime policy (change here): extra hours beyond a normal day are paid only when there is at least
// OT_MIN_HOURS of them, at hourly rate x OT_MULTIPLIER (1.25 = the legal minimum for ordinary-day overtime).
if (!defined('OT_MULTIPLIER')) define('OT_MULTIPLIER', 1.25);
if (!defined('OT_MIN_HOURS'))  define('OT_MIN_HOURS', 1);

/**
 * SQL for hours worked in a day:
 *  - Time In earlier than 8:00 AM counts as 8:00 AM (7:59, 7:30, 6:30 ... all start at 8:00)
 *  - Overtime window is 8:00 AM until 6:00 AM the NEXT day: a Time Out from 12:00 AM to 6:00 AM that is
 *    earlier than the Time In means the shift ended the next day. Any other out-before-in is invalid (0 hours).
 *  - 1 hour break is deducted
 */
function sqlHoursWorked() {
    $in = "IF(time_in < '08:00:00', '08:00:00', time_in)";
    return "CASE WHEN time_in IS NOT NULL AND time_out IS NOT NULL THEN
                GREATEST((CASE WHEN TIME_TO_SEC(time_out) >= TIME_TO_SEC($in)
                               THEN TIME_TO_SEC(time_out) - TIME_TO_SEC($in)
                               WHEN TIME_TO_SEC(time_out) <= 21600
                               THEN TIME_TO_SEC(time_out) + 86400 - TIME_TO_SEC($in)
                               ELSE 0 END) / 3600 - 1, 0)
                ELSE 0 END";
}

function getAttendanceDetails($pdo, $employee_id, $start_date, $end_date) {
    $query = "SELECT 
                COUNT(*) as total_days,
                COUNT(CASE WHEN status = 'Present' THEN 1 END) as present_days,
                COUNT(CASE WHEN status = 'Late' THEN 1 END) as late_days,
                COUNT(CASE WHEN status = 'Half Day' THEN 1 END) as half_days,
                COUNT(CASE WHEN status = 'Absent' THEN 1 END) as absent_days
              FROM attendance 
              WHERE employee_id = ? 
              AND attendance_date BETWEEN ? AND ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$employee_id, $start_date, $end_date]);
    $result = $stmt->fetch();

    $detail_query = "SELECT 
                        attendance_date, time_in, time_out, status,
                        " . sqlHoursWorked() . " as hours_worked
                     FROM attendance 
                     WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?
                     AND status IN ('Present', 'Late', 'Half Day')";
    $detail_stmt = $pdo->prepare($detail_query);
    $detail_stmt->execute([$employee_id, $start_date, $end_date]);
    $details = $detail_stmt->fetchAll();

    $total_hours = 0;
    foreach ($details as $detail) {
        if ($detail['hours_worked'] > 0) $total_hours += $detail['hours_worked'];
    }

    $result['total_hours'] = $total_hours;
    $result['daily_details'] = $details;
    return $result;
}

function getEmployeeBonuses($pdo, $employee_id, $start_date, $end_date) {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_bonus FROM bonuses 
                           WHERE employee_id = ? AND DATE(created_at) BETWEEN ? AND ?");
    $stmt->execute([$employee_id, $start_date, $end_date]);
    $result = $stmt->fetch();
    return $result['total_bonus'] ?? 0;
}

function getEmployeeIncentives($pdo, $employee_id, $start_date, $end_date) {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) as total_incentive FROM incentives 
                           WHERE employee_id = ? AND DATE(created_at) BETWEEN ? AND ?");
    $stmt->execute([$employee_id, $start_date, $end_date]);
    $result = $stmt->fetch();
    return $result['total_incentive'] ?? 0;
}

// --- NEW: Get RAW monthly deductions for the period (SSS, Pag-IBIG, PhilHealth) ---
function getEmployeeRawDeductions($pdo, $employee_id, $start_date, $end_date) {
    $start_month = date('Y-m', strtotime($start_date));
    $end_month = date('Y-m', strtotime($end_date));

    $stmt = $pdo->prepare("SELECT deduction_type, COALESCE(SUM(amount), 0) as total 
                           FROM deductions 
                           WHERE employee_id = ? 
                           AND deduction_month BETWEEN ? AND ?
                           GROUP BY deduction_type");
    $stmt->execute([$employee_id, $start_month, $end_month]);

    $result = ['SSS' => 0, 'Pag-IBIG' => 0, 'PhilHealth' => 0, 'total' => 0];
    while ($row = $stmt->fetch()) {
        $result[$row['deduction_type']] = (float)$row['total'];
    }
    $result['total'] = $result['SSS'] + $result['Pag-IBIG'] + $result['PhilHealth'];
    return $result;
}

// --- NEW: Get PRORATED deductions based on period working days ---
function getEmployeeDeductions($pdo, $employee_id, $start_date, $end_date) {
    $raw = getEmployeeRawDeductions($pdo, $employee_id, $start_date, $end_date);

    // Count working days in period vs. working days in the month(s) spanned
    $period_working_days = countWorkingDays($start_date, $end_date);
    $month_working_days = countWorkingDaysInMonths($start_date, $end_date);

    // Proration ratio
    $ratio = 1.0;
    if ($month_working_days > 0) {
        $ratio = $period_working_days / $month_working_days;
        if ($ratio > 1) $ratio = 1.0; // safety cap
    }

    // Prorated amounts
    $sss = round($raw['SSS'] * $ratio, 2);
    $pagibig = round($raw['Pag-IBIG'] * $ratio, 2);
    $philhealth = round($raw['PhilHealth'] * $ratio, 2);
    $total = $sss + $pagibig + $philhealth;

    return [
        'SSS' => $sss,
        'Pag-IBIG' => $pagibig,
        'PhilHealth' => $philhealth,
        'total' => $total,
        'raw' => $raw,                      // Original monthly amounts
        'ratio' => $ratio,                  // e.g. 0.25 for 1 week
        'period_working_days' => $period_working_days,
        'month_working_days' => $month_working_days,
    ];
}

// Cash advance (CA) to deduct this payroll: same rule as the Cash Advance page's "process deduction"
// (Pending/Approved advances with a balance, oldest first, never more than the pay available).
// Read-only here: printing/viewing payroll does NOT change any advance balance.
function getEmployeeCashAdvanceDeduction($pdo, $employee_id, $end_date, $available_pay) {
    if ($available_pay <= 0) return 0;
    try {
        $stmt = $pdo->prepare("SELECT remaining_balance FROM cash_advances
                               WHERE employee_id = ? AND status IN ('Pending', 'Approved')
                               AND remaining_balance > 0 AND date_requested <= ?
                               ORDER BY date_requested ASC");
        $stmt->execute([$employee_id, $end_date]);
        $total = 0;
        foreach ($stmt->fetchAll() as $row) {
            $take = min((float)$row['remaining_balance'], $available_pay - $total);
            if ($take <= 0) break;
            $total += $take;
        }
        return round($total, 2);
    } catch (PDOException $e) {
        return 0;   // cash_advances table not available
    }
}

function calculateSalaryFromAttendance($pdo, $employee_id, $period_id, $base_date = null, $custom_start = null, $custom_end = null) {
    $emp_stmt = $pdo->prepare("SELECT " . employeeColumnsNoPhoto($pdo, 'e') . ", p.hourly_rate as position_hourly_rate 
                               FROM employees e
                               LEFT JOIN positions p ON e.position = p.position_name
                               WHERE e.employee_id = ?");
    $emp_stmt->execute([$employee_id]);
    $employee = $emp_stmt->fetch();

    if (!$employee) return null;

    $hourly_rate = 0;
    $daily_rate = 0;

    if (isset($employee['hourly_rate']) && $employee['hourly_rate'] > 0) {
        $hourly_rate = $employee['hourly_rate'];
        $daily_rate = $hourly_rate * 8;
    } else if (isset($employee['daily_rate']) && $employee['daily_rate'] > 0) {
        $daily_rate = $employee['daily_rate'];
        $hourly_rate = $daily_rate / 8;
    } else if (isset($employee['position_hourly_rate']) && $employee['position_hourly_rate'] > 0) {
        $hourly_rate = $employee['position_hourly_rate'];
        $daily_rate = $hourly_rate * 8;
    }

    if ($daily_rate <= 0 || $hourly_rate <= 0) return null;

    $period = getPeriodDetails($period_id, $base_date, $custom_start, $custom_end);
    if (!$period) return null;

    $attendance = getAttendanceDetails($pdo, $employee_id, $period['start_date'], $period['end_date']);

    $total_days = $attendance['total_days'] ?? 0;
    $present_days = $attendance['present_days'] ?? 0;
    $late_days = $attendance['late_days'] ?? 0;
    $half_days = $attendance['half_days'] ?? 0;
    $absent_days = $attendance['absent_days'] ?? 0;
    $total_hours = $attendance['total_hours'] ?? 0;

    $working_days = $present_days + $late_days;
    $full_days_worked = 0;
    $partial_hours = 0;
    $overtime_hours = 0;
    $standard_hours_per_day = 8;

    if ($working_days > 0) {
        $detail_stmt = $pdo->prepare("SELECT attendance_date, time_in, time_out, status,
                            " . sqlHoursWorked() . " as hours_worked
                         FROM attendance 
                         WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?
                         AND status IN ('Present', 'Late', 'Half Day')");
        $detail_stmt->execute([$employee_id, $period['start_date'], $period['end_date']]);
        foreach ($detail_stmt->fetchAll() as $detail) {
            $hours = $detail['hours_worked'];
            if ($hours >= $standard_hours_per_day) {
                $full_days_worked++;
                $extra = $hours - $standard_hours_per_day;
                if ($extra >= OT_MIN_HOURS) $overtime_hours += $extra;
            } else {
                $partial_hours += $hours;
            }
        }
    }

    $overtime_pay = $overtime_hours * $hourly_rate * OT_MULTIPLIER;
    $gross_pay = ($full_days_worked * $daily_rate) + ($partial_hours * $hourly_rate) + $overtime_pay;
    if ($total_days == 0 || $total_hours <= 0) $gross_pay = 0;

    $bonus_amount = getEmployeeBonuses($pdo, $employee_id, $period['start_date'], $period['end_date']);
    $incentive_amount = getEmployeeIncentives($pdo, $employee_id, $period['start_date'], $period['end_date']);

    // --- PRORATED DEDUCTIONS ---
    $deductions = getEmployeeDeductions($pdo, $employee_id, $period['start_date'], $period['end_date']);

    $cash_advance = getEmployeeCashAdvanceDeduction($pdo, $employee_id, $period['end_date'], $gross_pay + $bonus_amount + $incentive_amount - $deductions['total']);
    $net_pay = $gross_pay + $bonus_amount + $incentive_amount - $deductions['total'] - $cash_advance;
    $percentage_worked = ($gross_pay > 0) ? 100 : 0;

    return [
        'period_name' => $period['name'],
        'period_days' => $period['days'],
        'start_date' => $period['start_date'],
        'end_date' => $period['end_date'],
        'working_days' => $working_days,
        'present_days' => $present_days,
        'late_days' => $late_days,
        'half_days' => $half_days,
        'absent_days' => $absent_days,
        'total_hours' => $total_hours,
        'expected_hours' => $working_days * $standard_hours_per_day,
        'daily_rate' => $daily_rate,
        'hourly_rate' => $hourly_rate,
        'full_days_worked' => $full_days_worked,
        'partial_hours' => $partial_hours,
        'overtime_hours' => round($overtime_hours, 2),
        'overtime_pay' => round($overtime_pay, 2),
        'gross_pay' => $gross_pay,
        'bonus' => $bonus_amount,
        'incentive' => $incentive_amount,
        'deductions' => $deductions,
        'cash_advance' => $cash_advance,
        'net_pay' => $net_pay,
        'employee' => $employee,
        'attendance_details' => $attendance['daily_details'] ?? [],
        'percentage_worked' => $percentage_worked,
    ];
}

// --- GET FILTERS ---
$period_filter = 6;
$custom_start = isset($_GET['custom_start']) ? $_GET['custom_start'] : date('Y-m-01');
$custom_end = isset($_GET['custom_end']) ? $_GET['custom_end'] : date('Y-m-d');
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$auto_print = isset($_GET['auto_print']) ? $_GET['auto_print'] : 0;

function getPresetDateRange($preset) {
    $today = date('Y-m-d');
    switch ($preset) {
        case '5days': return ['start' => date('Y-m-d', strtotime('-4 days', strtotime($today))), 'end' => $today, 'label' => '5 Days'];
        case '1week': return ['start' => date('Y-m-d', strtotime('-6 days', strtotime($today))), 'end' => $today, 'label' => '1 Week'];
        case '15days': return ['start' => date('Y-m-d', strtotime('-14 days', strtotime($today))), 'end' => $today, 'label' => '15 Days'];
        case '30days': return ['start' => date('Y-m-d', strtotime('-29 days', strtotime($today))), 'end' => $today, 'label' => '30 Days'];
        case '3months': return ['start' => date('Y-m-d', strtotime('-2 months', strtotime($today))), 'end' => $today, 'label' => '3 Months'];
        case '5months': return ['start' => date('Y-m-d', strtotime('-4 months', strtotime($today))), 'end' => $today, 'label' => '5 Months'];
        default: return null;
    }
}

$preset = isset($_GET['preset']) ? $_GET['preset'] : '';
if ($preset && in_array($preset, ['5days', '1week', '15days', '30days', '3months', '5months'])) {
    $range = getPresetDateRange($preset);
    if ($range) { $custom_start = $range['start']; $custom_end = $range['end']; }
}

try {
    $pdo = getDBConnection();

    $employees_query = "SELECT " . employeeColumnsNoPhoto($pdo, 'e') . ", p.hourly_rate as position_hourly_rate 
                        FROM employees e
                        LEFT JOIN positions p ON e.position = p.position_name
                        WHERE e.status IN ('Regular', 'Project-Based', 'Contractual', 'Trainee/OJT')
                        ORDER BY e.employee_id";
    $employees_stmt = $pdo->query($employees_query);
    $employees = [];
    while ($row = $employees_stmt->fetch()) {
        if ($row['daily_rate'] && $row['daily_rate'] > 0) {
            $row['hourly_rate'] = $row['daily_rate'] / 8;
        } else if ($row['hourly_rate'] && $row['hourly_rate'] > 0) {
            $row['daily_rate'] = $row['hourly_rate'] * 8;
        } else if ($row['position_hourly_rate'] && $row['position_hourly_rate'] > 0) {
            $row['daily_rate'] = $row['position_hourly_rate'] * 8;
            $row['hourly_rate'] = $row['position_hourly_rate'];
        } else {
            $row['daily_rate'] = 0;
            $row['hourly_rate'] = 0;
        }
        $employees[] = $row;
    }

    $all_employee_salaries = [];
    $total_gross = 0;
    $total_bonus_all = 0;
    $total_incentive_all = 0;
    $total_deductions_all = 0;
    $total_sss_all = 0;
    $total_pagibig_all = 0;
    $total_philhealth_all = 0;
    $total_ca_all = 0;
    $total_net = 0;
    $period_info = getPeriodDetails($period_filter, date('Y-m-d'), $custom_start, $custom_end);

    foreach ($employees as $emp) {
        $salary = calculateSalaryFromAttendance($pdo, $emp['employee_id'], $period_filter, date('Y-m-d'), $custom_start, $custom_end);
        if ($salary) {
            $all_employee_salaries[$emp['employee_id']] = $salary;
            $total_gross += $salary['gross_pay'];
            $total_bonus_all += $salary['bonus'];
            $total_incentive_all += $salary['incentive'];
            $total_sss_all += $salary['deductions']['SSS'];
            $total_pagibig_all += $salary['deductions']['Pag-IBIG'];
            $total_philhealth_all += $salary['deductions']['PhilHealth'];
            $total_deductions_all += $salary['deductions']['total'];
            $total_ca_all += $salary['cash_advance'];
            $total_net += $salary['net_pay'];
        }
    }

    ksort($all_employee_salaries);

    // Compute the period-level ratio for display (uses any employee's deductions ratio, they're all the same)
    $period_ratio = 1.0;
    $period_working_days = 0;
    $month_working_days = 0;
    if (!empty($all_employee_salaries)) {
        $first = reset($all_employee_salaries);
        $period_ratio = $first['deductions']['ratio'];
        $period_working_days = $first['deductions']['period_working_days'];
        $month_working_days = $first['deductions']['month_working_days'];
    }

} catch(PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
}

$current_month_year = date('F Y', strtotime($custom_start));
$total_days_count = 0;
foreach ($all_employee_salaries as $salary) $total_days_count += $salary['working_days'];

$preset_label = '';
if ($preset && in_array($preset, ['5days', '1week', '15days', '30days', '3months', '5months'])) {
    $range = getPresetDateRange($preset);
    if ($range) $preset_label = $range['label'];
}

function getDateColumns($start_date, $end_date) {
    $columns = [];
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $end->modify('+1 day');
    $interval = new DateInterval('P1D');
    foreach (new DatePeriod($start, $interval, $end) as $dt) {
        $columns[] = [
            'date' => $dt->format('Y-m-d'),
            'day' => $dt->format('j'),
            'day_name' => $dt->format('D'),
            'is_weekend' => in_array($dt->format('N'), [6, 7])
        ];
    }
    return $columns;
}

$date_columns = getDateColumns($custom_start, $custom_end);

// If action is print, show the print view

// Export format for the print view (?action=print&format=pdf|docx|excel)
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
