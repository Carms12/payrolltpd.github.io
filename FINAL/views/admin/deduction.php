<?php
require_once __DIR__ . '/../../controllers/DeductionController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Deductions · Payroll Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/deduction.css">
    <link rel="stylesheet" href="../../assets/css/mobile.css">
</head>
<body>

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

<!-- Sidebar -->
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
            <a href="salary.php" class="nav-item">
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
            <a href="deduction.php" class="nav-item active">
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

<!-- Main Content -->
<div class="main-content">
    <div class="page-header">
        <div>
            <h1>📋 Monthly Deductions</h1>
            <div class="subtitle">Auto-generated every month — SSS, Pag-IBIG, PhilHealth</div>
        </div>
        <div class="header-actions">
            <button class="btn btn-settings" onclick="openSettingsModal()">
                <i class="fas fa-cog"></i> Defaults
            </button>
            <button class="btn btn-sss" onclick="openAddModal('SSS')">
                <i class="fas fa-plus"></i> SSS
            </button>
            <button class="btn btn-pagibig" onclick="openAddModal('Pag-IBIG')">
                <i class="fas fa-plus"></i> Pag-IBIG
            </button>
            <button class="btn btn-philhealth" onclick="openAddModal('PhilHealth')">
                <i class="fas fa-plus"></i> PhilHealth
            </button>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?php echo $message_type; ?>">
        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'info' ? 'info-circle' : 'exclamation-circle'); ?>"></i>
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card accent-cyan">
            <div class="stat-label"><i class="fas fa-users stat-icon"></i> Employees</div>
            <div class="stat-value"><?php echo count($grouped); ?></div>
        </div>
        <div class="stat-card accent-orange">
            <div class="stat-label"><i class="fas fa-shield-halved stat-icon"></i> Total SSS</div>
            <div class="stat-value">₱<?php echo number_format($total_sss, 2); ?></div>
        </div>
        <div class="stat-card accent-green">
            <div class="stat-label"><i class="fas fa-piggy-bank stat-icon"></i> Total Pag-IBIG</div>
            <div class="stat-value">₱<?php echo number_format($total_pagibig, 2); ?></div>
        </div>
        <div class="stat-card accent-purple">
            <div class="stat-label"><i class="fas fa-heart-pulse stat-icon"></i> Total PhilHealth</div>
            <div class="stat-value">₱<?php echo number_format($total_philhealth, 2); ?></div>
        </div>
    </div>

    <!-- Filter -->
    <div class="glass-card" style="margin-bottom: 1.5rem;">
        <form method="GET">
            <div style="display:flex;gap:0.75rem;align-items:end;flex-wrap:wrap;">
                <div class="form-group" style="margin:0;flex:1;min-width:200px;">
                    <label>Employee</label>
                    <select name="filter_employee" class="form-select">
                        <option value="">All Employees</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?php echo htmlspecialchars($e['employee_id']); ?>" <?php echo $filter_employee === $e['employee_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:150px;">
                    <label>Type</label>
                    <select name="filter_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="SSS" <?php echo $filter_type === 'SSS' ? 'selected' : ''; ?>>SSS</option>
                        <option value="Pag-IBIG" <?php echo $filter_type === 'Pag-IBIG' ? 'selected' : ''; ?>>Pag-IBIG</option>
                        <option value="PhilHealth" <?php echo $filter_type === 'PhilHealth' ? 'selected' : ''; ?>>PhilHealth</option>
                    </select>
                </div>
                <div class="form-group" style="margin:0;flex:1;min-width:180px;">
                    <label>Month</label>
                    <select name="filter_month" class="form-select">
                        <option value="">All Months</option>
                        <?php 
                        $month_list = $months_raw;
                        if (!in_array(date('Y-m'), $month_list)) array_unshift($month_list, date('Y-m'));
                        foreach ($month_list as $m): 
                        ?>
                        <option value="<?php echo htmlspecialchars($m); ?>" <?php echo $filter_month === $m ? 'selected' : ''; ?>>
                            <?php echo date('F Y', strtotime($m . '-01')); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
                <a href="deduction.php?filter_month=<?php echo date('Y-m'); ?>" class="btn btn-secondary btn-sm"><i class="fas fa-calendar-day"></i> This Month</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card">
        <div class="card-header">
            <h3>
                <?php if ($filter_type): ?>
                    <?php echo htmlspecialchars($filter_type); ?> Deductions
                <?php else: ?>
                    Employee Deductions
                <?php endif; ?>
            </h3>
            <span class="badge" style="background:rgba(255,255,255,0.04);color:rgba(255,255,255,0.3);">
                <?php echo count($grouped); ?> employee<?php echo count($grouped) !== 1 ? 's' : ''; ?>
            </span>
        </div>

        <?php if (empty($grouped)): ?>
        <div style="text-align:center;padding:3rem;">
            <div style="font-size:2.5rem;color:rgba(255,255,255,0.05);margin-bottom:1rem;"><i class="fas fa-file-invoice"></i></div>
            <p style="color:rgba(255,255,255,0.3);">No deduction records for this month</p>
            <p style="font-size:0.75rem;margin-top:0.5rem;color:rgba(255,255,255,0.1);">Set employee defaults first, then reload to auto-generate</p>
        </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th class="text-center">Month</th>
                        <th class="text-right">SSS</th>
                        <th class="text-right">Pag-IBIG</th>
                        <th class="text-right">PhilHealth</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grouped as $g): 
                        $total = $g['SSS'] + $g['Pag-IBIG'] + $g['PhilHealth'];
                        $initials = strtoupper(substr($g['first_name'] ?? '?', 0, 1) . substr($g['last_name'] ?? '?', 0, 1));
                    ?>
                    <tr>
                        <td>
                            <div class="emp-cell">
                                <div class="emp-avatar"><?php echo $initials; ?></div>
                                <div>
                                    <div class="emp-name"><?php echo htmlspecialchars(($g['first_name'] ?? '') . ' ' . ($g['last_name'] ?? '')); ?></div>
                                    <div class="emp-position"><?php echo htmlspecialchars($g['position'] ?? 'N/A'); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="text-center"><span class="month-chip"><?php echo date('M Y', strtotime($g['deduction_month'] . '-01')); ?></span></td>
                        <td class="text-right amt-sss">
                            <?php if ($g['SSS'] > 0): ?>
                                ₱<?php echo number_format($g['SSS'], 2); ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right amt-pagibig">
                            <?php if ($g['Pag-IBIG'] > 0): ?>
                                ₱<?php echo number_format($g['Pag-IBIG'], 2); ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right amt-philhealth">
                            <?php if ($g['PhilHealth'] > 0): ?>
                                ₱<?php echo number_format($g['PhilHealth'], 2); ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right amt-total">₱<?php echo number_format($total, 2); ?></td>
                        <td class="text-center">
                            <div style="display:flex;gap:0.35rem;justify-content:center;">
                                <button class="btn btn-secondary btn-icon"
                                        onclick='openEditGroup(<?php echo json_encode([
                                            "employee" => ($g["first_name"] ?? "") . " " . ($g["last_name"] ?? ""),
                                            "month" => $g["deduction_month"],
                                            "sss" => ["id" => $g["ids"]["SSS"], "amount" => $g["SSS"]],
                                            "pagibig" => ["id" => $g["ids"]["Pag-IBIG"], "amount" => $g["Pag-IBIG"]],
                                            "philhealth" => ["id" => $g["ids"]["PhilHealth"], "amount" => $g["PhilHealth"]]
                                        ]); ?>)'
                                        title="Edit amounts">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-right" style="color:rgba(255,255,255,0.3);font-size:0.7rem;">GRAND TOTAL</td>
                        <td class="text-right amt-sss">₱<?php echo number_format($total_sss, 2); ?></td>
                        <td class="text-right amt-pagibig">₱<?php echo number_format($total_pagibig, 2); ?></td>
                        <td class="text-right amt-philhealth">₱<?php echo number_format($total_philhealth, 2); ?></td>
                        <td class="text-right amt-total" style="font-size:0.85rem;">₱<?php echo number_format($grand_total, 2); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ADD MODAL -->
