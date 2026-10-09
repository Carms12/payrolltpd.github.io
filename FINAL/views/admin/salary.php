<?php
require_once __DIR__ . '/../../controllers/SalaryController.php';

// Print / export view (?action=print)
if ($action == 'print') {
    include __DIR__ . '/partials/salary_print.php';
    exit;
}

// Payslip view (?action=payslip&employee_id=ID  -> one employee, omit employee_id -> every employee, one page each)
if ($action == 'payslip') {
    include __DIR__ . '/partials/payslip.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Pro · Salary Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/salary.css">
    <link rel="stylesheet" href="../../assets/css/mobile.css">
</head>
<body>

<div class="export-loading" id="exportLoading">
    <div class="spinner"></div>
    <div class="loading-text">Generating document...</div>
</div>

<div class="bg-grid"></div>
<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>

<div id="overlay" class="overlay" onclick="toggleSidebar()"></div>
<button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(0,255,255,0.08); border: 1px solid rgba(0,255,255,0.06);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00ffff" stroke-width="2">
                    <rect x="4" y="2" width="16" height="20" rx="2"></rect>
                    <line x1="9" y1="22" x2="15" y2="22"></line>
                </svg>
            </div>
            <div><span class="text-sm font-bold text-white tracking-tight">TPD<span style="color:#00ffff;">Solutions</span></span></div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="dashboard.php" class="nav-item">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path><circle cx="12" cy="16" r="5"></circle><path d="M12 11v5"></path><path d="M9 16h6"></path></svg></span>
            <span>Dashboard</span>
        </a>
        <a href="employees.php" class="nav-item">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span>
            <span>Employees</span>
        </a>
        <a href="attendance.php" class="nav-item">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12H4"></path><path d="M12 4v16"></path><path d="M4 12l4-4"></path><path d="M20 12l-4-4"></path><path d="M4 12l4 4"></path><path d="M20 12l-4 4"></path></svg></span>
            <span>Attendance</span>
        </a>

        <div class="nav-label">Payroll</div>
        <div class="nav-item" onclick="toggleDropdown()" style="cursor: pointer;">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="14" rx="2"></rect><path d="M2 10h20"></path><path d="M6 14h.01"></path><path d="M10 14h.01"></path><path d="M14 14h.01"></path><path d="M18 14h.01"></path></svg></span>
            <span>Payroll</span>
            <span class="nav-arrow" id="dropdownArrow"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"></polyline></svg></span>
        </div>
        <div class="nav-dropdown" id="payrollDropdown">
            <a href="salary.php" class="nav-item active">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg></span>
                <span>Salary</span>
            </a>
            <a href="bonus.php" class="nav-item">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path></svg></span>
                <span>Bonus</span>
            </a>
            <a href="incentives.php" class="nav-item">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L22 7L12 12L2 7L12 2Z"></path><path d="M2 17L12 22L22 17"></path><path d="M2 12L12 17L22 12"></path></svg></span>
                <span>Incentives</span>
            </a>
            <a href="cash_advance.php" class="nav-item">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v4M12 22v-4M4 12H2M6 12H4M20 12h-2M22 12h-2M19.07 4.93l-2.83 2.83M4.93 19.07l2.83-2.83M19.07 19.07l-2.83-2.83M4.93 4.93l2.83 2.83"></path></svg></span>
                <span>Cash Advance</span>
            </a>
            <a href="deduction.php" class="nav-item">
                <span class="nav-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 12H4"></path><circle cx="12" cy="12" r="10"></circle></svg></span>
                <span>Deductions</span>
            </a>
        </div>

        <div class="nav-label">Organization</div>
        <a href="position.php" class="nav-item">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg></span>
            <span>Positions</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-item">
            <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg></span>
            <span>Logout</span>
        </a>
    </div>
</aside>

<div class="main-content">
    <div class="page-header">
        <div>
            <h1>💰 Salary</h1>
            <div class="subtitle">8 AM - 5 PM workday with 1-hour lunch break (12 PM - 1 PM) excluded</div>
        </div>
        <div class="header-actions">
            <div class="print-dropdown">
                <button class="print-dropdown-btn" onclick="togglePrintDropdown(event)">
                    <i class="fas fa-print"></i> Print
                    <span class="arrow" id="printArrow">▼</span>
                </button>
                <div class="print-dropdown-menu" id="printDropdownMenu">
                    <button class="menu-item" onclick="exportDocument('pdf')">
                        <span class="icon pdf"><i class="fas fa-file-pdf"></i></span>
                        Print as PDF
                    </button>
                    <button class="menu-item" onclick="exportDocument('docx')">
                        <span class="icon docx"><i class="fas fa-file-word"></i></span>
                        Print as DOCX
                    </button>
                    <button class="menu-item" onclick="exportDocument('pdf', 'ca')">
                        <span class="icon pdf"><i class="fas fa-file-pdf"></i></span>
                        Print as PDF (CA only)
                    </button>
                    <div class="divider"></div>
                    <button class="menu-item" onclick="exportDocument('excel')">
                        <span class="icon excel"><i class="fas fa-file-excel"></i></span>
                        Export to Excel
                    </button>
                </div>
            </div>
            <span class="period-badge"><i class="fas fa-sliders-h"></i> <?php echo $preset_label ? $preset_label : 'Custom Range'; ?></span>
        </div>
    </div>

    <?php if (isset($error_message)): ?>
    <div class="message error" style="padding:0.75rem 1rem;border-radius:10px;background:rgba(255,107,107,0.08);color:#ff6b6b;border:1px solid rgba(255,107,107,0.1);margin-bottom:1rem;"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Preset Date Range Buttons -->
    <div class="glass-card" style="margin-bottom: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2); font-weight: 600;">QUICK PRESETS:</span>
            <div class="preset-buttons">
                <a href="?preset=5days&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '5days' ? 'active' : ''; ?>">5 Days</a>
                <a href="?preset=1week&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '1week' ? 'active' : ''; ?>">1 Week</a>
                <a href="?preset=15days&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '15days' ? 'active' : ''; ?>">15 Days</a>
                <a href="?preset=30days&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '30days' ? 'active' : ''; ?>">30 Days</a>
                <a href="?preset=3months&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '3months' ? 'active' : ''; ?>">3 Months</a>
                <a href="?preset=5months&custom_start=<?php echo $custom_start; ?>&custom_end=<?php echo $custom_end; ?>" class="preset-btn <?php echo $preset == '5months' ? 'active' : ''; ?>">5 Months</a>
            </div>
        </div>
    </div>

    <!-- Custom Date Range Filter -->
    <div class="glass-card" style="margin-bottom: 1.5rem;">
        <form method="GET" id="filterForm">
            <div class="custom-date-range">
                <label><i class="fas fa-calendar-alt"></i> From:</label>
                <input type="date" name="custom_start" value="<?php echo $custom_start; ?>" required>
                <label><i class="fas fa-calendar-alt"></i> To:</label>
                <input type="date" name="custom_end" value="<?php echo $custom_end; ?>" required>
                <button type="submit" class="btn btn-custom btn-sm"><i class="fas fa-sync-alt"></i> Calculate Salary</button>
                <?php if ($custom_start != date('Y-m-01') || $custom_end != date('Y-m-d')): ?>
                <a href="?custom_start=<?php echo date('Y-m-01'); ?>&custom_end=<?php echo date('Y-m-d'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Reset to Current Month</a>
                <?php endif; ?>
                <?php if ($preset): ?>
                <a href="?" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i> Clear Preset</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card accent-cyan">
            <div class="stat-label"><i class="fas fa-users stat-icon"></i> Employees</div>
            <div class="stat-value"><?php echo count($employees); ?></div>
        </div>
        <div class="stat-card accent-green">
            <div class="stat-label"><i class="fas fa-money-bill-wave stat-icon"></i> Total Gross</div>
            <div class="stat-value">₱<?php echo number_format($total_gross, 2); ?></div>
        </div>
        <div class="stat-card accent-pink">
            <div class="stat-label"><i class="fas fa-gift stat-icon"></i> Total Bonus</div>
            <div class="stat-value">₱<?php echo number_format($total_bonus_all, 2); ?></div>
        </div>
        <div class="stat-card accent-red">
            <div class="stat-label"><i class="fas fa-minus-circle stat-icon"></i> Total Deductions</div>
            <div class="stat-value">₱<?php echo number_format($total_deductions_all, 2); ?></div>
        </div>
        <div class="stat-card accent-purple">
            <div class="stat-label"><i class="fas fa-wallet stat-icon"></i> Total Net Pay</div>
            <div class="stat-value">₱<?php echo number_format($total_net, 2); ?></div>
        </div>
    </div>

    <!-- Salary Table -->
    <div class="glass-card">
        <div class="card-header">
            <div>
                <h3>Employee Salary Summary</h3>
                <span class="card-subtitle">
                    <?php echo count($all_employee_salaries); ?> employees
                    <?php if ($period_info): ?>
                    <br><span class="period-info">
                        <strong>Date Range:</strong> <?php echo date('M d, Y', strtotime($period_info['start_date'])); ?> - 
                        <?php echo date('M d, Y', strtotime($period_info['end_date'])); ?>
                        (<strong><?php echo $period_info['days']; ?> days</strong>)
                        <span class="highlight"><?php echo $preset_label ? $preset_label : 'Custom Range'; ?></span>
                        <br><span style="color:rgba(255,255,255,0.1);font-size:0.65rem;">
                            <i class="fas fa-utensils"></i> Lunch break (12:00 PM - 1:00 PM) excluded
                        </span>
                    </span>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <?php if (empty($all_employee_salaries)): ?>
        <div class="empty-state" style="text-align:center;padding:3rem;">
            <div style="font-size:2.5rem;color:rgba(255,255,255,0.05);margin-bottom:1rem;"><i class="fas fa-users"></i></div>
            <p style="color:rgba(255,255,255,0.3);">No employees found</p>
            <p style="font-size:0.75rem;margin-top:0.5rem;color:rgba(255,255,255,0.1);">Add employees to start tracking salary</p>
        </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Position</th>
                        <th class="text-center">Days</th>
                        <th class="text-center">Hours</th>
                        <th class="text-right">Daily Rate</th>
                        <th class="text-right">Gross</th>
                        <th class="text-right">Bonus</th>
                        <th class="text-right">Incentive</th>
                        <th class="text-right">SSS</th>
                        <th class="text-right">Pag-IBIG</th>
                        <th class="text-right">PhilHealth</th>
                        <th class="text-right">Total Ded.</th>
                        <th class="text-right">Net Pay</th>
                        <th class="text-center">%</th>
                        <th class="text-center">Payslip</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $grand_gross = 0; $grand_bonus = 0; $grand_incentive = 0;
                    $grand_sss = 0; $grand_pagibig = 0; $grand_philhealth = 0;
                    $grand_deductions = 0; $grand_net = 0;
                    
                    foreach ($all_employee_salaries as $emp_id => $salary): 
                        $grand_gross += $salary['gross_pay'];
                        $grand_bonus += $salary['bonus'];
                        $grand_incentive += $salary['incentive'];
                        $grand_sss += $salary['deductions']['SSS'];
                        $grand_pagibig += $salary['deductions']['Pag-IBIG'];
                        $grand_philhealth += $salary['deductions']['PhilHealth'];
                        $grand_deductions += $salary['deductions']['total'];
                        $grand_net += $salary['net_pay'];
                        
                        $has_income = $salary['gross_pay'] > 0;
                        $hours_status = '';
                        $is_full_month = ($salary['working_days'] >= 22);
                        if ($salary['working_days'] > 0 && $salary['total_hours'] > 0) {
                            $expected = $salary['working_days'] * 8;
                            $actual = $salary['total_hours'];
                            $percentage = ($actual / $expected) * 100;
                            if ($percentage < 80) $hours_status = 'badge-danger';
                            elseif ($percentage < 100) $hours_status = 'badge-warning';
                            else $hours_status = 'badge-success';
                        }
                        $ded = $salary['deductions'];
                    ?>
                    <tr class="<?php echo $has_income ? 'row-positive' : 'row-zero'; ?>">
                        <td>
                            <strong style="color:#ffffff;font-size:0.7rem;"><?php echo $salary['employee']['first_name'] . ' ' . $salary['employee']['last_name']; ?></strong>
                            <br><small class="text-muted" style="font-size:0.55rem;">ID: <?php echo $salary['employee']['employee_id']; ?></small>
                        </td>
                        <td><span style="color:rgba(255,255,255,0.3);font-size:0.65rem;"><?php echo $salary['employee']['position'] ?? 'N/A'; ?></span></td>
                        <td class="text-center" style="font-weight:600;color:#ffffff;font-size:0.7rem;">
                            <?php echo $salary['working_days']; ?>
                            <?php if ($is_full_month): ?>
                            <span class="badge-success" style="font-size:0.45rem;">FULL</span>
                            <?php endif; ?>
                            <?php if ($salary['half_days'] > 0): ?>
                            <br><small style="color:#fb923c;font-size:0.5rem;">(<?php echo $salary['half_days']; ?> half)</small>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span style="font-weight:600;color:#a78bfa;font-size:0.7rem;"><?php echo number_format($salary['total_hours'], 1); ?>h</span>
                            <?php if ($hours_status): ?>
                            <br><span class="<?php echo $hours_status; ?>" style="font-size:0.5rem;padding:1px 6px;border-radius:8px;">
                                <?php echo number_format(($salary['total_hours'] / ($salary['working_days'] * 8)) * 100, 0); ?>%
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-cyan" style="font-weight:600;font-size:0.7rem;">₱<?php echo number_format($salary['daily_rate'], 2); ?></td>
                        <td class="text-right" style="color:#ffffff;font-size:0.7rem;">₱<?php echo number_format($salary['gross_pay'], 2); ?></td>
                        <td class="text-right" style="color:#f472b6;font-size:0.7rem;">₱<?php echo number_format($salary['bonus'], 2); ?></td>
                        <td class="text-right" style="color:#34d399;font-size:0.7rem;">₱<?php echo number_format($salary['incentive'], 2); ?></td>
                        <td class="text-right" style="color:#fb923c;font-size:0.7rem;">₱<?php echo number_format($ded['SSS'], 2); ?></td>
                        <td class="text-right" style="color:#34d399;font-size:0.7rem;">₱<?php echo number_format($ded['Pag-IBIG'], 2); ?></td>
                        <td class="text-right" style="color:#a78bfa;font-size:0.7rem;">₱<?php echo number_format($ded['PhilHealth'], 2); ?></td>
                        <td class="text-right" style="color:#ff6b6b;font-weight:600;font-size:0.7rem;">₱<?php echo number_format($ded['total'], 2); ?></td>
                        <td class="text-right fw-bold" style="color:#00ffff;font-size:0.85rem;">₱<?php echo number_format($salary['net_pay'], 2); ?></td>
                        <td class="text-center" style="font-size:0.65rem;color:rgba(255,255,255,0.3);">
                            <?php echo number_format($salary['percentage_worked'], 0); ?>%
                        </td>
                        <td class="text-center">
                            <a href="?action=payslip&employee_id=<?php echo (int)$emp_id; ?>&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="Print payslip for <?php echo htmlspecialchars($salary['employee']['first_name'] . ' ' . $salary['employee']['last_name']); ?>">
                                <i class="fas fa-file-invoice-dollar"></i> Payslip
                            </a>
                            <a href="?action=payslip&mode=ca&employee_id=<?php echo (int)$emp_id; ?>&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="Payslip with Cash Advance only (no SSS / Pag-IBIG / PhilHealth)">
                                <i class="fas fa-hand-holding-usd"></i> CA only
                            </a>
                            <a href="?action=payslip&mode=none&employee_id=<?php echo (int)$emp_id; ?>&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="Payslip with NO deductions">
                                <i class="fas fa-ban"></i> No deduction
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot style="border-top:2px solid rgba(255,255,255,0.06);">
                    <tr>
                        <td colspan="5" class="text-right fw-bold" style="color:rgba(255,255,255,0.3);font-size:0.7rem;">GRAND TOTAL</td>
                        <td class="text-right fw-bold" style="color:#ffffff;font-size:0.75rem;">₱<?php echo number_format($grand_gross, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#f472b6;font-size:0.75rem;">₱<?php echo number_format($grand_bonus, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#34d399;font-size:0.75rem;">₱<?php echo number_format($grand_incentive, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#fb923c;font-size:0.75rem;">₱<?php echo number_format($grand_sss, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#34d399;font-size:0.75rem;">₱<?php echo number_format($grand_pagibig, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#a78bfa;font-size:0.75rem;">₱<?php echo number_format($grand_philhealth, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#ff6b6b;font-size:0.75rem;">₱<?php echo number_format($grand_deductions, 2); ?></td>
                        <td class="text-right fw-bold" style="color:#00ffff;font-size:0.95rem;">₱<?php echo number_format($grand_net, 2); ?></td>
                        <td></td>
                        <td class="text-center">
                            <a href="?action=payslip&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="One payslip per employee">
                                <i class="fas fa-print"></i> All
                            </a>
                            <a href="?action=payslip&mode=ca&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="All payslips, Cash Advance only">
                                <i class="fas fa-print"></i> All CA only
                            </a>
                            <a href="?action=payslip&mode=none&custom_start=<?php echo urlencode($custom_start); ?>&custom_end=<?php echo urlencode($custom_end); ?>&auto_print=1" target="_blank" class="btn btn-secondary btn-sm" title="All payslips, no deductions">
                                <i class="fas fa-print"></i> All no deduction
                            </a>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div style="margin-top:0.75rem;padding-top:0.75rem;border-top:1px solid rgba(255,255,255,0.02);">
            <div style="display:flex;gap:1rem;flex-wrap:wrap;font-size:0.6rem;color:rgba(255,255,255,0.15);">
                <span><span class="badge-sss">SSS</span> Social Security System</span>
                <span><span class="badge-pagibig">Pag-IBIG</span> Home Development Mutual Fund</span>
                <span><span class="badge-philhealth">PhilHealth</span> National Health Insurance</span>
                <span><i class="fas fa-calculator" style="color:rgba(255,255,255,0.1);"></i> Deductions prorated by working days in period</span>
                <span><i class="fas fa-utensils" style="color:rgba(255,255,255,0.1);"></i> Lunch break excluded</span>
                <span><i class="fas fa-clock" style="color:rgba(255,255,255,0.1);"></i> Workday: 8 AM - 5 PM</span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
    window.SALARY_PAGE = {
        customStart: '<?php echo $custom_start; ?>',
        customEnd: '<?php echo $custom_end; ?>'
    };
</script>
<script src="../../assets/js/salary.js"></script>
<script src="../../assets/js/mobile.js"></script>

</body>
</html>