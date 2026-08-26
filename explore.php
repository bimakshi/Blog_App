<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get all categories
$categoryStmt = $pdo->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

$categories = $categoryStmt->fetchAll();


// Fetch all blogs with their categories
$stmt = $pdo->query(
    "SELECT b.id,
            b.title,
            b.created_at,
            u.username,
            c.name AS category_name,
            c.id AS category_id
     FROM blogs b
     JOIN users u ON b.user_id = u.id
     LEFT JOIN categories c ON b.category_id = c.id
     ORDER BY b.created_at DESC"
);

$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="latest-section">

    <!-- Section Heading -->

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

    <div class="explore-controls">

    <!-- Category Filters -->

    <?php if (count($categories) > 0): ?>

        <div class="category-filters">

            <button
                type="button"
                class="category-filter active"
                data-category="all"
            >
                All
            </button>

            <?php foreach ($categories as $category): ?>

                <button
                    type="button"
                    class="category-filter"
                    data-category="<?= $category['id'] ?>"
                >
                    <?= sanitize($category['name']) ?>
                </button>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

        <!-- Search -->

        <div class="explore-search">

            <input
                type="text"
                id="storySearch"
                placeholder="Search stories..."
                autocomplete="off"
            >

        </div>

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
                    data-category="<?= $b['category_id'] ?? '' ?>"
                >

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


        <!-- No Search / Filter Results -->

        <div
            id="noSearchResults"
            class="empty-state"
            style="display: none;"
        >

            <h4>
                No stories found
            </h4>

            <p>
                We couldn't find any stories matching your search or category.
            </p>

        </div>

    <?php endif; ?>


</section>


<script>

const searchInput = document.getElementById('storySearch');
const blogCards = document.querySelectorAll('.blog-card');
const categoryFilters = document.querySelectorAll('.category-filter');
const noSearchResults = document.getElementById('noSearchResults');

let selectedCategory = 'all';


function filterStories() {

    const searchText = searchInput.value.toLowerCase().trim();

    let visibleCount = 0;


    blogCards.forEach(function (card) {

        const title = card.dataset.title.toLowerCase();
        const author = card.dataset.author.toLowerCase();
        const category = card.dataset.category;


        // Check search
        const matchesSearch =
            title.includes(searchText) ||
            author.includes(searchText);


        // Check category
        const matchesCategory =
            selectedCategory === 'all' ||
            category === selectedCategory;


        // Show only if both conditions match
        if (matchesSearch && matchesCategory) {

            card.style.display = '';

            visibleCount++;

        } else {

            card.style.display = 'none';

        }

    });


    // Show "No stories found"
    if (visibleCount === 0) {

        noSearchResults.style.display = 'block';

    } else {

        noSearchResults.style.display = 'none';

    }

}


// Live search
searchInput.addEventListener('input', function () {

    filterStories();

});


// Category filter
categoryFilters.forEach(function (button) {

    button.addEventListener('click', function () {

        // Remove active state from all buttons
        categoryFilters.forEach(function (btn) {
            btn.classList.remove('active');
        });


        // Add active state to clicked button
        this.classList.add('active');


        // Get selected category
        selectedCategory = this.dataset.category;


        // Filter stories
        filterStories();

    });

});

</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
