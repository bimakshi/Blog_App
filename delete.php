<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

// Only allow POST requests for deleting stories
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('myblogs.php');
}

// Check CSRF token
check_csrf();

// Get blog ID from POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('error', 'Invalid story.');
    redirect('myblogs.php');
}

// Check that the blog belongs to the logged-in user
$stmt = $pdo->prepare(
    "SELECT id
     FROM blogs
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $id,
    current_user_id()
]);

$blog = $stmt->fetch();

// Blog doesn't exist or doesn't belong to current user
if (!$blog) {
    set_flash('error', 'Story not found or you do not have permission to delete it.');
    redirect('myblogs.php');
}

// Delete the blog
$stmt = $pdo->prepare(
    "DELETE FROM blogs
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $id,
    current_user_id()
]);

redirect('myblogs.php');