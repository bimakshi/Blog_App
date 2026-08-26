<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

// Get current user's blogs with category and image
$stmt = $pdo->prepare(
    "SELECT b.id,
            b.title,
            b.content,
            b.image,
            b.created_at,
            b.updated_at,
            c.name AS category_name
     FROM blogs b
     LEFT JOIN categories c ON b.category_id = c.id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
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

                    <?php if (!empty($b['image'])): ?>

                        <div class="my-blog-card-image">

                            <img
                                src="uploads/blogs/<?= sanitize($b['image']) ?>"
                                alt="<?= sanitize($b['title']) ?>"
                            >

                        </div>

                    <?php endif; ?>


                    <div class="my-blog-card-content">

                        <div class="my-blog-card-top">

                            <span class="my-blog-label">
                                YOUR STORY
                            </span>

                            <?php if (!empty($b['category_name'])): ?>

                                <span class="my-blog-category">
                                    <?= sanitize($b['category_name']) ?>
                                </span>

                            <?php endif; ?>

                        </div>


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

                            <form
                                method="post"
                                action="delete.php"
                                class="my-blog-delete-form"
                                onsubmit="return openDeleteModal(this);"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= csrf_token() ?>"
                                >

                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= $b['id'] ?>"
                                >

                                <button
                                    type="submit"
                                    class="my-blog-delete"
                                >
                                    Delete
                                </button>

                            </form>

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</section>


<!-- Delete Confirmation Modal -->

<div id="deleteModal" class="delete-modal">

    <div class="delete-modal-content">

        <h3>
            Delete Story?
        </h3>

        <p>
            Are you sure you want to delete this story?
            This action cannot be undone.
        </p>

        <div class="delete-modal-actions">

            <button
                type="button"
                class="delete-cancel-btn"
                onclick="closeDeleteModal()"
            >
                Cancel
            </button>

            <button
                type="button"
                class="delete-confirm-btn"
                onclick="confirmDelete()"
            >
                Delete
            </button>

        </div>

    </div>

</div>


<script>

let deleteForm = null;

function openDeleteModal(form) {

    deleteForm = form;

    document.getElementById('deleteModal').classList.add('show');

    return false;
}

function closeDeleteModal() {

    document.getElementById('deleteModal').classList.remove('show');

    deleteForm = null;
}

function confirmDelete() {

    if (deleteForm) {
        deleteForm.submit();
    }

}

</script>


<?php require_once __DIR__ . '/includes/footer.php'; ?>