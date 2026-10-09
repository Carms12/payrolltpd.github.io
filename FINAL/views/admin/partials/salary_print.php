<?php /* Print / export view for salary.php (?action=print). Included by salary.php after the controller has run.
   ?mode=ca -> payroll WITHOUT SSS / Pag-IBIG / PhilHealth: the only deduction column is CA (cash advance). */
$ca_only  = (($_GET['mode'] ?? '') === 'ca');
$col_base = $ca_only ? 12 : 15; ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Payroll<?php echo $ca_only ? '_CA' : ''; ?>_<?php echo date('Y-m-d', strtotime($custom_start)); ?>_to_<?php echo date('Y-m-d', strtotime($custom_end)); ?></title>
        <link rel="stylesheet" href="../../assets/css/salary-print.css">
    </head>
    <body>
    <div class="print-container" id="printContainer">
        <div class="company-header">
            <div class="company-logo"><img src="../../assets/img/logo.jpg" alt="Logo"></div>
            <div class="company-left">
                <div class="company-name">TPD TECHNOLOGY PRODUCT TRADING &amp; SOLUTION</div>
                <div class="company-address">Unit 2 Climaco Compound, SunRiser Lane 2, Tugbungan Zamboanga City</div>
                <div class="company-contact">Tel#: (062)-308-3148 &nbsp;|&nbsp; Cp#: 0993-306-8155</div>
            </div>
            <div class="company-right">
                <div><span class="label">MAYOR'S PERMIT</span> <span class="value">19-2564OR</span></div>
                <div><span class="label">DTI.NO.</span> <span class="value">5741533</span></div>
                <div><span class="label">BIR 2303</span> <span class="value">2RC000109449</span></div>
            </div>
        </div>

        <div class="payroll-title-row">
            <div class="payroll-title">PAYROLL</div>
            <div class="payroll-period">Period: <strong><?php echo date('F d, Y', strtotime($custom_start)) . ' &ndash; ' . date('F d, Y', strtotime($custom_end)); ?></strong></div>
            <?php if ($preset_label): ?>
            <div class="preset-badge"><?php echo $preset_label; ?></div>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="payroll-table" id="payrollTable">
                <thead>
                    <tr class="page-gap"><th colspan="<?php echo $col_base + count($date_columns); ?>"></th></tr>
                    <tr>
                        <th rowspan="2">EMP. NO.</th>
                        <th rowspan="2">NAME</th>
                        <th rowspan="2">POSITION</th>
                        <th rowspan="2">STATUS</th>
                        <th rowspan="2">RATE /DAY</th>
                        <th rowspan="2">GROSS PAY</th>
                        <th rowspan="2">HOLIDAY</th>
                        <th colspan="<?php echo count($date_columns); ?>" class="days-group">DAILY HOURS (Date of Month)</th>
                        <?php if (!$ca_only): ?><th rowspan="2" class="ded-head">SSS</th><?php endif; ?>
                        <?php if (!$ca_only): ?><th rowspan="2" class="ded-head">PAG-IBIG</th><?php endif; ?>
                        <?php if (!$ca_only): ?><th rowspan="2" class="ded-head">PHILHEALTH</th><?php endif; ?>
                        <th rowspan="2" class="ded-head">CA</th>
                        <th rowspan="2" class="ded-head">TOTAL DED.</th>
                        <th rowspan="2">NET AMOUNT</th>
                        <th rowspan="2">NO. OF DAYS</th>
                        <th rowspan="2">SIGNATURE</th>
                    </tr>
                    <tr>
                        <?php foreach ($date_columns as $col): ?>
                        <th class="day-col-header<?php echo $col['is_weekend'] ? ' wk' : ''; ?>">
                            <?php echo $col['day']; ?>
                            <br><small><?php echo substr($col['day_name'], 0, 3); ?></small>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ($all_employee_salaries as $emp_id => $salary):
                        $emp = $salary['employee'];
                        $status_display = $emp['status'] ?? 'Contractual';
                        $daily_rate = $salary['daily_rate'];
                        $gross_pay = $salary['gross_pay'];
                        $holiday_pay = 0;
                        $net_pay = $ca_only ? $salary['net_pay'] + $salary['deductions']['total'] : $salary['net_pay'];
                        $working_days = $salary['working_days'];
                        $ded = $salary['deductions'];
                        $ca = $salary['cash_advance'] ?? 0;
                        
                        $attendance_map = [];
                        foreach ($salary['attendance_details'] as $detail) {
                            $attendance_map[$detail['attendance_date']] = [
                                'hours' => round($detail['hours_worked'], 1),
                                'status' => strtolower($detail['status'] ?? '')
                            ];
                        }
                        
                        $no_of_days = $working_days . 'DAYS';
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($emp['employee_code'] ?? ($emp['employee_id'] ?? '')); ?></td>
                        <td class="left"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></td>
                        <td class="left"><?php echo htmlspecialchars($emp['position'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($status_display); ?></td>
                        <td class="right"><?php echo number_format($daily_rate, 2); ?></td>
                        <td class="right"><?php echo number_format($gross_pay, 2); ?></td>
                        <td class="right"><?php echo number_format($holiday_pay, 2); ?></td>
                        <?php
                        foreach ($date_columns as $col):
                            $date = $col['date'];
                            $attendance = $attendance_map[$date] ?? null;
                            $hours = $attendance ? $attendance['hours'] : 0;
                            $is_weekend = $col['is_weekend'];
                            
                            $css_class = 'day-col';
                            if ($is_weekend) {
                                $css_class .= ' weekend';
                                $display = '--';
                            } elseif ($hours > 0) {
                                $css_class .= ($attendance['status'] === 'late') ? ' late' : (($attendance['status'] === 'half day') ? ' half' : ' present');
                                $display = $hours;
                            } elseif ($attendance !== null) {
                                $css_class .= ' absent';
                                $display = '-';
                            } else {
                                $display = ' ';
                            }
                        ?>
                        <td class="<?php echo $css_class; ?>"><?php echo $display; ?></td>
                        <?php endforeach; ?>
                        <?php if (!$ca_only): ?><td class="right deduction-col"><?php echo number_format($ded['SSS'], 2); ?></td><?php endif; ?>
                        <?php if (!$ca_only): ?><td class="right deduction-col"><?php echo number_format($ded['Pag-IBIG'], 2); ?></td><?php endif; ?>
                        <?php if (!$ca_only): ?><td class="right deduction-col"><?php echo number_format($ded['PhilHealth'], 2); ?></td><?php endif; ?>
                        <td class="right deduction-col"><?php echo number_format($ca, 2); ?></td>
                        <td class="right deduction-col"><?php echo number_format($ca_only ? $ca : $ded['total'] + $ca, 2); ?></td>
                        <td class="right net-col"><?php echo number_format($net_pay, 2); ?></td>
                        <td><?php echo $no_of_days; ?></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (!empty($all_employee_salaries)): ?>
                    <tr class="nothing-row">
                        <td colspan="<?php echo $col_base + count($date_columns); ?>">**** Nothing Follows ****</td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr class="total-row">
                        <td colspan="7" class="total-label">TOTAL</td>
                        <td colspan="<?php echo count($date_columns); ?>"></td>
                        <?php if (!$ca_only): ?><td class="right"><?php echo number_format($total_sss_all, 2); ?></td><?php endif; ?>
                        <?php if (!$ca_only): ?><td class="right"><?php echo number_format($total_pagibig_all, 2); ?></td><?php endif; ?>
                        <?php if (!$ca_only): ?><td class="right"><?php echo number_format($total_philhealth_all, 2); ?></td><?php endif; ?>
                        <td class="right"><?php echo number_format($total_ca_all, 2); ?></td>
                        <td class="right"><?php echo number_format($ca_only ? $total_ca_all : $total_deductions_all + $total_ca_all, 2); ?></td>
                        <td class="right grand"><?php echo number_format($ca_only ? $total_net + $total_deductions_all : $total_net, 2); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="page-gap-foot"><td colspan="<?php echo $col_base + count($date_columns); ?>"></td></tr>
                </tfoot>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <span class="sig-label">CERTIFIED:</span>
                <div class="sig-line"></div>
                <span class="sig-name">RENATO T. PADAMA</span>
                <span class="sig-title">OPERATION MANAGER</span>
                <span class="sig-date">Date: _______________</span>
            </div>
            <div class="signature-box">
                <span class="sig-label">NOTED:</span>
                <div class="sig-line"></div>
                <span class="sig-name">RENATO T. PADAMA</span>
                <span class="sig-title">OPERATION MANAGER</span>
                <span class="sig-date">Date: _______________</span>
            </div>
            <div class="signature-box">
                <span class="sig-label">PREPARED BY:</span>
                <div class="sig-line"></div>
                <span class="sig-name">SUZZETTE S. EMBUDO</span>
                <span class="sig-title">PROJECT ADMINISTRATOR / HR</span>
                <span class="sig-date">DATE: <?php echo date('F d, Y'); ?></span>
            </div>
        </div>

        <div class="footer-note">
            <span>Confidential &ndash; for authorized personnel only</span>
            <span>
            Payroll <?php echo date('F d, Y', strtotime($custom_start)) . ' - ' . date('F d, Y', strtotime($custom_end)); ?>
            <?php if ($preset_label): ?> (<?php echo $preset_label; ?>) <?php endif; ?></span>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        window.SALARY_PRINT = {
            autoPrint: <?php echo $auto_print == 1 ? 'true' : 'false'; ?>,
            excel: <?php echo $format == 'excel' ? 'true' : 'false'; ?>,
            monthYear: '<?php echo date('F_Y', strtotime($custom_start)); ?>',
            colCount: <?php echo $col_base + count($date_columns); ?>,
            presetSuffix: '<?php echo ($preset ? '_' . $preset : '') . ($ca_only ? '_CA' : ''); ?>'
        };
    </script>
    <script src="../../assets/js/salary-print.js"></script>
    <?php if ($format == 'excel'): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <?php endif; ?>
    </body>
    </html>