<div class="modal-overlay" id="addModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="addModalTitle"><i class="fas fa-plus-circle" style="color:#00ffff;"></i> Add Deduction</h3>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="deduction_type" id="add_type" value="">
            <div class="modal-body">
                <div class="form-group">
                    <label>Employee <span class="required">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">— Select Employee —</option>
                        <?php foreach ($employees as $e): ?>
                        <option value="<?php echo htmlspecialchars($e['employee_id']); ?>">
                            <?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name'] . ' — ' . $e['position']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Deduction Type</label>
                    <input type="text" id="add_type_display" class="form-input" readonly style="opacity:0.7;cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Month <span class="required">*</span></label>
                    <input type="month" name="deduction_month" class="form-input" value="<?php echo date('Y-m'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Amount (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-input" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label>Notes <span class="text-muted" style="font-size:0.6rem;text-transform:none;">(optional)</span></label>
                    <textarea name="notes" class="form-textarea" placeholder="Optional notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" name="add_deduction" class="btn btn-primary"><i class="fas fa-save"></i> Save Deduction</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT MODAL (single type) -->
<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-pen" style="color:#a78bfa;"></i> Edit Deduction</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="deduction_type" id="edit_type_hidden">
            <div class="modal-body">
                <div class="form-group">
                    <label>Deduction Type</label>
                    <input type="text" id="edit_type_display" class="form-input" readonly style="opacity:0.7;cursor:not-allowed;">
                </div>
                <div class="form-group">
                    <label>Month <span class="required">*</span></label>
                    <input type="month" name="deduction_month" id="edit_month" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Amount (₱) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" id="edit_amount" class="form-input" required>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" id="edit_notes" class="form-textarea"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" name="edit_deduction" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT GROUP MODAL (all 3 types at once) -->
