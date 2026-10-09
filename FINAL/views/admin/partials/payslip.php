<?php
/* Payslip view for salary.php (?action=payslip[&employee_id=ID]). Included by salary.php after the controller has run.
 * Uses the SAME computed figures as the payroll table ($all_employee_salaries), so payslip = payroll. */
$only_id = isset($_GET['employee_id']) ? (int)$_GET['employee_id'] : 0;
$slips = $only_id ? array_intersect_key($all_employee_salaries, [$only_id => true]) : $all_employee_salaries;
$photo_ids = [];
try {
    foreach ($pdo->query("SELECT employee_id, UNIX_TIMESTAMP(photo_updated) AS v FROM employees WHERE photo IS NOT NULL") as $pr) $photo_ids[(int)$pr['employee_id']] = (int)$pr['v'];
} catch (Exception $e) { /* no photo column yet */ }
// ?mode=ca -> payslip WITHOUT SSS / Pag-IBIG / PhilHealth: the only deduction is the cash advance (CA)
$ca_only = (($_GET['mode'] ?? '') === 'ca');
// ?mode=none -> payslip with NO deductions at all (not even cash advance): net pay = total earnings
$no_ded = (($_GET['mode'] ?? '') === 'none');
$peso = function ($n) { return '&#8369;' . number_format((float)$n, 2); };
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip_<?php echo date('Y-m-d', strtotime($custom_start)); ?>_to_<?php echo date('Y-m-d', strtotime($custom_end)); ?></title>
    <link rel="stylesheet" href="../../assets/css/payslip.css">
</head>
<body>
<?php if (empty($slips)): ?>
    <div class="no-slip">No payslip available for this employee in the selected period (no rate set or employee not found).</div>
<?php endif; ?>

<?php /* Portrait letter: 2 payslips per sheet (top + bottom half). One employee = one half-sheet payslip (top half). */ ?>
<?php foreach (array_chunk($slips, 2, true) as $sheet): ?>
<div class="sheet">
<?php foreach ($sheet as $emp_id => $s):
    $emp  = $s['employee'];
    $ded  = $s['deductions'];
    $ca   = $s['cash_advance'] ?? 0;
    $basic = max(0, $s['gross_pay'] - $s['overtime_pay']);
    $total_ded = $ca_only ? $ca : $ded['total'] + $ca;
    $earnings_total = $s['gross_pay'] + $s['bonus'] + $s['incentive'];
    $net_shown = $no_ded ? $earnings_total : ($ca_only ? $earnings_total - $ca : $s['net_pay']);
    $full_name = trim($emp['first_name'] . ' ' . $emp['last_name']);
