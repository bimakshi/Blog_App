<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can delete comments
require_login();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    set_flash(
        'error',
        'Invalid request.'
    );

    redirect('explore.php');
}

// Check CSRF token
check_csrf();

// Get comment ID
$comment_id = filter_input(
    INPUT_POST,
    'comment_id',
    FILTER_VALIDATE_INT
);

// Get blog ID
$blog_id = filter_input(
    INPUT_POST,
    'blog_id',
    FILTER_VALIDATE_INT
);

// Validate IDs
if (!$comment_id || !$blog_id) {

    set_flash(
        'error',
        'Invalid comment.'
    );

    redirect('explore.php');
}

// Delete only the comment belonging to the current user
$stmt = $pdo->prepare(
    "DELETE FROM comments
     WHERE id = ?
     AND user_id = ?
     AND blog_id = ?"
);

$stmt->execute([
    $comment_id,
    current_user_id(),
    $blog_id
]);

if ($stmt->rowCount() > 0) {

    set_flash(
        'success',
        'Comment deleted.'
    );

} else {

    set_flash(
        'error',
        'Comment not found or you do not have permission to delete it.'
    );
}

// Return to the same story
redirect('single.php?id=' . $blog_id . '#comments');