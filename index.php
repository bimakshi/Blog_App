<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/header.php';

// Fetch latest blogs
$stmt = $pdo->query("SELECT b.id, b.title, b.created_at, u.username 
                     FROM blogs b JOIN users u ON b.user_id = u.id
                     ORDER BY b.created_at DESC");
$blogs = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero">

    <span class="hero-label">WELCOME TO BLOGNEST</span>

    <h2>Discover ideas.<br>Share your stories.</h2>

    <p>
        A place to read, write, and share stories that matter.
    </p>

    <a href="#latest-blogs" class="hero-btn">
        Explore Articles <span>→</span>
    </a>

</section>


<!-- Latest Blogs -->
<section id="latest-blogs" class="latest-section">

    <div class="section-heading">
        <div>
            <span class="section-label">FROM OUR COMMUNITY</span>
            <h3>Latest Articles</h3>
        </div>

        <span class="article-count">
            <?= count($blogs) ?> articles
        </span>
    </div>


    <?php if (count($blogs) === 0): ?>

        <div class="empty-state">
            <h4>No stories yet</h4>
            <p>Be the first person to share a story with the community.</p>

            <a href="create.php" class="write-btn">
                Write the first story
            </a>
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
