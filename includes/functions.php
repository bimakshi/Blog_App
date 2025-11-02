<?php
require_once __DIR__ . '/config.php';

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

// Flash messages
function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type'=>$type, 'msg'=>$msg];
}

// Retrieve and clear flash message
function get_flash() {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

// Auth helpers
function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

// Require login
function require_login() {
    if (!is_logged_in()) {
        set_flash('error', 'You must be logged in.');
        redirect('login.php');
    }
}

// Get current user ID
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

// Sanitize output
function sanitize($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

// CSRF
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Check CSRF token
function check_csrf() {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        die('CSRF validation failed.');
    }
}
