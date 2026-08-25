<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

// Get current user's blogs
$stmt = $pdo->prepare(
    "SELECT *
     FROM blogs
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->execute([current_user_id()]);
$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="my-blogs-page">

    <div class="my-blogs-heading">

        <div>
            <span class="section-label">
                YOUR WRITING
            </span>

            <h2>My Blogs</h2>

            <p>
                Manage and edit the stories you have shared with the BlogNest community.
            </p>
        </div>

        <a href="create.php" class="my-blogs-write-btn">
        Write a Story
        </a>

    </div>


    <?php if (count($blogs) === 0): ?>

        <div class="my-blogs-empty">

            <h3>You haven't written any stories yet.</h3>

            <p>
                Start sharing your ideas and stories with the BlogNest community.
            </p>

            <a href="create.php" class="my-blogs-write-btn">
                Write Your First Story
            </a>

        </div>

    <?php else: ?>

        <div class="my-blogs-grid">

            <?php foreach ($blogs as $b): ?>

                <article class="my-blog-card">

                    <div class="my-blog-card-content">

                        <span class="my-blog-label">
                            YOUR STORY
                        </span>

                        <h3>
                            <?= sanitize($b['title']) ?>
                        </h3>

                        <div class="my-blog-meta">

                            <span>
                                Created
                                <?= date('M d, Y', strtotime($b['created_at'])) ?>
                            </span>

                            <?php if (!empty($b['updated_at'])): ?>

                                <span>•</span>

                                <span>
                                    Updated
                                    <?= date('M d, Y', strtotime($b['updated_at'])) ?>
                                </span>

                            <?php endif; ?>

                        </div>

                        <p class="my-blog-preview">
                            <?= sanitize(substr($b['content'], 0, 250)) ?>

                            <?php if (strlen($b['content']) > 250): ?>
                                ...
                            <?php endif; ?>
                        </p>

                        <div class="my-blog-actions">

                            <a
                                href="single.php?id=<?= $b['id'] ?>"
                                class="my-blog-view"
                            >
                                View Story →
                            </a>

                            <a
                                href="edit.php?id=<?= $b['id'] ?>"
                                class="my-blog-edit"
                            >
                                Edit
                            </a>

                            <a
                                href="delete.php?id=<?= $b['id'] ?>"
                                class="my-blog-delete"
                                onclick="return confirm('Are you sure you want to delete this story?');"
                            >
                                Delete
                            </a>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>