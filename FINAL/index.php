<?php
// Entry point: login page.
// Config (session, errors, DB) -> controller -> view.
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

ob_start();

$auth = new AuthController(getDBConnection(), 'index.php');

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth->handleLoginRequest('views/admin/dashboard.php');
}

// Opening the login page always means signing in again:
// any earlier login is ended here (no automatic jump to the dashboard).
$auth->forceFreshLogin();
$loginToken = $auth->loginToken();
sendNoCacheHeaders();

// Show the login page
include __DIR__ . '/views/auth/login.php';
