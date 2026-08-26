<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get search term
$search = trim($_GET['search'] ?? '');

// Fetch all blogs
$stmt = $pdo->query(
    "SELECT b.id,
            b.title,
            b.created_at,
            b.image,
            u.username,
            c.name AS category_name
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     LEFT JOIN categories c ON b.category_id = c.id
     ORDER BY b.created_at DESC"
);

$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="latest-section">

    <!-- Search -->

    <div class="explore-search">

        <input
            type="text"
            id="storySearch"
            placeholder="Search stories..."
            autocomplete="off"
        >

    </div>


    <!-- Heading -->

    <div class="section-heading">

        <div>

            <span class="section-label">
                FROM OUR COMMUNITY
            </span>

            <h3>
                Explore Stories
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

                <article
                    class="blog-card"
                    data-title="<?= sanitize($b['title']) ?>"
                    data-author="<?= sanitize($b['username']) ?>"
                >

                    <!-- Blog Image -->

                    <?php if (!empty($b['image'])): ?>

                        <div class="blog-card-image">

                            <img
                                src="uploads/blogs/<?= sanitize($b['image']) ?>"
                                alt="<?= sanitize($b['title']) ?>"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="blog-card-content">

                        <div class="blog-card-top">

    <span class="blog-card-label">
        ARTICLE
    </span>

    <?php if (!empty($b['category_name'])): ?>

        <span class="blog-card-category">
            <?= sanitize($b['category_name']) ?>
        </span>

    <?php endif; ?>

</div>


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


        <!-- No search results -->

        <div
            id="noSearchResults"
            class="empty-state"
            style="display: none;"
        >

            <h4>
                No stories found
            </h4>

            <p>
                We couldn't find any stories matching your search.
            </p>

        </div>

    <?php endif; ?>

</section>


<script>

const searchInput = document.getElementById('storySearch');
const blogCards = document.querySelectorAll('.blog-card');
const noSearchResults = document.getElementById('noSearchResults');

if (searchInput) {

    searchInput.addEventListener('input', function () {

        const searchText = this.value.toLowerCase().trim();

        let visibleCount = 0;

        blogCards.forEach(function (card) {

            const title = card.dataset.title.toLowerCase();
            const author = card.dataset.author.toLowerCase();

            const matches =
                title.includes(searchText) ||
                author.includes(searchText);

            if (matches) {

                card.style.display = '';

                visibleCount++;

            } else {

                card.style.display = 'none';

            }

        });


        if (
            noSearchResults &&
            visibleCount === 0 &&
            searchText !== ''
        ) {

            noSearchResults.style.display = 'block';

        } else if (noSearchResults) {

            noSearchResults.style.display = 'none';

        }

    });

}

</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>