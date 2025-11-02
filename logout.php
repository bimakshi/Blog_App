<?php
// logout.php
require_once __DIR__ . '/includes/functions.php';
session_start();
session_unset();
session_destroy();
setcookie('last_login', '', time() - 3600, '/');
set_flash('success', 'You have been logged out.');
header('Location: index.php');
exit;
