<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can like comments
require_login();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('error', 'Invalid request.');
    redirect('explore.php');
}

// Check CSRF token
check_csrf();

// Get IDs
$comment_id = filter_input(
    INPUT_POST,
    'comment_id',
    FILTER_VALIDATE_INT
);

$blog_id = filter_input(
    INPUT_POST,
    'blog_id',
    FILTER_VALIDATE_INT
);

if (!$comment_id || !$blog_id) {
    set_flash('error', 'Invalid comment.');
    redirect('explore.php');
}

// Make sure the comment belongs to this blog
$stmt = $pdo->prepare(
    "SELECT id
     FROM comments
     WHERE id = ?
     AND blog_id = ?
     LIMIT 1"
);

$stmt->execute([
    $comment_id,
    $blog_id
]);

$comment = $stmt->fetch();

if (!$comment) {
    set_flash('error', 'Comment not found.');
    redirect('single.php?id=' . $blog_id);
}


// Check whether the user already liked the comment
$stmt = $pdo->prepare(
    "SELECT id
     FROM comment_likes
     WHERE user_id = ?
     AND comment_id = ?
     LIMIT 1"
);

$stmt->execute([
    current_user_id(),
    $comment_id
]);

$existing_like = $stmt->fetch();


// Already liked → unlike
if ($existing_like) {

    $stmt = $pdo->prepare(
        "DELETE FROM comment_likes
         WHERE id = ?"
    );

    $stmt->execute([
        $existing_like['id']
    ]);

    set_flash(
        'success',
        'Comment like removed.'
    );


// Not liked → like
} else {

    $stmt = $pdo->prepare(
        "INSERT INTO comment_likes
         (user_id, comment_id)
         VALUES (?, ?)"
    );

    $stmt->execute([
        current_user_id(),
        $comment_id
    ]);

    set_flash(
        'success',
        'Comment liked.'
    );
}


// Return to the story
redirect('single.php?id=' . $blog_id . '#comments');