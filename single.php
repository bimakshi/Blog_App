<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get blog ID from the URL
$blog_id = $_GET['id'] ?? null;

// Check that an ID was provided
if ($blog_id === null) {
    die('Blog not found.');
}

// Fetch the blog with category
$stmt = $pdo->prepare(
    "SELECT b.id, b.title, b.content, b.created_at,
            u.username,
            c.name AS category_name
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     LEFT JOIN categories c ON b.category_id = c.id
     WHERE b.id = ?"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch();

// Check whether the blog exists
if (!$blog) {
    die('Blog not found.');
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="single-blog">

    <a href="explore.php" class="back-link">
        ←  Back to Explore
    </a>


    <h2>
        <?= sanitize($blog['title']) ?>
    </h2>


    <div class="single-blog-info">

        <div class="single-blog-meta">

            <span>
                By <?= sanitize($blog['username']) ?>
            </span>

            <span>•</span>

            <span>
                <?= date('M d, Y', strtotime($blog['created_at'])) ?>
            </span>

        </div>


        <?php if (!empty($blog['category_name'])): ?>

            <span class="single-blog-category">
                <?= sanitize($blog['category_name']) ?>
            </span>

        <?php endif; ?>

    </div>


    <div class="single-blog-divider"></div>


    <div class="content">

        <?= nl2br(sanitize($blog['content'])) ?>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>