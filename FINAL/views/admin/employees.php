<?php
require_once __DIR__ . '/../../controllers/EmployeesController.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Payroll Pro · Employee Management</title>
    
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.tailwindcss.com https://cdnjs.cloudflare.com; font-src https://fonts.gstatic.com https://cdnjs.cloudflare.com; connect-src 'self'; img-src 'self' data: blob:;">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/employees.css">
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
            <a href="employees.php" class="nav-item active">
                <span class="nav-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </span>
                <span>Employees</span>
                <span class="nav-badge"><?php echo $employeeCount; ?></span>
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

    <main class="main-content">
        <header class="page-header">
            <div>
                <h1>Employee Management</h1>
                <div class="subtitle">Manage employee records, positions, and rates across your organization</div>
            </div>
            <div class="header-actions">
                <button onclick="openModal()" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add Employee
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
                <p>Table "employees" doesn't exist in the database.</p>
                <p style="font-size:0.75rem; margin-top:0.5rem; color:rgba(255,255,255,0.1);">Run the employees.sql file in phpMyAdmin or MySQL</p>
            </div>
        </div>
        <?php else: ?>

        <section class="stats-grid">
            <div class="stat-card accent-cyan">
                <div class="stat-label"><i class="fas fa-users stat-icon"></i> Total Employees</div>
                <div class="stat-value"><?php echo $employeeCount; ?></div>
            </div>
            <div class="stat-card accent-green">
                <div class="stat-label"><i class="fas fa-user-check stat-icon"></i> Regular</div>
                <div class="stat-value">
                    <?php 
                        $regular = array_filter($employees, function($e) { return $e['status'] === 'Regular'; });
                        echo count($regular);
                    ?>
                </div>
            </div>
            <div class="stat-card accent-yellow">
                <div class="stat-label"><i class="fas fa-user-clock stat-icon"></i> Project-Based</div>
                <div class="stat-value">
                    <?php 
                        $project = array_filter($employees, function($e) { return $e['status'] === 'Project-Based'; });
                        echo count($project);
                    ?>
                </div>
            </div>
            <div class="stat-card accent-purple">
                <div class="stat-label"><i class="fas fa-dollar-sign stat-icon"></i> Avg Hourly Rate</div>
                <div class="stat-value">
                    <?php 
                        $avg = 0;
                        if (!empty($employees)) {
                            $total = array_sum(array_column($employees, 'hourly_rate'));
                            $avg = $total / count($employees);
                        }
                        echo 'PHP ' . number_format($avg, 2);
                    ?>
                </div>
            </div>
        </section>

        <div class="glass-card" style="margin-bottom: 1.5rem;">
            <form method="GET" class="search-wrapper" role="search">
                <div class="search-input">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" placeholder="Search by name, code, or position..." 
                           value="<?php echo escapeOutput($search); ?>" 
                           maxlength="100" autocomplete="off">
                </div>
                
                <select name="position" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    <?php foreach ($uniquePositions as $pos): ?>
                    <option value="<?php echo escapeOutput($pos); ?>" <?php echo $positionFilter === $pos ? 'selected' : ''; ?>>
                        <?php echo escapeOutput($pos); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if ($search || $positionFilter): ?>
                <a href="employees.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <div>
                    <h3>All Employees</h3>
                    <span class="card-subtitle"><?php echo count($employees); ?> employees found</span>
                    <?php if ($positionFilter): ?>
                    <span class="card-subtitle" style="display:inline-block; margin-left:0.5rem; padding:0.15rem 0.6rem; background:rgba(0,255,255,0.06); border-radius:20px; border:1px solid rgba(0,255,255,0.06);">
                        <i class="fas fa-filter" style="color:#00ffff; font-size:0.6rem;"></i> <?php echo escapeOutput($positionFilter); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (empty($employees)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-users"></i></div>
                <p>No employees found</p>
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
                            <th>Employee Code</th>
                            <th>Name</th>
                            <th>Position</th>
                            <th style="text-align: right;">Hourly Rate</th>
                            <th>Status</th>
                            <th>Date Hired</th>
                            <th style="text-align: right; width: 90px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $index => $employee): ?>
                        <tr>
                            <td class="text-muted"><?php echo $index + 1; ?></td>
                            <td class="text-cyan font-mono" style="font-size:0.8rem;"><?php echo escapeOutput($employee['employee_code']); ?></td>
                            <td class="text-white">
                                <div class="emp-name-cell">
                                    <?php if (!empty($employee['photo_v'])): ?>
                                    <img class="emp-photo" src="employee_photo.php?id=<?php echo (int)$employee['employee_id']; ?>&amp;v=<?php echo (int)$employee['photo_v']; ?>" alt="">
                                    <?php else: ?>
                                    <span class="emp-photo emp-photo-initials"><?php echo escapeOutput(strtoupper(mb_substr($employee['first_name'], 0, 1) . mb_substr($employee['last_name'], 0, 1))); ?></span>
                                    <?php endif; ?>
                                    <span><?php echo escapeOutput($employee['first_name'] . ' ' . $employee['last_name']); ?></span>
                                </div>
                            </td>
                            <td><?php echo escapeOutput($employee['position']); ?></td>
                            <td class="text-rate">
                                <span class="currency">PHP</span>
                                <span class="amount"><?php echo number_format($employee['hourly_rate'], 2); ?></span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $employee['status'])); ?>">
                                    <?php echo escapeOutput($employee['status']); ?>
                                </span>
                            </td>
                            <td style="font-size:0.8rem; color:rgba(255,255,255,0.3);">
                                <?php echo $employee['date_hired'] ? date('M d, Y', strtotime($employee['date_hired'])) : 'N/A'; ?>
                            </td>
                            <td style="text-align: right;">
                                <div class="actions-cell">
                                    <button onclick="openEditModal(<?php echo (int)$employee['employee_id']; ?>)" 
                                            class="btn-icon btn-edit" 
                                            aria-label="Edit employee"
                                            title="Edit Employee">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button onclick="openDeleteModal(<?php echo (int)$employee['employee_id']; ?>, '<?php echo escapeOutput(addslashes($employee['first_name'] . ' ' . $employee['last_name'])); ?>')" 
                                            class="btn-icon btn-delete" 
                                            aria-label="Delete employee"
                                            title="Delete Employee">
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

    <div class="modal-overlay" id="employeeModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Employee</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="employeeForm" onsubmit="return preventDuplicateSubmit()" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="action" id="formAction" value="create">
                <input type="hidden" name="employee_id" id="employeeId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrfToken); ?>">
                <div class="modal-body">
                    <div class="photo-upload">
                        <div class="photo-preview" id="photoPreview"><i class="fas fa-user"></i></div>
                        <div class="photo-actions">
                            <label for="photoInput" class="photo-btn"><i class="fas fa-camera"></i> Choose Photo</label>
                            <input type="file" id="photoInput" name="photo" accept="image/jpeg,image/png,image/webp" onchange="previewPhoto(this)">
                            <label class="photo-remove" id="photoRemoveWrap" style="display:none;">
                                <input type="checkbox" name="remove_photo" id="removePhoto" value="1" onchange="toggleRemovePhoto(this)"> Remove current photo
                            </label>
                            <div class="rate-hint"><i class="fas fa-info-circle"></i> JPG, PNG or WEBP, max 2 MB (optional)</div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employeeCode">Employee Code</label>
                            <input type="text" id="employeeCode" name="employee_code" 
                                   placeholder="Auto-generated" 
                                   maxlength="20" autocomplete="off">
                            <p style="font-size:0.6rem; color:rgba(255,255,255,0.1); margin-top:0.25rem;">Leave blank to auto-generate</p>
                        </div>
                        <div class="form-group">
                            <label for="dateHired">Date Hired</label>
                            <input type="date" id="dateHired" name="date_hired" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="first_name" 
                                   placeholder="First name" maxlength="100" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="last_name" 
                                   placeholder="Last name" maxlength="100" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="positionSelect">Position</label>
                            <select id="positionSelect" name="position" required onchange="updateHourlyRate()">
                                <option value="">Select Position</option>
                                <?php foreach ($positions as $pos): ?>
                                <option value="<?php echo escapeOutput($pos['position_name']); ?>" 
                                        data-rate="<?php echo $pos['hourly_rate']; ?>">
                                    <?php echo escapeOutput($pos['position_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="hourlyRate">Hourly Rate ($)</label>
                            <input type="number" step="0.01" min="0.01" max="9999.99" 
                                   id="hourlyRate" name="hourly_rate" 
                                   placeholder="Auto-filled from position" 
                                   readonly required>
                            <div class="rate-hint">
                                <i class="fas fa-info-circle"></i> Rate is determined by position
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="statusSelect">Status</label>
                        <select id="statusSelect" name="status" required>
                            <option value="">Select Status</option>
                            <?php foreach ($statusOptions as $status): ?>
                            <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-plus"></i> Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay modal-delete" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h2><i class="fas fa-trash mr-2"></i> Delete Employee</h2>
                <button class="modal-close" onclick="closeDeleteModal()" aria-label="Close delete modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form method="POST" id="deleteForm">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="employee_id" id="deleteEmployeeId" value="">
                <input type="hidden" name="csrf_token" value="<?php echo escapeOutput($csrfToken); ?>">
                <div class="modal-body">
                    <div class="delete-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="delete-warning">Are you sure you want to delete this employee?</div>
                    <div class="delete-employee-name" id="deleteEmployeeName">Employee Name</div>
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
        window.EMPLOYEES_DATA = {
            employees: <?php echo json_encode($employees, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>
        };
    </script>
    <script src="../../assets/js/employees.js"></script>
    <script src="../../assets/js/mobile.js"></script>
</body>
</html>