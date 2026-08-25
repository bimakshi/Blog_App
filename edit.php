<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can edit stories
require_login();

// Get blog ID from URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Check if a valid ID was provided
if (!$id) {
    set_flash('error', 'Invalid story.');
    redirect('myblogs.php');
}

// Get the blog and make sure it belongs to the logged-in user
$stmt = $pdo->prepare(
    "SELECT *
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
    set_flash('error', 'Story not found or you do not have permission to edit it.');
    redirect('myblogs.php');
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check CSRF token
    check_csrf();

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if ($title === '' || $content === '') {

        set_flash('error', 'Please enter both a story title and content.');

    } else {

        // Update the blog
        $stmt = $pdo->prepare(
            "UPDATE blogs
             SET title = ?, content = ?, updated_at = NOW()
             WHERE id = ?
             AND user_id = ?"
        );

        $stmt->execute([
            $title,
            $content,
            $id,
            current_user_id()
        ]);

        set_flash('success', 'Your story has been updated.');

        redirect('myblogs.php');
    }

    // Keep the user's entered values if validation fails
    $blog['title'] = $title;
    $blog['content'] = $content;
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="write-page">

    <div class="write-header">

        <span class="write-label">EDIT YOUR STORY</span>

        <h2>Edit Story</h2>

        <p>
            Update your story and keep sharing your ideas with the BlogNest community.
        </p>

    </div>

    <div class="write-card">

        <form method="post" class="write-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>"
            >


            <div class="form-group">

                <label for="title">
                    Story Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    placeholder="Give your story a title..."
                    value="<?= sanitize($blog['title']) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="content">
                    Your Story
                </label>

                <textarea
                    id="content"
                    name="content"
                    rows="14"
                    placeholder="Start writing your story..."
                    required
                ><?= sanitize($blog['content']) ?></textarea>

            </div>


            <div class="write-actions">

                <span class="draft-note">
                    Your changes will be saved immediately.
                </span>

                <button type="submit" class="publish-btn">
                    Update Story
                </button>

            </div>

        </form>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>