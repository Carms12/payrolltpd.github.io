<?php
require_once __DIR__ . '/../../controllers/BonusController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payroll Pro · Bonus Management</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/bonus.css">
    <link rel="stylesheet" href="../../assets/css/mobile.css">
</head>
<body>

    <!-- ================================================================ -->
    <!-- BACKGROUND EFFECTS -->
    <!-- ================================================================ -->
    
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <!-- ================================================================ -->
    <!-- MOBILE OVERLAY & TOGGLE -->
    <!-- ================================================================ -->
    
    <div id="overlay" class="overlay" onclick="toggleSidebar()"></div>
    <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2">
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
    </button>

    <!-- ================================================================ -->
    <!-- SIDEBAR -->
    <!-- ================================================================ -->
    
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(0,255,255,0.08); border: 1px solid rgba(0,255,255,0.06);">
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
                    <span class="text-sm font-bold text-white tracking-tight">
                        TPD<span style="color:#00ffff;">Solutions</span>
                    </span>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <!-- Main Section -->
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

            <!-- Payroll Section -->
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
                
                <a href="bonus.php" class="nav-item active">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"></path>
                        </svg>
                    </span>
                    <span>Bonus</span>
                    <span class="nav-badge"><?php echo $bonusCount; ?></span>
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
                <a href="cash_advance.php" class="nav-item">
                    <span class="nav-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v4M12 22v-4M4 12H2M6 12H4M20 12h-2M22 12h-2M19.07 4.93l-2.83 2.83M4.93 19.07l2.83-2.83M19.07 19.07l-2.83-2.83M4.93 4.93l2.83 2.83"></path>
                        </svg>
                    </span>
                    <span>Cash Advance</span>
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

            <!-- Organization Section -->
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

    <!-- ================================================================ -->
    <!-- MAIN CONTENT -->
    <!-- ================================================================ -->
    
    <div class="main-content">
        
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>⭐ Bonus Management</h1>
                <div class="subtitle">Manage employee bonuses and rewards across your organization</div>
            </div>
            <div class="header-actions">
                <button type="button" onclick="openBonusModal()" class="btn btn-primary" id="addBonusBtn">
                    <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
                    Add Bonus
                </button>
            </div>
        </div>

        <!-- Message Display -->
        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <!-- Table Missing Message -->
        <?php if (!$tableExists): ?>
        <div class="glass-card">
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-database"></i></div>
                <p>Table "bonuses" doesn't exist in the database.</p>
                <p style="font-size:0.75rem; margin-top:0.5rem; color:rgba(255,255,255,0.1);">
                    Run the SQL script in phpMyAdmin or MySQL to create the table
                </p>
            </div>
        </div>
        <?php else: ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card accent-cyan">
                <div class="stat-label">
                    <i class="fas fa-star stat-icon"></i> Total Bonuses
                </div>
                <div class="stat-value"><?php echo $bonusCount; ?></div>
            </div>
            
            <div class="stat-card accent-green">
                <div class="stat-label">
                    <i class="fas fa-money-bill-wave stat-icon"></i> Total Amount
                </div>
                <div class="stat-value">₱<?php echo number_format($totalAmount, 2); ?></div>
            </div>
            
            <div class="stat-card accent-purple">
                <div class="stat-label">
                    <i class="fas fa-chart-line stat-icon"></i> Average Bonus
                </div>
                <div class="stat-value">₱<?php echo number_format($avgAmount, 2); ?></div>
            </div>
            
            <div class="stat-card accent-pink">
                <div class="stat-label">
                    <i class="fas fa-trophy stat-icon"></i> Highest Bonus
                </div>
                <div class="stat-value">₱<?php echo number_format($maxAmount, 2); ?></div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="glass-card">
            <form method="GET" class="search-wrapper">
                <div class="search-input">
                    <i class="fas fa-search search-icon"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search bonuses by name, reason or employee..." 
                        value="<?php echo htmlspecialchars($search); ?>"
                    >
                </div>
                
                <select name="employee_id" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Employees</option>
                    <?php foreach ($employees as $emp): ?>
                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($filterEmployeeId == $emp['employee_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($emp['last_name'] . ', ' . $emp['first_name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
                
                <?php if ($search || $filterEmployeeId): ?>
                <a href="bonus.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Bonuses Table -->
        <div class="glass-card">
            <div class="card-header">
                <div>
                    <h3>📋 All Bonuses</h3>
                    <span class="card-subtitle"><?php echo count($bonuses); ?> bonuses found</span>
                </div>
            </div>

            <?php if (empty($bonuses)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-star"></i></div>
                <p>No bonuses found</p>
            </div>
            <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Employee</th>
                            <th>Bonus Name</th>
                            <th>Reason</th>
                            <th style="text-align: right;">Amount</th>
                            <th>Created At</th>
                            <th style="text-align: right; width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bonuses as $index => $bonus): ?>
                        <tr>
                            <td class="text-muted"><?php echo $index + 1; ?></td>
                            
                            <td class="employee-cell">
                                <div class="employee-info">
                                    <span class="employee-avatar">
                                        <?php echo strtoupper(substr($bonus['first_name'], 0, 1) . substr($bonus['last_name'], 0, 1)); ?>
                                    </span>
                                    <div>
                                        <div><?php echo htmlspecialchars($bonus['first_name'] . ' ' . $bonus['last_name']); ?></div>
                                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.25);">
                                            <?php echo htmlspecialchars($bonus['employee_code']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="text-white"><?php echo htmlspecialchars($bonus['bonus_name']); ?></td>
                            <td class="reason-cell"><?php echo htmlspecialchars($bonus['reason']); ?></td>
                            
                            <td style="text-align: right; font-weight: 600; color: #00ffff;">
                                ₱<?php echo number_format($bonus['amount'], 2); ?>
                            </td>
                            
                            <td class="text-muted"><?php echo date('M d, Y', strtotime($bonus['created_at'])); ?></td>
                            
                            <td style="text-align: right;">
                                <div class="actions-cell">
                                    <button 
                                        type="button" 
                                        onclick="editBonus(<?php echo $bonus['bonus_id']; ?>)" 
                                        class="btn btn-warning btn-sm" 
                                        style="border: none;"
                                        title="Edit Bonus"
                                    >
                                        <i class="fas fa-edit" style="font-size: 0.75rem; color: #ffd93d;"></i>
                                    </button>
                                    
                                    <button 
                                        type="button" 
                                        onclick="deleteBonus(<?php echo $bonus['bonus_id']; ?>, '<?php echo htmlspecialchars(addslashes($bonus['bonus_name'])); ?>', '<?php echo htmlspecialchars(addslashes($bonus['first_name'] . ' ' . $bonus['last_name'])); ?>')" 
                                        class="btn btn-danger btn-sm" 
                                        style="border: none;"
                                        title="Delete Bonus"
                                    >
                                        <i class="fas fa-trash-alt" style="font-size: 0.75rem; color: #ff6b6b;"></i>
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
    </div>

    <!-- ================================================================ -->
    <!-- MODALS -->
    <!-- ================================================================ -->

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="bonusModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Bonus</h2>
                <button type="button" class="modal-close" onclick="closeBonusModal()">
                    <i class="fas fa-times" style="font-size: 1.2rem;"></i>
                </button>
            </div>
            
            <form method="POST" id="bonusForm" onsubmit="return handleSubmit()">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="bonus_id" id="bonusId" value="">
                <input type="hidden" name="submission_token" id="submissionToken" value="<?php echo $submissionToken; ?>">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employeeSelect">Assign to Employee <span class="required">*</span></label>
                        <select id="employeeSelect" name="employee_id" required>
                            <option value="">— Select Employee —</option>
                            <?php foreach ($employees as $emp): ?>
                            <option value="<?php echo $emp['employee_id']; ?>">
                                <?php echo htmlspecialchars($emp['last_name'] . ', ' . $emp['first_name'] . ' (' . $emp['employee_code'] . ')'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="bonusName">Bonus Name <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="bonusName" 
                            name="bonus_name" 
                            placeholder="e.g. Year-End Bonus" 
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason <span class="required">*</span></label>
                        <textarea 
                            id="reason" 
                            name="reason" 
                            placeholder="e.g. Outstanding performance in Q4 2024" 
                            required
                        ></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount (₱) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            min="0.01" 
                            id="amount" 
                            name="amount" 
                            placeholder="0.00" 
                            required
                        >
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBonusModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus" style="font-size: 0.8rem;"></i>
                        Add Bonus
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal-overlay modal-delete" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h2>
                    <i class="fas fa-trash-alt" style="color: #ff6b6b; margin-right: 0.5rem;"></i>
                    Delete Bonus
                </h2>
                <button type="button" class="modal-close" onclick="closeDeleteModal()">
                    <i class="fas fa-times" style="font-size: 1.2rem;"></i>
                </button>
            </div>
            
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="bonus_id" id="deleteBonusId" value="">
                <input type="hidden" name="submission_token" value="<?php echo $submissionToken; ?>">
                
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-trash-alt" style="font-size: 2rem;"></i>
                    </div>
                    
                    <div class="delete-warning">Are you sure you want to delete this bonus?</div>
                    <div class="delete-bonus-name" id="deleteBonusName">Bonus Name</div>
                    <div class="delete-employee-name" id="deleteEmployeeName">Assigned to: Employee Name</div>
                    <div class="delete-subtext">This action cannot be undone.</div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger-solid">
                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                        Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================================================================ -->
    <!-- JAVASCRIPT -->
    <!-- ================================================================ -->
    
    <script>
        window.BONUS_DATA = {
            bonuses: <?php echo json_encode($bonuses); ?>
        };
    </script>
    <script src="../../assets/js/bonus.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html>