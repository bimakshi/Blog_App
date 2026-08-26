<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get search term
$search = trim($_GET['search'] ?? '');

// Get selected category
$selected_category = filter_input(
    INPUT_GET,
    'category',
    FILTER_VALIDATE_INT
);

// Fetch categories
$category_stmt = $pdo->query(
    "SELECT id, name
     FROM categories
     ORDER BY name ASC"
);

$categories = $category_stmt->fetchAll();

// Fetch all blogs
$stmt = $pdo->query(
    "SELECT
        b.id,
        b.title,
        b.created_at,
        b.image,
        u.username,
        c.id AS category_id,
        c.name AS category_name
     FROM blogs b
     JOIN users u
        ON b.user_id = u.id
     LEFT JOIN categories c
        ON b.category_id = c.id
     ORDER BY b.created_at DESC"
);

$blogs = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="latest-section">

    <!-- Explore Controls -->

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

        <span class="article-count" id="articleCount">
            <?= count($blogs) ?> articles
        </span>

    </div>

    <div class="explore-controls">

        <!-- Categories -->

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


        <!-- Search -->

        <div class="explore-search">

            <input
                type="text"
                id="storySearch"
                placeholder="Search stories..."
                autocomplete="off"
                value="<?= sanitize($search) ?>"
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

                    <!-- Blog Image -->

                    <?php if (!empty($b['image'])): ?>

                        <div class="blog-card-image">

                            <img
                                src="uploads/blogs/<?= sanitize($b['image']) ?>"
                                alt="<?= sanitize($b['title']) ?>"
                            >

                        </div>

                    <?php endif; ?>


                    <!-- Card Content -->

                    <div class="blog-card-content">

                        <!-- Article + Category -->

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


                        <!-- Title -->

                        <h4>

                            <a href="single.php?id=<?= $b['id'] ?>">
                                <?= sanitize($b['title']) ?>
                            </a>

                        </h4>


                        <!-- Author + Date -->

                        <div class="blog-card-meta">

                            <span>
                                By <?= sanitize($b['username']) ?>
                            </span>

                            <span>•</span>

                            <span>
                                <?= date(
                                    'M d, Y',
                                    strtotime($b['created_at'])
                                ) ?>
                            </span>

                        </div>


                        <!-- Read More -->

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
const categoryButtons = document.querySelectorAll('.category-filter');
const noSearchResults = document.getElementById('noSearchResults');
const articleCount = document.getElementById('articleCount');

let selectedCategory = 'all';


function filterBlogs() {

    const searchText = searchInput
        ? searchInput.value.toLowerCase().trim()
        : '';

    let visibleCount = 0;


    blogCards.forEach(function (card) {

        const title = card.dataset.title
            ? card.dataset.title.toLowerCase()
            : '';

        const author = card.dataset.author
            ? card.dataset.author.toLowerCase()
            : '';

        const category = card.dataset.category || '';


        const matchesSearch =
            title.includes(searchText) ||
            author.includes(searchText);


        const matchesCategory =
            selectedCategory === 'all' ||
            category === selectedCategory;


        if (matchesSearch && matchesCategory) {

            card.style.display = '';
            visibleCount++;

        } else {

            card.style.display = 'none';

        }

    });


    // Update article count

    if (articleCount) {

        articleCount.textContent =
            visibleCount +
            (visibleCount === 1 ? ' article' : ' articles');

    }


    // Show empty state

    if (
        noSearchResults &&
        visibleCount === 0
    ) {

        noSearchResults.style.display = 'block';

    } else if (noSearchResults) {

        noSearchResults.style.display = 'none';

    }

}


/* Search */

if (searchInput) {

    searchInput.addEventListener(
        'input',
        filterBlogs
    );

}


/* Category buttons */

categoryButtons.forEach(function (button) {

    button.addEventListener(
        'click',
        function () {

            // Remove active from all buttons

            categoryButtons.forEach(function (btn) {

                btn.classList.remove('active');

            });


            // Add active to clicked button

            this.classList.add('active');


            // Get selected category

            selectedCategory =
                this.dataset.category;


            filterBlogs();

        }
    );

});

</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>