?>
<div class="payslip">
    <div class="company-header">
        <div class="company-logo"><img src="../../assets/img/logo.jpg" alt="Logo"></div>
        <div class="company-left">
            <div class="company-name">TPD TECHNOLOGY PRODUCT TRADING &amp; SOLUTION</div>
            <div class="company-address">Unit 2 Climaco Compound, SunRiser Lane 2, Tugbungan Zamboanga City &nbsp;|&nbsp; Tel#: (062)-308-3148 &nbsp;|&nbsp; Cp#: 0993-306-8155</div>
        </div>
    </div>

    <div class="slip-title-row">
        <div class="slip-title">PAYSLIP</div>
        <div class="slip-period">Pay Period: <strong><?php echo date('M d, Y', strtotime($s['start_date'])) . ' &ndash; ' . date('M d, Y', strtotime($s['end_date'])); ?></strong></div>
    </div>

    <div class="info-wrap">
        <div class="emp-photo-box">
            <?php if (isset($photo_ids[(int)$emp['employee_id']])): ?>
                <img src="employee_photo.php?id=<?php echo (int)$emp['employee_id']; ?>&amp;v=<?php echo $photo_ids[(int)$emp['employee_id']]; ?>" alt="Employee photo">
            <?php else: ?>
                <span class="initials"><?php echo htmlspecialchars(strtoupper(mb_substr($emp['first_name'], 0, 1) . mb_substr($emp['last_name'], 0, 1))); ?></span>
            <?php endif; ?>
        </div>
        <table class="info">
            <tr><td class="k">Employee No.</td><td><?php echo htmlspecialchars($emp['employee_code'] ?? $emp['employee_id']); ?></td>
                <td class="k">Rate / Day</td><td><?php echo $peso($s['daily_rate']); ?></td></tr>
            <tr><td class="k">Name</td><td><strong><?php echo htmlspecialchars($full_name); ?></strong></td>
                <td class="k">Rate / Hour</td><td><?php echo $peso($s['hourly_rate']); ?></td></tr>
            <tr><td class="k">Position</td><td><?php echo htmlspecialchars($emp['position'] ?? ''); ?></td>
                <td class="k">Status</td><td><?php echo htmlspecialchars($emp['status'] ?? ''); ?></td></tr>
        </table>
    </div>

    <div class="cols">
        <div class="col">
            <div class="box-title">ATTENDANCE</div>
            <table class="amt">
                <tr><td>Days Worked</td><td class="r"><?php echo (int)$s['working_days']; ?></td></tr>
                <tr><td>&nbsp;&nbsp;Present / Late</td><td class="r"><?php echo (int)$s['present_days']; ?> / <?php echo (int)$s['late_days']; ?></td></tr>
                <tr><td>Half Day</td><td class="r"><?php echo (int)$s['half_days']; ?></td></tr>
                <tr><td>Absent</td><td class="r"><?php echo (int)$s['absent_days']; ?></td></tr>
                <tr><td>Hours Worked</td><td class="r"><?php echo number_format($s['total_hours'], 2); ?></td></tr>
                <tr><td>Overtime Hours</td><td class="r"><?php echo number_format($s['overtime_hours'], 2); ?></td></tr>
            </table>
        </div>

        <div class="col">
            <div class="box-title">EARNINGS</div>
            <table class="amt">
                <tr><td>Basic Pay</td><td class="r"><?php echo $peso($basic); ?></td></tr>
                <tr><td>Overtime Pay</td><td class="r"><?php echo $peso($s['overtime_pay']); ?></td></tr>
                <tr><td>Bonus</td><td class="r"><?php echo $peso($s['bonus']); ?></td></tr>
                <tr><td>Incentive</td><td class="r"><?php echo $peso($s['incentive']); ?></td></tr>
                <tr class="sub"><td>TOTAL EARNINGS</td><td class="r"><?php echo $peso($earnings_total); ?></td></tr>
            </table>
        </div>

        <?php if (!$no_ded): ?>
        <div class="col">
            <div class="box-title">DEDUCTIONS</div>
            <table class="amt">
                <?php if (!$ca_only): ?>
                <tr><td>SSS</td><td class="r"><?php echo $peso($ded['SSS']); ?></td></tr>
                <tr><td>Pag-IBIG</td><td class="r"><?php echo $peso($ded['Pag-IBIG']); ?></td></tr>
                <tr><td>PhilHealth</td><td class="r"><?php echo $peso($ded['PhilHealth']); ?></td></tr>
                <?php endif; ?>
                <tr><td>Cash Advance</td><td class="r"><?php echo $peso($ca); ?></td></tr>
                <tr class="sub"><td>TOTAL DEDUCTIONS</td><td class="r"><?php echo $peso($total_ded); ?></td></tr>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <div class="net-row"><span>NET PAY</span><span><?php echo $peso($net_shown); ?></span></div>

    <div class="sign-row">
        <div class="sign"><div class="line"></div>Prepared by</div>
        <div class="sign"><div class="line"></div>Employee Signature / Date Received</div>
    </div>
</div>
<?php endforeach; ?>
</div>
<?php endforeach; ?>

<?php if (!empty($_GET['auto_print']) && !empty($slips)): ?>
<script>window.onload = function () { setTimeout(function () { window.print(); }, 500); };</script>
<?php endif; ?>
</body>
</html>
