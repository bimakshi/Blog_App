<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can comment
require_login();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    set_flash('error', 'Invalid comment request.');
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

// Get parent comment ID
$parent_id = filter_input(
    INPUT_POST,
    'parent_id',
    FILTER_VALIDATE_INT
);

// Get comment
$comment = trim($_POST['comment'] ?? '');


// Validate blog ID
if (!$blog_id) {

    set_flash('error', 'Invalid story.');
    redirect('explore.php');
}


// Validate comment
if ($comment === '') {

    set_flash(
        'error',
        'Please write a comment before posting.'
    );

    redirect('single.php?id=' . $blog_id);
}


// Limit comment length
if (strlen($comment) > 1000) {

    set_flash(
        'error',
        'Comment must be 1000 characters or less.'
    );

    redirect('single.php?id=' . $blog_id);
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


// If this is a reply, verify the parent comment
if ($parent_id) {

    $stmt = $pdo->prepare(
        "SELECT id
         FROM comments
         WHERE id = ?
         AND blog_id = ?
         LIMIT 1"
    );

    $stmt->execute([
        $parent_id,
        $blog_id
    ]);

    $parent_comment = $stmt->fetch();

    if (!$parent_comment) {

        set_flash(
            'error',
            'The comment you are replying to could not be found.'
        );

        redirect('single.php?id=' . $blog_id);
    }
}


// Add comment or reply
$stmt = $pdo->prepare(
    "INSERT INTO comments
     (user_id, blog_id, parent_id, comment)
     VALUES (?, ?, ?, ?)"
);

$stmt->execute([
    current_user_id(),
    $blog_id,
    $parent_id ?: null,
    $comment
]);


if ($parent_id) {

    set_flash(
        'success',
        'Your reply has been posted.'
    );

} else {

    set_flash(
        'success',
        'Your comment has been posted.'
    );
}


// Return to story
redirect('single.php?id=' . $blog_id . '#comments');