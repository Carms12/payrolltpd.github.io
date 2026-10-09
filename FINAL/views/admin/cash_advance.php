<?php
require_once __DIR__ . '/../../controllers/CashAdvanceController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Payroll Pro · Cash Advance Management</title>
    
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com; connect-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/cash-advance.css">
    <link rel="stylesheet" href="../../assets/css/mobile.css">
</head>
<body>

    <!-- Background -->
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

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(0, 255, 255, 0.08); border: 1px solid rgba(0, 255, 255, 0.06);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#00ffff" stroke-width="2">
                        <rect x="4" y="2" width="16" height="20" rx="2"></rect>
                        <line x1="9" y1="22" x2="15" y2="22"></line>
                        <line x1="9" y1="6" x2="9" y2="6"></line>
                        <line x1="15" y1="6" x2="15" y2="6"></line>
                        <line x1="9" y1="10" x2="9" y2="10"></line>
                        <line x1="15" y1="10" x2="15" y2="10"></line>
                        <line x1="9" y1="14" x2="9" y2="14"></line>
                        <line x1="15" y1="14" x2="15" y2="14"></line>
                        <line x1="9" y1="18" x2="9" y2="18"></line>
                        <line x1="15" y1="18" x2="15" y2="18"></line>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-white tracking-tight">TPD<span style="color:#00ffff;">Solutions</span></span>
                    <span class="block text-[9px] text-white/15 font-medium tracking-wider"></span>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="dashboard.php" class="nav-item">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12v-2a5 5 0 0 0-5-5H8a5 5 0 0 0-5 5v2"></path>
                        <circle cx="12" cy="16" r="5"></circle>
                        <path d="M12 11v5"></path>
                        <path d="M9 16h6"></path>
                    </svg>
                </span>
                <span>Dashboard</span>
            </a>
            <a href="employees.php" class="nav-item">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span>Employees</span>
                <span class="nav-badge"><?php echo count($employees); ?></span>
            </a>
            <a href="attendance.php" class="nav-item">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 12H4"></path>
                        <path d="M12 4v16"></path>
                        <path d="M4 12l4-4"></path>
                        <path d="M20 12l-4-4"></path>
                        <path d="M4 12l4 4"></path>
                        <path d="M20 12l-4 4"></path>
                    </svg>
                </span>
                <span>Attendance</span>
            </a>

            <div class="nav-label">Payroll</div>
            <div class="nav-item" onclick="toggleDropdown()" style="cursor: pointer;">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="6" width="20" height="14" rx="2"></rect>
                        <path d="M2 10h20"></path>
                        <path d="M6 14h.01"></path>
                        <path d="M10 14h.01"></path>
                        <path d="M14 14h.01"></path>
                        <path d="M18 14h.01"></path>
                    </svg>
                </span>
                <span>Payroll</span>
                <span class="nav-arrow" id="dropdownArrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </span>
            </div>
            <div class="nav-dropdown" id="payrollDropdown">
                <a href="salary.php" class="nav-item">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M12 6v6l4 2"></path>
                        </svg>
                    </span>
                    <span>Salary</span>
                </a>
                <a href="bonus.php" class="nav-item">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path>
                        </svg>
                    </span>
                    <span>Bonus</span>
                </a>
                <a href="incentives.php" class="nav-item">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L22 7L12 12L2 7L12 2Z"></path>
                            <path d="M2 17L12 22L22 17"></path>
                            <path d="M2 12L12 17L22 12"></path>
                        </svg>
                    </span>
                    <span>Incentives</span>
                </a>
                <a href="cash_advance.php" class="nav-item active">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v4M12 22v-4M4 12H2M6 12H4M20 12h-2M22 12h-2M19.07 4.93l-2.83 2.83M4.93 19.07l2.83-2.83M19.07 19.07l-2.83-2.83M4.93 4.93l2.83 2.83"></path>
                        </svg>
                    </span>
                    <span>Cash Advance</span>
                    <span class="nav-badge"><?php echo $pendingCount; ?></span>
                </a>
                <a href="deduction.php" class="nav-item">
    <span class="nav-icon">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20 12H4"></path>
            <circle cx="12" cy="12" r="10"></circle>
        </svg>
    </span>
    <span>Deductions</span>
