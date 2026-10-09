<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in · TPD Payroll</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
<div class="bg-grid"></div>
<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>
<main class="login">
    <section class="brand">
        <svg class="wave" viewBox="0 0 600 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true" focusable="false">
            <g fill="none" stroke="#fff" stroke-linecap="round">
                <path d="M-40 620 C 140 520, 300 700, 420 560 S 600 360, 660 420" stroke-opacity=".16" stroke-width="2"/>
                <path d="M-40 680 C 150 580, 310 760, 440 620 S 610 430, 670 490" stroke-opacity=".12" stroke-width="2"/>
                <path d="M-40 740 C 160 640, 320 820, 460 680 S 620 500, 680 560" stroke-opacity=".09" stroke-width="2"/>
                <path d="M-60 120 C 100 40, 240 200, 360 110 S 540 -20, 640 40" stroke-opacity=".10" stroke-width="2"/>
            </g>
            <circle cx="520" cy="140" r="170" fill="#fff" fill-opacity=".05"/>
        </svg>
        <div class="brand-inner">
            <img class="brand-logo" src="assets/img/logo.jpg" alt="TPD logo">
            <p class="brand-name">TPD Technology Product Trading &amp; Solutions</p>
            <p class="brand-note">Attendance, salary and cash advance records for TPD staff.</p>
        </div>
    </section>

    <section class="panel">
        <div class="form-wrap">
            <h1>Sign in</h1>
            <p class="lead">Use your admin username or email.</p>

            <?php if (!empty($_GET['expired']) && !isset($_SESSION['error'])): ?>
                <div class="alert notice" role="status">Your session ended. Sign in again to continue.</div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert error" role="alert"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" autocomplete="off">
                <input type="hidden" name="login_token" value="<?php echo htmlspecialchars($loginToken ?? ''); ?>">

                <div class="field">
                    <label for="username">Username or email</label>
                    <div class="control">
                        <input type="text" id="username" name="username" autocomplete="username" autocapitalize="none" spellcheck="false" autofocus required>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="control has-toggle">
                        <input type="password" id="password" name="password" autocomplete="current-password" required>
                        <button type="button" class="toggle" id="togglePassword" aria-label="Show password" aria-pressed="false">Show</button>
                    </div>
                    <div class="hint" id="capsHint" aria-live="polite"></div>
                </div>

                <button type="submit" class="submit">Sign in</button>
            </form>

            <p class="fine">Authorized staff only. You are signed out after <?php echo (int)(SESSION_IDLE_TIMEOUT / 60); ?> minutes of inactivity.</p>
        </div>
    </section>
</main>
<script src="assets/js/login.js"></script>
</body>
</html>
