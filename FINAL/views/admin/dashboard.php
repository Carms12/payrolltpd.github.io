<?php
require_once __DIR__ . '/../../controllers/DashboardController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Pro · Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
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
                <div>
                    <span class="text-sm font-bold text-white tracking-tight">TPD<span style="color:#00ffff;">Solutions</span></span>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Main</div>
            <a href="dashboard.php" class="nav-item active">
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
                <h1> Dashboard</h1>
                <div class="subtitle">Payroll overview and analytics at a glance</div>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="message error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card accent-cyan">
                <div class="stat-label"><i class="fas fa-users stat-icon"></i> Total Employees</div>
                <div class="stat-value"><?php echo $totalEmployees; ?></div>
            </div>
            <div class="stat-card accent-green">
                <div class="stat-label"><i class="fas fa-check-circle stat-icon"></i> Present Today</div>
                <div class="stat-value"><?php echo $todayAttendance['present'] ?? 0; ?></div>
            </div>
            <div class="stat-card accent-red">
                <div class="stat-label"><i class="fas fa-times-circle stat-icon"></i> Absent Today</div>
                <div class="stat-value"><?php echo $todayAttendance['absent'] ?? 0; ?></div>
            </div>
            <div class="stat-card accent-orange">
                <div class="stat-label"><i class="fas fa-clock stat-icon"></i> Late Today</div>
                <div class="stat-value"><?php echo $todayAttendance['late'] ?? 0; ?></div>
            </div>
            <div class="stat-card accent-purple">
                <div class="stat-label"><i class="fas fa-star stat-icon"></i> Total Bonuses</div>
                <div class="stat-value">₱<?php echo number_format($totalBonuses['total_amount'] ?? 0, 2); ?></div>
            </div>
            <div class="stat-card accent-pink">
                <div class="stat-label"><i class="fas fa-gem stat-icon"></i> Total Incentives</div>
                <div class="stat-value">₱<?php echo number_format($totalIncentives['total_amount'] ?? 0, 2); ?></div>
            </div>
            <div class="stat-card accent-red">
                <div class="stat-label"><i class="fas fa-minus-circle stat-icon"></i> Total Deductions</div>
                <div class="stat-value">₱<?php echo number_format($totalDeductions['total_amount'] ?? 0, 2); ?></div>
            </div>
        </div>

        <div class="grid-2col">
            <div class="glass-card">
                <div class="card-header">
                    <h3><i class="fas fa-chart-pie" style="color: #00ffff; margin-right: 0.5rem;"></i> Employee Status</h3>
                    <span class="card-subtitle">Total: <?php echo $totalEmployees; ?></span>
                </div>
                <div style="display: flex; gap: 2rem; padding: 0.5rem 0; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Regular</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #34d399;"><?php echo $regularCount; ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Project-Based</div>
                        <div style="font-size: 2rem; font-weight: 700; color: #fb923c;"><?php echo $projectBasedCount; ?></div>
                    </div>
                    <div style="flex: 1;"></div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 3px; background: #34d399;"></span>
                        <span style="font-size: 0.75rem; color: rgba(255,255,255,0.3);">Regular</span>
                        <span style="display: inline-block; width: 12px; height: 12px; border-radius: 3px; background: #fb923c; margin-left: 0.5rem;"></span>
                        <span style="font-size: 0.75rem; color: rgba(255,255,255,0.3);">Project-Based</span>
                    </div>
                </div>
                <div style="display: flex; height: 8px; border-radius: 4px; overflow: hidden; margin-top: 0.5rem;">
                    <?php if ($totalEmployees > 0): ?>
                    <div style="background: #34d399; width: <?php echo ($regularCount / $totalEmployees) * 100; ?>%;"></div>
                    <div style="background: #fb923c; width: <?php echo ($projectBasedCount / $totalEmployees) * 100; ?>%;"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="glass-card">
                <div class="card-header">
                    <h3><i class="fas fa-wallet" style="color: #a78bfa; margin-right: 0.5rem;"></i> Payroll Summary</h3>
                    <span class="card-subtitle">This Month</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; padding: 0.5rem 0;">
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Employees Tracked</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #ffffff;"><?php echo $payrollSummary['employees_with_attendance'] ?? 0; ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Total Attendance</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #ffffff;"><?php echo $payrollSummary['total_attendance'] ?? 0; ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Present Days</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #34d399;"><?php echo $payrollSummary['present_days'] ?? 0; ?></div>
                    </div>
                    <div>
                        <div style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">Total Deductions</div>
                        <div style="font-size: 1.3rem; font-weight: 700; color: #ff6b6b;">₱<?php echo number_format($payrollSummary['total_deductions'] ?? 0, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2col">
            <div class="glass-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-plus" style="color: #34d399; margin-right: 0.5rem;"></i> Recent Employees</h3>
                    <a href="employees.php" style="font-size: 0.7rem; color: rgba(255,255,255,0.15); text-decoration: none;">View All →</a>
                </div>
                <?php if (empty($recentEmployees)): ?>
                <div style="text-align: center; padding: 1.5rem 0; color: rgba(255,255,255,0.15); font-size: 0.85rem;">
                    No employees added yet
                </div>
                <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentEmployees as $emp): ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <span class="employee-avatar"><?php echo strtoupper(substr($emp['first_name'],0,1).substr($emp['last_name'],0,1)); ?></span>
                                        <span class="text-white" style="font-size: 0.8rem;"><?php echo htmlspecialchars($emp['first_name'].' '.$emp['last_name']); ?></span>
                                    </div>
                                </td>
                                <td style="font-size: 0.8rem; color: rgba(255,255,255,0.3);"><?php echo htmlspecialchars($emp['position']); ?></td>
                                <td><span class="employee-status-badge status-<?php echo str_replace('-', ' ', $emp['status']); ?>"><?php echo $emp['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <div class="glass-card">
                <div class="card-header">
                    <h3><i class="fas fa-clipboard-list" style="color: #fb923c; margin-right: 0.5rem;"></i> Recent Attendance</h3>
                    <a href="attendance.php" style="font-size: 0.7rem; color: rgba(255,255,255,0.15); text-decoration: none;">View All →</a>
                </div>
                <?php if (empty($recentAttendance)): ?>
                <div style="text-align: center; padding: 1.5rem 0; color: rgba(255,255,255,0.15); font-size: 0.85rem;">
                    No attendance records yet
                </div>
                <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAttendance as $att): ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <span class="employee-avatar"><?php echo strtoupper(substr($att['first_name'],0,1).substr($att['last_name'],0,1)); ?></span>
                                        <span class="text-white" style="font-size: 0.8rem;"><?php echo htmlspecialchars($att['first_name'].' '.$att['last_name']); ?></span>
                                    </div>
                                </td>
                                <td style="font-size: 0.8rem; color: rgba(255,255,255,0.3);"><?php echo date('M d, Y', strtotime($att['attendance_date'])); ?></td>
                                <td><span class="status-badge status-<?php echo str_replace(' ','.',$att['status']); ?>"><?php echo $att['status']; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-bar" style="color: #00ffff; margin-right: 0.5rem;"></i> Monthly Attendance (Last 30 Days)</h3>
                <span class="card-subtitle">Daily attendance overview</span>
            </div>
            <?php if (empty($monthlySummary)): ?>
            <div style="text-align: center; padding: 2rem 0; color: rgba(255,255,255,0.15); font-size: 0.85rem;">
                No attendance data available for the last 30 days
            </div>
            <?php else: ?>
            <div class="attendance-chart">
                <?php 
                $maxTotal = max(array_column($monthlySummary, 'total')) ?: 1;
                foreach ($monthlySummary as $day): 
                    $height = ($day['total'] / $maxTotal) * 70;
                    $presentPercent = $day['total'] > 0 ? ($day['present'] / $day['total']) * 100 : 0;
                    $absentPercent = $day['total'] > 0 ? ($day['absent'] / $day['total']) * 100 : 0;
                ?>
                <div class="chart-bar">
                    <div class="bar" style="height: <?php echo max(4, $height); ?>px; background: rgba(255,255,255,0.05); position: relative; overflow: hidden;">
                        <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: <?php echo $presentPercent; ?>%; background: #34d399; border-radius: 3px 3px 0 0;"></div>
                        <div style="position: absolute; bottom: <?php echo $presentPercent; ?>%; left: 0; width: 100%; height: <?php echo $absentPercent; ?>%; background: #ff6b6b; border-radius: 0;"></div>
                    </div>
                    <div class="bar-label"><?php echo date('d', strtotime($day['attendance_date'])); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="display: flex; gap: 1.5rem; justify-content: center; padding-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.04);">
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: #34d399; vertical-align: middle; margin-right: 0.3rem;"></span> Present
                </span>
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.2);">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: #ff6b6b; vertical-align: middle; margin-right: 0.3rem;"></span> Absent
                </span>
                <span style="font-size: 0.7rem; color: rgba(255,255,255,0.15);">
                    Total Records: <?php echo array_sum(array_column($monthlySummary, 'total')); ?>
                </span>
            </div>
            <?php endif; ?>
        </div>

        <div style="text-align:center;padding:1.5rem 0 0.5rem;color:rgba(255,255,255,0.08);font-size:0.7rem;">
            Payroll System &bull; TPD Solutions &bull; <?php echo date('F d, Y h:i A'); ?>
        </div>
    </div>

    <script src="../../assets/js/dashboard.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html>