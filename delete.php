<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Invalid delete request.');
    redirect('myblogs.php');
}

// Check CSRF token
check_csrf();

// Get blog ID
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('error', 'Invalid story.');
    redirect('myblogs.php');
}


// Get the blog and make sure it belongs to the logged-in user
$stmt = $pdo->prepare(
    "SELECT image
     FROM blogs
     WHERE id = ?
     AND user_id = ?"
);

$stmt->execute([
    $id,
    current_user_id()
]);

$blog = $stmt->fetch();


// Blog doesn't exist or doesn't belong to this user
if (!$blog) {

    set_flash(
        'error',
        'Story not found or you do not have permission to delete it.'
    );

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


// Delete the associated image
if (!empty($blog['image'])) {

    $image_path =
        __DIR__ . '/uploads/blogs/' . $blog['image'];

    if (file_exists($image_path)) {
        unlink($image_path);
    }
}


set_flash(
    'success',
    'Your story has been deleted.'
);

redirect('myblogs.php');