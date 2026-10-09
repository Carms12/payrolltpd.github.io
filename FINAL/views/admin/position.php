<?php
require_once __DIR__ . '/../../controllers/PositionController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Payroll Pro · Position Management</title>
    
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com; connect-src 'self';">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/position.css">
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

    <!-- ===== Sidebar ===== -->
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

            <div class="nav-label">Organization</div>
            <a href="position.php" class="nav-item active">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </span>
                <span>Positions</span>
                <span class="nav-badge"><?php echo $positionCount; ?></span>
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
    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Position Management</h1>
                <div class="subtitle">Manage job positions and hourly rates across your organization</div>
            </div>
            <div class="header-actions">
                <button onclick="openModal()" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    Add Position
                </button>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if (!$tableExists): ?>
        <div class="glass-card">
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-database"></i></div>
                <p>Table "positions" doesn't exist in the database.</p>
                <p style="font-size:0.75rem; margin-top:0.5rem; color:rgba(255,255,255,0.1);">Run the positions.sql file in phpMyAdmin or MySQL</p>
            </div>
        </div>
        <?php else: ?>

        <div class="stats-grid">
            <div class="stat-card accent-cyan">
                <div class="stat-label"><i class="fas fa-briefcase stat-icon"></i> Total Positions</div>
                <div class="stat-value"><?php echo $positionCount; ?></div>
            </div>
            <div class="stat-card accent-green">
                <div class="stat-label"><i class="fas fa-dollar-sign stat-icon"></i> Avg Hourly Rate</div>
                <div class="stat-value">
                    <?php 
                        $avg = 0;
                        if (!empty($positions)) {
                            $total = array_sum(array_column($positions, 'hourly_rate'));
                            $avg = $total / count($positions);
                        }
                        echo 'PHP' . number_format($avg, 2);
                    ?>
                </div>
            </div>
            <div class="stat-card accent-purple">
                <div class="stat-label"><i class="fas fa-arrow-up stat-icon"></i> Highest Rate</div>
                <div class="stat-value">
                    <?php 
                        $max = 0;
                        if (!empty($positions)) {
                            $max = max(array_column($positions, 'hourly_rate'));
                        }
                        echo 'PHP' . number_format($max, 2);
                    ?>
                </div>
            </div>
            <div class="stat-card accent-pink">
                <div class="stat-label"><i class="fas fa-arrow-down stat-icon"></i> Lowest Rate</div>
                <div class="stat-value">
                    <?php 
                        $min = 0;
                        if (!empty($positions)) {
                            $min = min(array_column($positions, 'hourly_rate'));
                        }
                        echo 'PHP' . number_format($min, 2);
                    ?>
                </div>
            </div>
        </div>

        <div class="glass-card" style="margin-bottom: 1.5rem;">
            <form method="GET" class="search-wrapper">
                <div class="search-input">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Search positions..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if ($search): ?>
                <a href="position.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <div>
                    <h3>All Positions</h3>
                    <span class="card-subtitle"><?php echo count($positions); ?> positions found</span>
                </div>
            </div>

            <?php if (empty($positions)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-briefcase"></i></div>
                <p>No positions found</p>
                <?php if ($search): ?>
                <p style="font-size:0.75rem; margin-top:0.5rem; color:rgba(255,255,255,0.1);">Try adjusting your search criteria</p>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Position Name</th>
                            <th style="text-align: right;">Hourly Rate</th>
                            <th style="text-align: right; width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($positions as $index => $position): ?>
                        <tr>
                            <td class="text-muted"><?php echo $index + 1; ?></td>
                            <td class="text-white"><?php echo htmlspecialchars($position['position_name']); ?></td>
                            <td style="text-align: right; font-weight: 600; color: #00ffff;">
                                PHP<?php echo number_format($position['hourly_rate'], 2); ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="actions-cell">
                                    <button onclick="openEditModal(<?php echo $position['position_id']; ?>)" class="btn btn-warning btn-sm" style="border: none;" aria-label="Edit position">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ffd93d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 20h9"></path>
                                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo $position['position_id']; ?>, '<?php echo htmlspecialchars($position['position_name']); ?>')" class="btn btn-danger btn-sm" style="border: none;" aria-label="Delete position">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ff6b6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                        </svg>
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

    <!-- ===== Add/Edit Modal ===== -->
    <div class="modal-overlay" id="positionModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Position</h2>
                <button class="modal-close" onclick="closeModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" id="positionForm" onsubmit="return preventDuplicateSubmit()">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="position_id" id="positionId" value="">
                <input type="hidden" name="submission_token" id="submissionToken" value="<?php echo $submissionToken; ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="positionName">Position Name</label>
                        <input type="text" id="positionName" name="position_name" placeholder="e.g. lineman" required>
                    </div>
                    <div class="form-group">
                        <label for="hourlyRate">Hourly Rate </label>
                        <input type="number" step="0.01" min="0" id="hourlyRate" name="hourly_rate" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Position
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== Delete Confirmation Modal ===== -->
    <div class="modal-overlay modal-delete" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h2>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ff6b6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete Position
                </h2>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.25)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="position_id" id="deletePositionId" value="">
                <input type="hidden" name="submission_token" value="<?php echo $submissionToken; ?>">
                <div class="modal-body">
                    <div class="delete-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ff6b6b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </div>
                    <div class="delete-warning">Are you sure you want to delete this position?</div>
                    <div class="delete-position-name" id="deletePositionName">Position Name</div>
                    <div class="delete-subtext">This action cannot be undone.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger-solid">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 4px;">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                        Delete Permanently
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.POSITIONS_DATA = {
            positions: <?php echo json_encode($positions); ?>
        };
    </script>
    <script src="../../assets/js/position.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html>