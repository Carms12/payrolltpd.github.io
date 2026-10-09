<?php
require_once __DIR__ . '/../../controllers/AttendanceController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Pro · Attendance Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/attendance.css">
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
                <div><span class="text-sm font-bold text-white tracking-tight">TPD<span style="color:#00ffff;">Solutions</span></span></div>
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
            <a href="attendance.php" class="nav-item active">
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

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>📋 Attendance Management</h1>
                <div class="subtitle">Track employee attendance with time in/out records</div>
            </div>
            <div class="header-actions">
                <button type="button" onclick="openImportModal()" class="btn btn-success">
                    <i class="fas fa-file-import" style="font-size: 0.8rem;"></i> Import CSV / Excel
                </button>
                <button type="button" onclick="openBulkModal()" class="btn btn-secondary">
                    <i class="fas fa-users" style="font-size: 0.8rem;"></i> Bulk
                </button>
                <button type="button" onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-plus" style="font-size: 0.8rem;"></i> Add
                </button>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="message <?php echo $messageType; ?>">
            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : ($messageType === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle'); ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <?php if (!$tableExists): ?>
        <div class="glass-card">
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-database"></i></div>
                <p>Table "attendance" doesn't exist. Run the SQL script first.</p>
            </div>
        </div>
        <?php else: ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label"><i class="fas fa-check-circle" style="color: #34d399; margin-right: 0.3rem;"></i> Present</div>
                <div class="stat-value" style="color:#34d399;"><?php echo $stats['present'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fas fa-times-circle" style="color: #ff6b6b; margin-right: 0.3rem;"></i> Absent</div>
                <div class="stat-value" style="color:#ff6b6b;"><?php echo $stats['absent'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fas fa-clock" style="color: #ffd93d; margin-right: 0.3rem;"></i> Late</div>
                <div class="stat-value" style="color:#ffd93d;"><?php echo $stats['late'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label"><i class="fas fa-sun" style="color: #fb923c; margin-right: 0.3rem;"></i> Half Day</div>
                <div class="stat-value" style="color:#fb923c;"><?php echo $stats['half_day'] ?? 0; ?></div>
            </div>
        </div>

        <div class="glass-card">
            <form method="GET" action="attendance.php" class="filter-bar" id="filterForm">
                <input type="hidden" name="per_page" value="<?php echo $perPage; ?>">
                
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-user"></i> Name</span>
                    <input type="text" name="employee_name" placeholder="Search employee..." value="<?php echo htmlspecialchars($filterEmployeeName ?? ''); ?>" style="width: 180px;">
                </div>
                
                <div class="filter-divider"></div>
                
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-calendar-alt"></i> From</span>
                    <input type="date" name="date_from" value="<?php echo $filterDateFrom; ?>" style="width: 140px;">
                </div>
                <div class="filter-group">
                    <span class="filter-label">To</span>
                    <input type="date" name="date_to" value="<?php echo $filterDateTo; ?>" style="width: 140px;">
                </div>
                
                <div class="filter-divider"></div>
                
                <div class="filter-group">
                    <span class="filter-label"><i class="fas fa-filter"></i> Employee</span>
                    <select name="employee_id" style="width: 160px;">
                        <option value="">All Employees</option>
                        <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($filterEmployeeId == $emp['employee_id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($emp['last_name'].', '.$emp['first_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-divider"></div>
                
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="attendance.php" class="btn btn-clear btn-sm">
                    <i class="fas fa-times"></i> Clear All
                </a>
            </form>
            
            <?php if ($filterDateFrom || $filterDateTo || $filterEmployeeId || $filterEmployeeName): ?>
            <div style="margin-top: 0.75rem; display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 0.75rem; border-top: 1px solid rgba(255,255,255,0.04);">
                <span style="font-size: 0.65rem; color: rgba(255,255,255,0.15); font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center;">Active Filters:</span>
                
                <?php if ($filterDateFrom || $filterDateTo): ?>
                <span style="background: rgba(0,255,255,0.04); border: 1px solid rgba(0,255,255,0.06); border-radius: 6px; padding: 0.15rem 0.6rem; font-size: 0.7rem; color: #00ffff;">
                    <i class="fas fa-calendar-alt" style="margin-right: 0.3rem; font-size: 0.6rem;"></i>
                    <?php echo $filterDateFrom ? date('M d, Y', strtotime($filterDateFrom)) : 'Any'; ?> 
                    → 
                    <?php echo $filterDateTo ? date('M d, Y', strtotime($filterDateTo)) : 'Any'; ?>
                </span>
                <?php endif; ?>
                
                <?php if ($filterEmployeeName): ?>
                <span style="background: rgba(52,211,153,0.04); border: 1px solid rgba(52,211,153,0.06); border-radius: 6px; padding: 0.15rem 0.6rem; font-size: 0.7rem; color: #34d399;">
                    <i class="fas fa-user" style="margin-right: 0.3rem; font-size: 0.6rem;"></i>
                    <?php echo htmlspecialchars($filterEmployeeName); ?>
                </span>
                <?php endif; ?>
                
                <?php if ($filterEmployeeId): 
                    $selectedEmp = array_filter($employees, function($e) use ($filterEmployeeId) { return $e['employee_id'] == $filterEmployeeId; });
                    $empName = !empty($selectedEmp) ? current($selectedEmp)['first_name'] . ' ' . current($selectedEmp)['last_name'] : 'Unknown';
                ?>
                <span style="background: rgba(251,146,60,0.04); border: 1px solid rgba(251,146,60,0.06); border-radius: 6px; padding: 0.15rem 0.6rem; font-size: 0.7rem; color: #fb923c;">
                    <i class="fas fa-id-badge" style="margin-right: 0.3rem; font-size: 0.6rem;"></i>
                    <?php echo htmlspecialchars($empName); ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($profileEmployee)): $pe = $profileEmployee; ?>
        <div class="glass-card emp-profile">
            <div class="emp-profile-photo">
                <?php if (!empty($pe['photo_v'])): ?>
                    <img src="employee_photo.php?id=<?php echo (int)$pe['employee_id']; ?>&amp;v=<?php echo (int)$pe['photo_v']; ?>" alt="Employee photo">
                <?php else: ?>
                    <span><?php echo htmlspecialchars(strtoupper(mb_substr($pe['first_name'], 0, 1) . mb_substr($pe['last_name'], 0, 1))); ?></span>
                <?php endif; ?>
            </div>
            <div class="emp-profile-main">
                <div class="emp-profile-name"><?php echo htmlspecialchars($pe['first_name'] . ' ' . $pe['last_name']); ?></div>
                <div class="emp-profile-sub">
                    <span class="emp-profile-code"><?php echo htmlspecialchars($pe['employee_code'] ?? ''); ?></span>
                    <?php if (!empty($pe['position'])): ?><span><?php echo htmlspecialchars($pe['position']); ?></span><?php endif; ?>
                    <?php if (!empty($pe['status'])): ?><span class="emp-profile-badge"><?php echo htmlspecialchars($pe['status']); ?></span><?php endif; ?>
                </div>
                <div class="emp-profile-grid">
                    <div><small>Date Hired</small><b><?php echo !empty($pe['date_hired']) ? date('M d, Y', strtotime($pe['date_hired'])) : 'N/A'; ?></b></div>
                    <div><small>Rate / Hour</small><b>&#8369;<?php echo number_format((float)($pe['hourly_rate'] ?? 0), 2); ?></b></div>
                    <div><small>Rate / Day</small><b>&#8369;<?php echo number_format(!empty($pe['daily_rate']) ? (float)$pe['daily_rate'] : (float)($pe['hourly_rate'] ?? 0) * 8, 2); ?></b></div>
                    <?php if (!empty($pe['sss_number'])): ?><div><small>SSS No.</small><b><?php echo htmlspecialchars($pe['sss_number']); ?></b></div><?php endif; ?>
                    <?php if (!empty($pe['philhealth_number'])): ?><div><small>PhilHealth No.</small><b><?php echo htmlspecialchars($pe['philhealth_number']); ?></b></div><?php endif; ?>
                    <?php if (!empty($pe['pagibig_number'])): ?><div><small>Pag-IBIG No.</small><b><?php echo htmlspecialchars($pe['pagibig_number']); ?></b></div><?php endif; ?>
                </div>
            </div>
            <div class="emp-profile-stats">
                <div><b style="color:#34d399;"><?php echo (int)($stats['present'] ?? 0); ?></b><small>Present</small></div>
                <div><b style="color:#ffd93d;"><?php echo (int)($stats['late'] ?? 0); ?></b><small>Late</small></div>
                <div><b style="color:#fb923c;"><?php echo (int)($stats['half_day'] ?? 0); ?></b><small>Half Day</small></div>
                <div><b style="color:#ff6b6b;"><?php echo (int)($stats['absent'] ?? 0); ?></b><small>Absent</small></div>
            </div>
        </div>
        <?php endif; ?>

        <div class="glass-card">
            <div class="card-header">
                <div>
                    <h3>📅 
                        <?php 
                        if ($filterDateFrom && $filterDateTo) {
                            echo date('M d, Y', strtotime($filterDateFrom)) . ' — ' . date('M d, Y', strtotime($filterDateTo));
                        } elseif ($filterDateFrom) {
                            echo 'From ' . date('M d, Y', strtotime($filterDateFrom));
                        } elseif ($filterDateTo) {
                            echo 'Until ' . date('M d, Y', strtotime($filterDateTo));
                        } else {
                            echo 'All Records';
                        }
                        ?>
                    </h3>
                    <span class="card-subtitle">
                        Showing <?php echo count($attendanceRecords); ?> of <?php echo $totalRecords; ?> records
                    </span>
                </div>
            </div>
            <?php if (empty($attendanceRecords)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                <p>No attendance records found matching your filters</p>
            </div>
            <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Status</th>
                            <th>Remarks</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $r): ?>
                        <tr>
                            <td>
                                <div class="employee-info">
                                    <span class="employee-avatar"><?php echo strtoupper(substr($r['first_name'],0,1).substr($r['last_name'],0,1)); ?></span>
                                    <div>
                                        <div class="text-white"><?php echo htmlspecialchars($r['first_name'].' '.$r['last_name']); ?></div>
                                        <div style="font-size:0.65rem;color:rgba(255,255,255,0.2);"><?php echo htmlspecialchars($r['employee_code']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-white"><?php echo date('M d, Y', strtotime($r['attendance_date'])); ?></td>
                            <td><?php echo $r['time_in'] ? date('h:i A', strtotime($r['time_in'])) : '<span class="text-muted">—</span>'; ?></td>
                            <td><?php echo $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '<span class="text-muted">—</span>'; ?></td>
                            <td><span class="status-badge status-<?php echo str_replace(' ','.',$r['status']); ?>"><?php echo $r['status']; ?></span></td>
                            <td class="text-muted"><?php echo $r['remarks'] ? htmlspecialchars($r['remarks']) : '—'; ?></td>
                            <td>
                                <div class="actions-cell">
                                    <button type="button" onclick="editRecord(<?php echo $r['attendance_id']; ?>)" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fas fa-edit" style="font-size: 0.7rem;"></i>
                                    </button>
                                    <button type="button" onclick="deleteRecord(<?php echo $r['attendance_id']; ?>, '<?php echo htmlspecialchars(addslashes($r['first_name'].' '.$r['last_name'])); ?>')" class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fas fa-trash-alt" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- ==================== PAGINATION ==================== -->
            <?php if ($totalPages > 1 || $perPage != 10): ?>
            <div class="pagination-wrap">
                <div class="pagination-info">
                    Showing <strong><?php echo $offset + 1; ?></strong>–<strong><?php echo min($offset + $perPage, $totalRecords); ?></strong>
                    of <strong><?php echo $totalRecords; ?></strong> records
                    &nbsp;|&nbsp; Page <strong><?php echo $page; ?></strong> of <strong><?php echo $totalPages; ?></strong>
                </div>

                <div class="per-page-select">
                    <label for="perPageSelect">Per page:</label>
                    <select id="perPageSelect" onchange="changePerPage(this.value)">
                        <?php foreach ([10, 20, 25, 50, 100] as $pp): ?>
                        <option value="<?php echo $pp; ?>" <?php echo $perPage == $pp ? 'selected' : ''; ?>><?php echo $pp; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($totalPages > 1): ?>
                <div class="pagination-controls">
                    <!-- First -->
                    <a href="<?php echo pageUrl(1, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName); ?>"
                       class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                       title="First page">
                        <i class="fas fa-angle-double-left" style="font-size: 0.7rem;"></i>
                    </a>
                    <!-- Prev -->
                    <a href="<?php echo pageUrl(max(1, $page - 1), $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName); ?>"
                       class="page-btn <?php echo $page <= 1 ? 'disabled' : ''; ?>"
                       title="Previous page">
                        <i class="fas fa-angle-left" style="font-size: 0.7rem;"></i>
                    </a>

                    <?php
                    // Smart page number window (current ±2, plus first/last with ellipsis)
                    $window = 2;
                    $start = max(1, $page - $window);
                    $end = min($totalPages, $page + $window);

                    if ($start > 1) {
                        echo '<a href="' . pageUrl(1, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName) . '" class="page-btn' . ($page == 1 ? ' active' : '') . '">1</a>';
                        if ($start > 2) echo '<span class="page-ellipsis">…</span>';
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        echo '<a href="' . pageUrl($i, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName) . '" class="page-btn' . ($i == $page ? ' active' : '') . '">' . $i . '</a>';
                    }

                    if ($end < $totalPages) {
                        if ($end < $totalPages - 1) echo '<span class="page-ellipsis">…</span>';
                        echo '<a href="' . pageUrl($totalPages, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName) . '" class="page-btn' . ($page == $totalPages ? ' active' : '') . '">' . $totalPages . '</a>';
                    }
                    ?>

                    <!-- Next -->
                    <a href="<?php echo pageUrl(min($totalPages, $page + 1), $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName); ?>"
                       class="page-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
                       title="Next page">
                        <i class="fas fa-angle-right" style="font-size: 0.7rem;"></i>
                    </a>
                    <!-- Last -->
                    <a href="<?php echo pageUrl($totalPages, $perPage, $filterDateFrom, $filterDateTo, $filterEmployeeId, $filterEmployeeName); ?>"
                       class="page-btn <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"
                       title="Last page">
                        <i class="fas fa-angle-double-right" style="font-size: 0.7rem;"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <!-- ==================== /PAGINATION ==================== -->

            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="modal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add Attendance</h2>
                <button type="button" class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" onsubmit="return handleSubmit()">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="attendance_id" id="attendanceId" value="">
                <input type="hidden" name="submission_token" value="<?php echo $submissionToken; ?>">
                <div class="form-group">
                    <label>Employee <span class="required">*</span></label>
                    <select id="empSelect" name="employee_id" required>
                        <option value="">— Select Employee —</option>
                        <?php foreach ($employees as $emp): 
                            $isDisabled = in_array($emp['employee_id'], $existingAttendance);
                        ?>
                        <option value="<?php echo $emp['employee_id']; ?>" <?php echo $isDisabled ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?>
                            <?php if ($isDisabled): ?>
                                (Already Recorded)
                            <?php endif; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($existingAttendance)): ?>
                    <small style="color: rgba(255,255,255,0.15); font-size: 0.7rem; display: block; margin-top: 0.3rem;">
                        <i class="fas fa-info-circle"></i> Employees marked with "(Already Recorded)" cannot be selected again for this date.
                    </small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Date <span class="required">*</span></label>
                    <input type="date" id="attDate" name="attendance_date" value="<?php echo $filterDate; ?>" required>
                </div>
                <div class="form-group">
                    <label>Status <span class="required">*</span></label>
                    <select id="statusSelect" name="status" required onchange="toggleTimeFields()" style="color: #e8edf5;">
                        <option value="Present" style="color: #34d399;">Present</option>
                        <option value="Absent" style="color: #ff6b6b;">Absent</option>
                        <option value="Late" style="color: #ffd93d;">Late</option>
                        <option value="Half Day" style="color: #fb923c;">Half Day</option>
                    </select>
                </div>
                <div class="form-row" id="timeFields">
                    <div class="form-group">
                        <label>Time In <span class="required" id="timeInRequired">*</span></label>
                        <input type="time" id="timeIn" name="time_in">
                    </div>
                    <div class="form-group">
                        <label>Time Out <span class="required" id="timeOutRequired">*</span></label>
                        <input type="time" id="timeOut" name="time_out">
                    </div>
                </div>
                <div class="form-group">
                    <label>Remarks</label>
                    <textarea id="remarks" name="remarks" placeholder="Optional notes..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save" style="font-size: 0.8rem;"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Modal -->
    <div class="modal-overlay" id="bulkModal">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-users" style="margin-right: 0.5rem; color: #00ffff;"></i> Bulk Attendance</h2>
                <button type="button" class="modal-close" onclick="closeBulkModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="bulk_create">
                <input type="hidden" name="submission_token" value="<?php echo $submissionToken; ?>">
                <div class="form-group">
                    <label>Date <span class="required">*</span></label>
                    <input type="date" name="attendance_date" value="<?php echo $filterDate; ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" style="color: #e8edf5;">
                        <option value="Present" style="color: #34d399;">Present</option>
                        <option value="Absent" style="color: #ff6b6b;">Absent</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Time In</label>
                        <input type="time" name="time_in" value="08:00">
                    </div>
                    <div class="form-group">
                        <label>Time Out</label>
                        <input type="time" name="time_out" value="17:00">
                    </div>
                </div>
                <div class="form-group">
                    <label>Select Employees <span class="required">*</span></label>
                    <div class="employee-checklist">
                        <?php foreach ($employees as $emp): 
                            $isDisabled = in_array($emp['employee_id'], $existingAttendance);
                        ?>
                        <label style="<?php echo $isDisabled ? 'opacity: 0.4; cursor: not-allowed;' : ''; ?>">
                            <input type="checkbox" name="employee_ids[]" value="<?php echo $emp['employee_id']; ?>" <?php echo $isDisabled ? 'disabled' : ''; ?>>
                            <span class="emp-name"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></span>
                            <span class="emp-code"><?php echo htmlspecialchars($emp['employee_code']); ?></span>
                            <?php if ($isDisabled): ?>
                            <span style="color: rgba(255,255,255,0.2); font-size: 0.65rem;">(Already Recorded)</span>
                            <?php endif; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeBulkModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check" style="font-size: 0.8rem;"></i> Record Attendance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div class="modal-overlay" id="importModal">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-file-import" style="margin-right: 0.5rem; color: #34d399;"></i> Import Attendance (CSV or Time Clock .xls)</h2>
                <button type="button" class="modal-close" onclick="closeImportModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data" onsubmit="return validateCSV()">
                <input type="hidden" name="submission_token" value="<?php echo $submissionToken; ?>">
                
                <div class="form-group">
                    <label>File <span class="required">*</span></label>
                    <input type="file" name="csv_file" accept=".csv,.txt,.xls" required id="csvFile">
                    <small style="color: rgba(255,255,255,0.15); font-size: 0.7rem; display: block; margin-top: 0.3rem;">
                        <i class="fas fa-info-circle"></i> Max file size: 10MB. Supported formats: .csv, .txt, .xls (time clock "Attendance Record" export)
                    </small>
                </div>
                
                <div class="form-group">
                    <label>Default Date (if not in CSV)</label>
                    <input type="date" name="csv_date" value="<?php echo $filterDate; ?>">
                    <small style="color: rgba(255,255,255,0.15); font-size: 0.7rem; display: block; margin-top: 0.3rem;">
                        <i class="fas fa-info-circle"></i> If your CSV doesn't have a date column, all records will use this date.
                    </small>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="xls_blank_absent" value="1" checked>
                        Time clock .xls only: mark weekdays with no punches as Absent
                    </label>
                    <small style="color: rgba(255,255,255,0.15); font-size: 0.7rem; display: block; margin-top: 0.3rem;">
                        <i class="fas fa-info-circle"></i> For .xls files the dates and employee names come from the file itself. Saturdays, Sundays and today are never marked Absent. Employees are matched ONLY by the file's Employee ID = their Employee Code (exact, e.g. 001 and 00001 are different). Time In up to 8:00 AM counts as 8:00 AM; 8:01 and later keeps the exact time and is marked Late. Time Out before 5:00 PM is noted as early out in Remarks.
                    </small>
                </div>

                <div class="form-group">
                    <label>CSV Format Guide</label>
                    <div class="csv-format-box">
                        <div><strong>Required Columns:</strong> <span class="highlight">name</span></div>
                        <div><strong>Optional Columns:</strong> <span class="highlight">date</span>, <span class="highlight">timein</span>, <span class="highlight">timeout</span></div>
                        <div style="margin-top: 0.3rem;">
                            <strong>Example:</strong>
                            <div class="sample-data">name,date,timein,timeout
carms melo,21/08/2026,8:00,5:00
asdasd sd,21/08/2026,8:00,5:50
russ sel,21/08/2026,8:00,5:00
carms melo,20/08/2026,8:00,1:00
asdasd sd,20/08/2026,8:00,3:00
russ sel,20/08/2026,8:00,4:00</div>
                        </div>
                        <div class="status-info">
                            <span style="color: #34d399;">✅ Status is auto-detected:</span>
                            <span style="color: #34d399;">Present</span> (8:00 AM - 5:00 PM),
                            <span style="color: #ffd93d;">Late</span> (after 8:30 AM),
                            <span style="color: #fb923c;">Half Day</span> (&lt; 4 hours),
                            <span style="color: #ff6b6b;">Absent</span> (no time)
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Cancel</button>
                    <button type="submit" class="btn btn-success" id="importBtn">
                        <i class="fas fa-upload" style="font-size: 0.8rem;"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        window.ATTENDANCE_DATA = {
            records: <?php echo json_encode($attendanceRecords); ?>,
            existing: <?php echo json_encode($existingAttendance); ?>,
            filterDate: '<?php echo $filterDate; ?>',
            submissionToken: '<?php echo $submissionToken; ?>'
        };
    </script>
    <script src="../../assets/js/attendance.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html>