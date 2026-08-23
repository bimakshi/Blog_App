<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Fetch latest blogs
$stmt = $pdo->query(
    "SELECT b.id, b.title, b.created_at, u.username
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     ORDER BY b.created_at DESC"
);

$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">

    <div class="hero-content">

        <span class="hero-label">
            WELCOME TO BLOGNEST
        </span>

        <h2>
            Write. Share. <span>Discover.</span>
        </h2>

        <p>
            A simple place to share your ideas, tell your stories,
            and discover perspectives from other writers.
        </p>

        <div class="hero-actions">

            <?php if (is_logged_in()): ?>

                <a href="create.php" class="hero-btn primary">
                    Write a Story
                </a>

                <a href="explore.php" class="hero-btn secondary">
                    Explore Stories
                </a>

            <?php else: ?>

                <a href="register.php" class="hero-btn primary">
                    Start Writing
                </a>

                <a href="explore.php" class="hero-btn secondary">
                    Explore Stories
                </a>

            <?php endif; ?>

        </div>

    </div>

</section>


<!-- Latest Blogs -->
<section id="latest-blogs" class="latest-section">

    <div class="section-heading">

        <div>
            <span class="section-label">
                FROM OUR COMMUNITY
            </span>

            <h3>
                Latest Articles
            </h3>
        </div>

        <span class="article-count">
            <?= count($blogs) ?> articles
        </span>

    </div>


    <?php if (count($blogs) === 0): ?>

        <div class="empty-state">

            <h4>
                No stories yet
            </h4>

            <p>
                Be the first person to share a story with the community.
            </p>

            <?php if (is_logged_in()): ?>

                <a href="create.php" class="write-btn">
                    Write the first story
                </a>

            <?php else: ?>

                <a href="register.php" class="write-btn">
                    Start Writing
                </a>

            <?php endif; ?>

        </div>

    <?php else: ?>

        <div class="blog-grid">

            <?php foreach ($blogs as $b): ?>

                <article class="blog-card">

                    <div class="blog-card-content">

                        <span class="blog-card-label">
                            ARTICLE
                        </span>

                        <h4>

                            <a href="single.php?id=<?= $b['id'] ?>">
                                <?= sanitize($b['title']) ?>
                            </a>

                        </h4>

                        <div class="blog-card-meta">

                            <span>
                                By <?= sanitize($b['username']) ?>
                            </span>

                            <span>•</span>

                            <span>
                                <?= date('M d, Y', strtotime($b['created_at'])) ?>
                            </span>

                        </div>

                        <a
                            href="single.php?id=<?= $b['id'] ?>"
                            class="read-more"
                        >
                            Read article <span>→</span>
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>