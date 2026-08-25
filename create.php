<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Only logged-in users can create stories
require_login();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if ($title === '' || $content === '') {

        set_flash('error', 'Please enter both a story title and content.');

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO blogs (user_id, title, content) VALUES (?, ?, ?)"
        );

        $stmt->execute([
            current_user_id(),
            $title,
            $content
        ]);

        set_flash('success', 'Your story has been published.');

        redirect('myblogs.php');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="write-page">

    <div class="write-header">

        <span class="write-label">CREATE SOMETHING</span>

        <h2>Write Your Story</h2>

        <p>
            Share your ideas, experiences, and thoughts with the BlogNest community.
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
                    value="<?= sanitize($_POST['title'] ?? '') ?>"
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
                ><?= sanitize($_POST['content'] ?? '') ?></textarea>

            </div>


            <div class="write-actions">

                <span class="draft-note">
                    Your story will be published immediately.
                </span>

                <button type="submit" class="publish-btn">
                    Publish Story
                </button>

            </div>

        </form>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>