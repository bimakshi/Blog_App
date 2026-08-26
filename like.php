<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can like stories
require_login();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    set_flash(
        'error',
        'Invalid like request.'
    );

    redirect('explore.php');
}


// Check CSRF token
check_csrf();


// Get blog ID
$blog_id = filter_input(
    INPUT_POST,
    'blog_id',
    FILTER_VALIDATE_INT
);


// Validate blog ID
if (!$blog_id) {

    set_flash(
        'error',
        'Invalid story.'
    );

    redirect('explore.php');
}


// Check that the blog exists
$stmt = $pdo->prepare(
    "SELECT id
     FROM blogs
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch();

if (!$blog) {

    set_flash(
        'error',
        'Story not found.'
    );

    redirect('explore.php');
}


// Get current user
$user_id = current_user_id();


// Check whether the user already liked this blog
$stmt = $pdo->prepare(
    "SELECT id
     FROM likes
     WHERE user_id = ?
     AND blog_id = ?
     LIMIT 1"
);

$stmt->execute([
    $user_id,
    $blog_id
]);

$existing_like = $stmt->fetch();


// If already liked → remove like
if ($existing_like) {

    $stmt = $pdo->prepare(
        "DELETE FROM likes
         WHERE id = ?"
    );

    $stmt->execute([
        $existing_like['id']
    ]);

    set_flash(
        'success',
        'Like removed.'
    );


// If not liked → add like
} else {

    $stmt = $pdo->prepare(
        "INSERT INTO likes
         (user_id, blog_id)
         VALUES (?, ?)"
    );

    $stmt->execute([
        $user_id,
        $blog_id
    ]);

    set_flash(
        'success',
        'Story liked.'
    );
}


// Return to the same story
redirect('single.php?id=' . $blog_id);