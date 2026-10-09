<?php
/**
 * DeductionController - Monthly deductions (SSS, Pag-IBIG, PhilHealth) with auto-generation.
 *
 * Request handling, data access and business logic for views/admin/deduction.php.
 * Extracted from the page so the view contains HTML only.
 * Variables set here are used by the view.
 */
require_once __DIR__ . '/../config/config.php';

$pdo = getDBConnection();

// --- AUTO-CREATE TABLES ---
$pdo->exec("CREATE TABLE IF NOT EXISTS deductions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    deduction_type ENUM('SSS','Pag-IBIG','PhilHealth') NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    deduction_month CHAR(7) NOT NULL,
    notes VARCHAR(255) DEFAULT NULL,
    auto_generated TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_employee (employee_id),
    INDEX idx_type (deduction_type),
    INDEX idx_month (deduction_month),
    UNIQUE KEY unique_emp_type_month (employee_id, deduction_type, deduction_month)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$pdo->exec("CREATE TABLE IF NOT EXISTS deduction_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    sss DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    pagibig DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    philhealth DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_employee (employee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Migrate
$cols = $pdo->query("SHOW COLUMNS FROM deductions")->fetchAll(PDO::FETCH_COLUMN);
if (!in_array('auto_generated', $cols)) {
    $pdo->exec("ALTER TABLE deductions ADD COLUMN auto_generated TINYINT(1) NOT NULL DEFAULT 0");
}
if (in_array('deduction_date', $cols) && !in_array('deduction_month', $cols)) {
    $pdo->exec("ALTER TABLE deductions CHANGE deduction_date deduction_month CHAR(7) NOT NULL");
}

// --- HANDLE ADD ---
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_deduction'])) {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $deduction_type = $_POST['deduction_type'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $deduction_month = $_POST['deduction_month'] ?? date('Y-m');
    $notes = trim($_POST['notes'] ?? '');

    $valid_types = ['SSS', 'Pag-IBIG', 'PhilHealth'];

    if ($employee_id && in_array($deduction_type, $valid_types) && $amount > 0 && $deduction_month) {
        try {
            $stmt = $pdo->prepare("INSERT INTO deductions (employee_id, deduction_type, amount, deduction_month, notes, auto_generated)
                                   VALUES (?, ?, ?, ?, ?, 0)
                                   ON DUPLICATE KEY UPDATE amount = VALUES(amount), notes = VALUES(notes)");
            $stmt->execute([$employee_id, $deduction_type, $amount, $deduction_month, $notes ?: null]);
            $message = $deduction_type . " deduction saved for " . date('F Y', strtotime($deduction_month . '-01')) . ".";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $message = "Please fill in all required fields with valid values.";
        $message_type = 'error';
    }
}

// --- HANDLE EDIT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_deduction'])) {
    $id = (int)$_POST['id'];
    $deduction_type = $_POST['deduction_type'] ?? '';
    $amount = (float)($_POST['amount'] ?? 0);
    $deduction_month = $_POST['deduction_month'] ?? date('Y-m');
    $notes = trim($_POST['notes'] ?? '');

    $valid_types = ['SSS', 'Pag-IBIG', 'PhilHealth'];

    if ($id && in_array($deduction_type, $valid_types) && $amount > 0) {
        try {
            $stmt = $pdo->prepare("UPDATE deductions SET deduction_type = ?, amount = ?, deduction_month = ?, notes = ? WHERE id = ?");
            $stmt->execute([$deduction_type, $amount, $deduction_month, $notes ?: null, $id]);
            $message = "Deduction updated successfully.";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// --- HANDLE DELETE ---
if (isset($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM deductions WHERE id = ?");
        $stmt->execute([(int)$_GET['delete']]);
        $message = "Deduction deleted successfully.";
        $message_type = 'success';
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// --- HANDLE SAVE DEFAULT SETTINGS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $emp_id = trim($_POST['settings_employee_id'] ?? '');
    $sss = (float)($_POST['settings_sss'] ?? 0);
    $pagibig = (float)($_POST['settings_pagibig'] ?? 0);
    $philhealth = (float)($_POST['settings_philhealth'] ?? 0);

    if ($emp_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO deduction_settings (employee_id, sss, pagibig, philhealth)
                                   VALUES (?, ?, ?, ?)
                                   ON DUPLICATE KEY UPDATE sss = VALUES(sss), pagibig = VALUES(pagibig), philhealth = VALUES(philhealth)");
            $stmt->execute([$emp_id, $sss, $pagibig, $philhealth]);
            $message = "Default deductions saved.";
            $message_type = 'success';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// --- AUTO-GENERATE CURRENT MONTH DEDUCTIONS ---
$auto_generated_count = 0;
$current_month = date('Y-m');

$active_employees = $pdo->query("SELECT employee_id FROM employees
                                 WHERE status IN ('Regular', 'Project-Based', 'Contractual', 'Trainee/OJT')")->fetchAll(PDO::FETCH_COLUMN);

$settings_stmt = $pdo->prepare("SELECT sss, pagibig, philhealth FROM deduction_settings WHERE employee_id = ?");
$check_stmt = $pdo->prepare("SELECT id FROM deductions WHERE employee_id = ? AND deduction_type = ? AND deduction_month = ?");
$insert_stmt = $pdo->prepare("INSERT IGNORE INTO deductions (employee_id, deduction_type, amount, deduction_month, notes, auto_generated)
                              VALUES (?, ?, ?, ?, 'Auto-generated', 1)");

foreach ($active_employees as $emp_id) {
    $settings_stmt->execute([$emp_id]);
    $settings = $settings_stmt->fetch();

    if (!$settings) continue;

    $types = [
        'SSS' => (float)$settings['sss'],
        'Pag-IBIG' => (float)$settings['pagibig'],
        'PhilHealth' => (float)$settings['philhealth'],
    ];

    foreach ($types as $type => $amount) {
        if ($amount <= 0) continue;

        $check_stmt->execute([$emp_id, $type, $current_month]);
        if (!$check_stmt->fetch()) {
            $insert_stmt->execute([$emp_id, $type, $amount, $current_month]);
            if ($insert_stmt->rowCount() > 0) $auto_generated_count++;
        }
    }
}

if ($auto_generated_count > 0) {
    $message = "Auto-generated $auto_generated_count deduction record(s) for " . date('F Y') . ".";
    $message_type = 'info';
}

// --- FETCH EMPLOYEES ---
$employees = $pdo->query("SELECT employee_id, first_name, last_name, position, status
                          FROM employees
                          WHERE status IN ('Regular', 'Project-Based', 'Contractual', 'Trainee/OJT')
                          ORDER BY first_name, last_name")->fetchAll();

$employee_map = [];
foreach ($employees as $e) {
    $employee_map[$e['employee_id']] = $e;
}

// --- FETCH SETTINGS ---
$settings_all = [];
$settings_query = $pdo->query("SELECT * FROM deduction_settings");
while ($s = $settings_query->fetch()) {
    $settings_all[$s['employee_id']] = $s;
}

// --- AVAILABLE MONTHS ---
$months_raw = $pdo->query("SELECT DISTINCT deduction_month FROM deductions ORDER BY deduction_month DESC")->fetchAll(PDO::FETCH_COLUMN);

// --- FILTERS ---
$filter_employee = $_GET['filter_employee'] ?? '';
$filter_type = $_GET['filter_type'] ?? '';
$filter_month = $_GET['filter_month'] ?? date('Y-m');

$where = [];
$params = [];

if ($filter_employee) { $where[] = "d.employee_id = ?"; $params[] = $filter_employee; }
if ($filter_type) { $where[] = "d.deduction_type = ?"; $params[] = $filter_type; }
if ($filter_month) { $where[] = "d.deduction_month = ?"; $params[] = $filter_month; }

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$sql = "SELECT d.*, e.first_name, e.last_name, e.position
        FROM deductions d
        LEFT JOIN employees e ON d.employee_id = e.employee_id
        $where_sql
        ORDER BY d.deduction_month DESC, e.first_name ASC, d.deduction_type";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$deductions = $stmt->fetchAll();

// --- GROUP BY EMPLOYEE ---
// If filter_type is set, we show the flat list; otherwise grouped by employee
$grouped = [];
foreach ($deductions as $d) {
    $key = $d['employee_id'];
    if (!isset($grouped[$key])) {
        $grouped[$key] = [
            'employee_id' => $key,
            'first_name' => $d['first_name'],
            'last_name' => $d['last_name'],
            'position' => $d['position'],
            'deduction_month' => $d['deduction_month'],
            'SSS' => 0, 'Pag-IBIG' => 0, 'PhilHealth' => 0,
            'ids' => ['SSS' => null, 'Pag-IBIG' => null, 'PhilHealth' => null],
            'notes' => ['SSS' => null, 'Pag-IBIG' => null, 'PhilHealth' => null],
            'auto' => ['SSS' => 0, 'Pag-IBIG' => 0, 'PhilHealth' => 0],
        ];
    }
    $type = $d['deduction_type'];
    $grouped[$key][$type] = (float)$d['amount'];
    $grouped[$key]['ids'][$type] = $d['id'];
    $grouped[$key]['notes'][$type] = $d['notes'];
    $grouped[$key]['auto'][$type] = $d['auto_generated'];
}

// --- TOTALS ---
$total_sss = 0; $total_pagibig = 0; $total_philhealth = 0;
foreach ($deductions as $d) {
    if ($d['deduction_type'] === 'SSS') $total_sss += (float)$d['amount'];
    if ($d['deduction_type'] === 'Pag-IBIG') $total_pagibig += (float)$d['amount'];
    if ($d['deduction_type'] === 'PhilHealth') $total_philhealth += (float)$d['amount'];
}
$grand_total = $total_sss + $total_pagibig + $total_philhealth;
