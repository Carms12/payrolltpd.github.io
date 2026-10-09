<?php
// Entry point: logout (root). Redirects to index.php.
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

$auth = new AuthController(null, 'index.php');
$auth->logout();
