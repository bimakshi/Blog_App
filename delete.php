<?php
// delete.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();

// Get blog ID from query parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT user_id FROM blogs WHERE id = ? LIMIT 1");   
$stmt->execute([$id]);   
$blog = $stmt->fetch();

// Check if blog exists
if (!$blog) {
    set_flash('error', 'Blog not found.');   
    header('Location: myblogs.php'); exit;   
}

// Authorization check
if ($blog['user_id'] != current_user_id()) {   
    set_flash('error', 'Not authorized.');     
    header('Location: myblogs.php'); exit;    
}

// Delete the blog
$stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");   
$stmt->execute([$id]);  
set_flash('success', 'Blog deleted.');  
header('Location: myblogs.php'); exit;   
?>
