<?php
// Logout from inside the admin pages. Redirects back to the login page (2 levels up).
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../controllers/AuthController.php';

$auth = new AuthController(null, '../../index.php');
$auth->logout();