</a>
            </div>
            

            <div class="nav-label">Organization</div>
            <a href="position.php" class="nav-item">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </span>
                <span>Positions</span>
            </a>

        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" class="nav-item">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </span>
                <span>Logout</span>
            </a>
        </div>
    </aside>

    <!-- ===== Main Content ===== -->
    <main class="main-content">
        <header class="page-header">
            <div>
                <h1>Cash Advance Management</h1>
                <div class="subtitle">Manage employee cash advances - deductions are automatically processed on salary</div>
            </div>
            <div class="header-actions">
                <?php if ($messageType === 'error' && strpos($message, 'Too many attempts') !== false): ?>
                <a href="?reset_rate_limit=1" class="reset-btn">
                    <i class="fas fa-sync"></i> Reset Rate Limit
                </a>
                <?php endif; ?>
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i> New Cash Advance
                </button>
            </div>
        </header>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo escapeOutput($message); ?>
        </div>
        <?php endif; ?>

        <?php if (!$tableExists): ?>
        <div class="glass-card">
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-database"></i></div>
                <p>Table "cash_advances" doesn't exist in the database.</p>
                <p style="font-size:0.75rem; margin-top:0.5rem; color:rgba(255,255,255,0.1);">Run the cash_advances.sql file in phpMyAdmin or MySQL</p>
            </div>
        </div>
        <?php else: ?>

        <section class="stats-grid">
            <div class="stat-card accent-cyan">
                <div class="stat-label"><i class="fas fa-hand-holding-usd stat-icon"></i> Total Cash Advances</div>
                <div class="stat-value">PHP <?php echo number_format($totalAmount, 2); ?></div>
            </div>
            <div class="stat-card accent-yellow">
                <div class="stat-label"><i class="fas fa-clock stat-icon"></i> Pending</div>
                <div class="stat-value"><?php echo $pendingCount; ?></div>
            </div>
            <div class="stat-card accent-green">
                <div class="stat-label"><i class="fas fa-check-circle stat-icon"></i> Approved</div>
                <div class="stat-value"><?php echo $approvedCount; ?></div>
            </div>
            <div class="stat-card accent-purple">
                <div class="stat-label"><i class="fas fa-money-bill-wave stat-icon"></i> Remaining Balance</div>
                <div class="stat-value">PHP <?php echo number_format($totalRemaining, 2); ?></div>
            </div>
            <div class="stat-card accent-pink">
                <div class="stat-label"><i class="fas fa-check-double stat-icon"></i> Fully Paid</div>
                <div class="stat-value"><?php echo $paidCount; ?></div>
            </div>
        </section>

        <div class="glass-card" style="margin-bottom: 1.5rem;">
            <form method="GET" class="search-wrapper" role="search">
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <?php foreach ($statusOptions as $status): ?>
                    <option value="<?php echo $status; ?>" <?php echo $statusFilter === $status ? 'selected' : ''; ?>>
                        <?php echo $status; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="employee" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    <?php foreach ($employees as $emp): ?>
                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo $employeeFilter == $emp['employee_id'] ? 'selected' : ''; ?>>
                        <?php echo escapeOutput($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_code'] . ')'); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <?php if ($statusFilter || $employeeFilter): ?>
                <a href="cash_advance.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Deduction Info Box -->
        <div class="glass-card" style="margin-bottom: 1.5rem; border-color: rgba(0, 255, 255, 0.08);">
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(0, 255, 255, 0.06); border: 1px solid rgba(0, 255, 255, 0.06); display: flex; align-items: center; justify-content: center; color: #00ffff;">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.2);">Automatic Deduction</div>
                        <div style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.6);">Cash advances are automatically deducted from employee salaries when processed</div>
                    </div>
                </div>
                <div style="display: flex; gap: 1.5rem; margin-left: auto; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 0.6rem; color: rgba(255, 255, 255, 0.15); text-transform: uppercase;">Total Outstanding</div>
                        <div style="font-size: 1.1rem; font-weight: 600; color: #00ffff;">PHP <?php echo number_format($totalRemaining, 2); ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.6rem; color: rgba(255, 255, 255, 0.15); text-transform: uppercase;">Pending Approval</div>
                        <div style="font-size: 1.1rem; font-weight: 600; color: #fbbf24;"><?php echo $pendingCount; ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <div>
                    <h3>All Cash Advances</h3>
                    <span class="card-subtitle"><?php echo count($cashAdvances); ?> records found</span>
                </div>
            </div>

            <?php if (empty($cashAdvances)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-hand-holding-usd"></i></div>
                <p>No cash advance records found</p>
            </div>
            <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Employee</th>
                            <th>Amount</th>
                            <th>Balance</th>
                            <th>Date Requested</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th style="text-align: right; width: 90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cashAdvances as $index => $ca): ?>
                        <tr>
                            <td class="text-muted"><?php echo $index + 1; ?></td>
                            <td class="text-white">
                                <?php echo escapeOutput($ca['first_name'] . ' ' . $ca['last_name']); ?>
                                <div style="font-size:0.7rem; color:rgba(255,255,255,0.2);"><?php echo escapeOutput($ca['employee_code']); ?></div>
                            </td>
                            <td class="text-rate">
                                <span class="currency">PHP</span>
                                <span class="amount"><?php echo number_format($ca['amount'], 2); ?></span>
                            </td>
                            <td class="text-rate">
                                <span class="currency">PHP</span>
                                <span class="amount"><?php echo number_format($ca['remaining_balance'], 2); ?></span>
                            </td>
                            <td style="font-size:0.8rem; color:rgba(255,255,255,0.3);">
                                <?php echo date('M d, Y', strtotime($ca['date_requested'])); ?>
                            </td>
                            <td style="font-size:0.8rem; color:rgba(255,255,255,0.5); max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo escapeOutput($ca['purpose']); ?>">
                                <?php echo escapeOutput($ca['purpose'] ?: 'N/A'); ?>
                            </td>
                            <td>
                                <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $ca['status'])); ?>">
                                    <?php echo escapeOutput($ca['status']); ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="actions-cell">
                                    <button onclick="openEditModal(<?php echo (int)$ca['cash_advance_id']; ?>)" 
                                            class="btn-icon btn-edit" 
                                            aria-label="Edit cash advance"
                                            title="Edit Cash Advance">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo (int)$ca['cash_advance_id']; ?>, '<?php echo escapeOutput(addslashes($ca['first_name'] . ' ' . $ca['last_name'])); ?> - PHP <?php echo number_format($ca['amount'], 2); ?>')" 
                                            class="btn-icon btn-delete" 
                                            aria-label="Delete cash advance"
                                            title="Delete Cash Advance">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <?php endif; ?>
    </main>

    <!-- ===== Modals ===== -->
    <div class="modal-overlay" id="cashAdvanceModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">New Cash Advance</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="cashAdvanceForm" onsubmit="return preventDuplicateSubmit()" autocomplete="off">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="cash_advance_id" id="cashAdvanceId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrfToken); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employeeSelect">Employee</label>
                        <select id="employeeSelect" name="employee_id" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo $emp['employee_id']; ?>">
                                <?php echo escapeOutput($emp['first_name'] . ' ' . $emp['last_name'] . ' (' . $emp['employee_code'] . ') - ' . $emp['position']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="amountInput">Amount</label>
                            <input type="number" step="0.01" min="0.01" max="999999.99" 
                                   id="amountInput" name="amount" 
                                   placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label for="dateRequested">Date Requested</label>
                            <input type="date" id="dateRequested" name="date_requested" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="statusSelect">Status</label>
                        <select id="statusSelect" name="status" required>
                            <?php foreach ($statusOptions as $status): ?>
                            <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="purposeInput">Purpose</label>
                        <input type="text" id="purposeInput" name="purpose" 
                               placeholder="Purpose of cash advance" maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="remarksInput">Remarks</label>
                        <textarea id="remarksInput" name="remarks" placeholder="Additional remarks..." rows="2"></textarea>
                    </div>
                    
                    <!-- Info about automatic deduction -->
                    <div style="background: rgba(0, 255, 255, 0.03); border: 1px solid rgba(0, 255, 255, 0.06); border-radius: 10px; padding: 0.75rem 1rem; margin-top: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; color: rgba(255, 255, 255, 0.3); font-size: 0.7rem;">
                            <i class="fas fa-info-circle" style="color: #00ffff;"></i>
                            <span>This cash advance will be automatically deducted from the employee's salary when processed.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus"></i> Create Cash Advance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay modal-delete" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-trash mr-2"></i> Delete Cash Advance</h2>
                <button class="modal-close" onclick="closeDeleteModal()" aria-label="Close delete modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="cash_advance_id" id="deleteCashAdvanceId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrfToken); ?>">
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="delete-warning">Are you sure you want to delete this cash advance?</div>
                    <div class="delete-item-name" id="deleteItemName">Cash Advance</div>
                    <div class="delete-subtext">This action cannot be undone.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger-solid">
                        <i class="fas fa-trash"></i> Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.CASH_ADVANCE_DATA = {
            cashAdvances: <?php echo json_encode($cashAdvances, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
        };
    </script>
    <script src="../../assets/js/cash-advance.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html> 