<div class="modal-overlay" id="editGroupModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="editGroupTitle"><i class="fas fa-pen" style="color:#00ffff;"></i> Edit Deductions</h3>
            <button class="modal-close" onclick="closeModal('editGroupModal')">&times;</button>
        </div>
        <form method="POST" id="editGroupForm">
            <div class="modal-body">
                <p style="font-size:0.75rem;color:rgba(255,255,255,0.4);margin-bottom:1rem;" id="editGroupSubtitle">
                    Update the three deduction amounts for this employee.
                </p>
                <div id="editGroupFields"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editGroupModal')">Cancel</button>
                <button type="submit" name="save_group" class="btn btn-primary"><i class="fas fa-save"></i> Save All</button>
            </div>
        </form>
    </div>
</div>

<!-- SETTINGS MODAL (defaults per employee) -->
<div class="modal-overlay" id="settingsModal">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3><i class="fas fa-cog" style="color:#00ffff;"></i> Default Monthly Deductions</h3>
            <button class="modal-close" onclick="closeModal('settingsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.4);margin-bottom:1rem;">
                Set default monthly amounts per employee. These will auto-generate records for the current month on every page load.
                Leave 0 to skip.
            </p>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th class="text-center">SSS</th>
                            <th class="text-center">Pag-IBIG</th>
                            <th class="text-center">PhilHealth</th>
                            <th class="text-center">Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $e): 
                            $sid = 'settings_' . preg_replace('/[^a-zA-Z0-9]/', '_', $e['employee_id']);
                            $set = $settings_all[$e['employee_id']] ?? ['sss' => 0, 'pagibig' => 0, 'philhealth' => 0];
                        ?>
                        <tr>
                            <td>
                                <div class="emp-cell">
                                    <div class="emp-avatar"><?php echo strtoupper(substr($e['first_name'], 0, 1) . substr($e['last_name'], 0, 1)); ?></div>
                                    <div>
                                        <div class="emp-name"><?php echo htmlspecialchars($e['first_name'] . ' ' . $e['last_name']); ?></div>
                                        <div class="emp-position"><?php echo htmlspecialchars($e['position']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <form method="POST" id="<?php echo $sid; ?>" style="display:contents;">
                                <input type="hidden" name="settings_employee_id" value="<?php echo htmlspecialchars($e['employee_id']); ?>">
                                <td class="text-center">
                                    <input type="number" step="0.01" min="0" name="settings_sss" class="form-input" style="width:90px;text-align:right;padding:0.35rem 0.5rem;"
                                           value="<?php echo number_format((float)$set['sss'], 2, '.', ''); ?>">
                                </td>
                                <td class="text-center">
                                    <input type="number" step="0.01" min="0" name="settings_pagibig" class="form-input" style="width:90px;text-align:right;padding:0.35rem 0.5rem;"
                                           value="<?php echo number_format((float)$set['pagibig'], 2, '.', ''); ?>">
                                </td>
                                <td class="text-center">
                                    <input type="number" step="0.01" min="0" name="settings_philhealth" class="form-input" style="width:90px;text-align:right;padding:0.35rem 0.5rem;"
                                           value="<?php echo number_format((float)$set['philhealth'], 2, '.', ''); ?>">
                                </td>
                                <td class="text-center">
                                    <button type="submit" name="save_settings" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeModal('settingsModal')">Close</button>
        </div>
    </div>
</div>

<script src="../../assets/js/deduction.js"></script>
<script src="../../assets/js/mobile.js"></script>

</body>
</html>