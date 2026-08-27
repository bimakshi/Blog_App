<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get blog ID
$blog_id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

// Check blog ID
if (!$blog_id) {
    set_flash('error', 'Story not found.');
    redirect('explore.php');
}

// Fetch blog
$stmt = $pdo->prepare(
    "SELECT
        b.id,
        b.title,
        b.content,
        b.created_at,
        b.image,
        u.username,
        c.name AS category_name
     FROM blogs b
     JOIN users u
        ON b.user_id = u.id
     LEFT JOIN categories c
        ON b.category_id = c.id
     WHERE b.id = ?"
);

$stmt->execute([$blog_id]);

$blog = $stmt->fetch();

// Check blog exists
if (!$blog) {
    set_flash('error', 'Story not found.');
    redirect('explore.php');
}


// =====================================================
// STORY LIKES
// =====================================================

// Get story like count
$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM likes
     WHERE blog_id = ?"
);

$stmt->execute([$blog_id]);

$like_count = (int) $stmt->fetchColumn();


// Check whether current user liked the story
$user_liked = false;

if (is_logged_in()) {

    $stmt = $pdo->prepare(
        "SELECT id
         FROM likes
         WHERE blog_id = ?
         AND user_id = ?
         LIMIT 1"
    );

    $stmt->execute([
        $blog_id,
        current_user_id()
    ]);

    $user_liked = (bool) $stmt->fetch();
}


// =====================================================
// COMMENTS
// =====================================================

// Fetch comments with like counts
$stmt = $pdo->prepare(
    "SELECT
        c.id,
        c.user_id,
        c.blog_id,
        c.parent_id,
        c.comment,
        c.created_at,
        u.username,
        COUNT(cl.id) AS like_count,

        EXISTS (
            SELECT 1
            FROM comment_likes user_cl
            WHERE user_cl.comment_id = c.id
            AND user_cl.user_id = ?
        ) AS user_liked

     FROM comments c

     JOIN users u
        ON c.user_id = u.id

     LEFT JOIN comment_likes cl
        ON c.id = cl.comment_id

     WHERE c.blog_id = ?

     GROUP BY
        c.id,
        c.user_id,
        c.blog_id,
        c.parent_id,
        c.comment,
        c.created_at,
        u.username

     ORDER BY c.created_at ASC"
);

$stmt->execute([
    is_logged_in() ? current_user_id() : 0,
    $blog_id
]);

$comments = $stmt->fetchAll();


// Organize comments and replies
$comment_tree = [];

foreach ($comments as $comment) {

    if ($comment['parent_id'] === null) {

        $comment_tree[$comment['id']] = [
            'comment' => $comment,
            'replies' => []
        ];
    }
}


// Add replies under their parent comment
foreach ($comments as $comment) {

    if ($comment['parent_id'] !== null) {

        if (isset($comment_tree[$comment['parent_id']])) {

            $comment_tree[$comment['parent_id']]['replies'][] = $comment;
        }
    }
}


$comment_count = count($comments);


require_once __DIR__ . '/includes/header.php';
?>


<section class="single-blog">

    <!-- Back -->

    <a href="explore.php" class="back-link">
        ← Back to Explore
    </a>


    <!-- Article Label + Category -->

    <div class="single-blog-top">

        <span class="single-blog-label">
            ARTICLE
        </span>

        <?php if (!empty($blog['category_name'])): ?>

            <span class="single-blog-category">
                <?= sanitize($blog['category_name']) ?>
            </span>

        <?php endif; ?>

    </div>


    <!-- Title -->

    <h2>
        <?= sanitize($blog['title']) ?>
    </h2>


    <!-- Author + Date -->

    <div class="meta">

        <span>
            By <?= sanitize($blog['username']) ?>
        </span>

        <span>•</span>

        <span>
            <?= date('M d, Y', strtotime($blog['created_at'])) ?>
        </span>

    </div>


    <!-- Cover Image -->

    <?php if (!empty($blog['image'])): ?>

        <div class="single-blog-image">

            <img
                src="uploads/blogs/<?= sanitize($blog['image']) ?>"
                alt="<?= sanitize($blog['title']) ?>"
            >

        </div>

    <?php endif; ?>


    <!-- Story Content -->

    <div class="content">

        <?= nl2br(sanitize($blog['content'])) ?>

    </div>


    <!-- =================================================
         STORY SOCIAL ACTIONS
         ================================================= -->

    <div
        id="story-like"
        class="story-social-section"
    >

        <div class="story-action-buttons">

            <?php if (is_logged_in()): ?>

                <form
                    method="post"
                    action="like.php"
                    class="story-action-form"
                >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= csrf_token() ?>"
                    >

                    <input
                        type="hidden"
                        name="blog_id"
                        value="<?= $blog_id ?>"
                    >

                    <button
                        type="submit"
                        class="story-action-btn <?= $user_liked ? 'liked' : '' ?>"
                    >

                        <span>
                            <?= $user_liked ? '♥' : '♡' ?>
                        </span>

                        <?= $like_count ?>
                        <?= $like_count === 1 ? 'Like' : 'Likes' ?>

                    </button>

                </form>

            <?php else: ?>

                <a
                    href="login.php"
                    class="story-action-btn"
                >
                    ♡ Like
                </a>

            <?php endif; ?>

        </div>

    </div>


    <!-- =================================================
         COMMENTS
         ================================================= -->

    <section
        id="comments"
        class="comments-section"
    >

        <div class="comments-heading">

            <div>

                <h3>
                    Comments
                </h3>

                <span>
                    <?= $comment_count ?>
                    <?= $comment_count === 1 ? 'comment' : 'comments' ?>
                </span>

            </div>

        </div>


        <!-- New Comment -->

        <?php if (is_logged_in()): ?>

            <form
                method="post"
                action="comment.php"
                class="comment-form"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= csrf_token() ?>"
                >

                <input
                    type="hidden"
                    name="blog_id"
                    value="<?= $blog_id ?>"
                >

                <div class="comment-form-row">

                    <textarea
                        name="comment"
                        maxlength="1000"
                        placeholder="Write a comment..."
                        required
                    ></textarea>

                    <button
                        type="submit"
                        class="comment-submit-btn"
                    >
                        Post Comment
                    </button>

                </div>

            </form>

        <?php else: ?>

            <div class="comment-login-message">

                <p>

                    <a href="login.php">
                        Sign in
                    </a>

                    to join the conversation.

                </p>

            </div>

        <?php endif; ?>


        <!-- Comments List -->

        <div class="comments-list">

            <?php if (count($comment_tree) === 0): ?>

                <div class="no-comments">

                    <p>
                        No comments yet. Be the first to share your thoughts.
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($comment_tree as $item): ?>

                    <?php $comment = $item['comment']; ?>


                    <!-- Main Comment -->

                    <article
                        id="comment-<?= $comment['id'] ?>"
                        class="comment-card"
                    >

                        <div class="comment-header">

                            <div class="comment-user">

                                <strong>
                                    <?= sanitize($comment['username']) ?>
                                </strong>

                            </div>


                            <div class="comment-date">

                                <?= date(
                                    'M d, Y',
                                    strtotime($comment['created_at'])
                                ) ?>


                                <?php if (
                                    is_logged_in() &&
                                    $comment['user_id'] == current_user_id()
                                ): ?>

                                    <form
                                        method="post"
                                        action="delete_comment.php"
                                        class="comment-delete-form"
                                        onsubmit="return openCommentDeleteModal(this);"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= csrf_token() ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="comment_id"
                                            value="<?= $comment['id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="blog_id"
                                            value="<?= $blog_id ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="comment-delete-btn"
                                            title="Delete comment"
                                            aria-label="Delete comment"
                                        >
                                            🗑
                                        </button>

                                    </form>

                                <?php endif; ?>

                            </div>

                        </div>


                        <p class="comment-text">

                            <?= nl2br(
                                sanitize($comment['comment'])
                            ) ?>

                        </p>


                        <!-- Comment Actions -->

                        <div class="comment-actions">

                            <?php if (is_logged_in()): ?>

                                <form
                                    method="post"
                                    action="comment_like.php"
                                    class="comment-like-form"
                                >

                                    <input
                                        type="hidden"
                                        name="csrf_token"
                                        value="<?= csrf_token() ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="comment_id"
                                        value="<?= $comment['id'] ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="blog_id"
                                        value="<?= $blog_id ?>"
                                    >

                                    <button
                                        type="submit"
                                        class="comment-action-btn <?= $comment['user_liked'] ? 'liked' : '' ?>"
                                    >

                                        <span class="comment-heart">
                                            <?= $comment['user_liked'] ? '♥' : '♡' ?>
                                        </span>

                                        <?= (int) $comment['like_count'] ?>

                                        <?= (int) $comment['like_count'] === 1
                                            ? 'Like'
                                            : 'Likes'
                                        ?>

                                    </button>

                                </form>

                            <?php else: ?>

                                <a
                                    href="login.php"
                                    class="comment-action-btn"
                                >
                                    ♡ Like
                                </a>

                            <?php endif; ?>


                            <?php if (is_logged_in()): ?>

                                <button
                                    type="button"
                                    class="comment-action-btn reply-toggle"
                                    onclick="toggleReplyForm(<?= $comment['id'] ?>)"
                                >
                                    Reply
                                </button>

                            <?php endif; ?>

                        </div>


                        <!-- Reply Form -->

                        <?php if (is_logged_in()): ?>

                            <form
                                method="post"
                                action="comment.php"
                                class="reply-form"
                                id="reply-form-<?= $comment['id'] ?>"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= csrf_token() ?>"
                                >

                                <input
                                    type="hidden"
                                    name="blog_id"
                                    value="<?= $blog_id ?>"
                                >

                                <input
                                    type="hidden"
                                    name="parent_id"
                                    value="<?= $comment['id'] ?>"
                                >

                                <textarea
                                    name="comment"
                                    rows="2"
                                    maxlength="1000"
                                    placeholder="Write a reply..."
                                    required
                                ></textarea>

                                <div class="reply-actions">

                                    <button
                                        type="button"
                                        class="reply-cancel-btn"
                                        onclick="toggleReplyForm(<?= $comment['id'] ?>)"
                                    >
                                        Cancel
                                    </button>

                                    <button
                                        type="submit"
                                        class="reply-submit-btn"
                                    >
                                        Reply
                                    </button>

                                </div>

                            </form>

                        <?php endif; ?>


                        <!-- Replies -->

                        <?php if (count($item['replies']) > 0): ?>

                            <div class="comment-replies">

                                <?php foreach ($item['replies'] as $reply): ?>

                                    <article
                                        id="comment-<?= $reply['id'] ?>"
                                        class="comment-card reply-card"
                                    >

                                        <div class="comment-header">

                                            <div class="comment-user">

                                                <strong>
                                                    <?= sanitize($reply['username']) ?>
                                                </strong>

                                            </div>


                                            <div class="comment-date">

                                                <?= date(
                                                    'M d, Y',
                                                    strtotime($reply['created_at'])
                                                ) ?>


                                                <?php if (
                                                    is_logged_in() &&
                                                    $reply['user_id'] == current_user_id()
                                                ): ?>

                                                    <form
                                                        method="post"
                                                        action="delete_comment.php"
                                                        class="comment-delete-form"
                                                        onsubmit="return openCommentDeleteModal(this);"
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="csrf_token"
                                                            value="<?= csrf_token() ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="comment_id"
                                                            value="<?= $reply['id'] ?>"
                                                        >

                                                        <input
                                                            type="hidden"
                                                            name="blog_id"
                                                            value="<?= $blog_id ?>"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="comment-delete-btn"
                                                            title="Delete reply"
                                                            aria-label="Delete reply"
                                                        >
                                                            🗑
                                                        </button>

                                                    </form>

                                                <?php endif; ?>

                                            </div>

                                        </div>


                                        <p class="comment-text">

                                            <?= nl2br(
                                                sanitize($reply['comment'])
                                            ) ?>

                                        </p>


                                        <!-- Reply Like -->

                                        <div class="comment-actions">

                                            <?php if (is_logged_in()): ?>

                                                <form
                                                    method="post"
                                                    action="comment_like.php"
                                                    class="comment-like-form"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= csrf_token() ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="comment_id"
                                                        value="<?= $reply['id'] ?>"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="blog_id"
                                                        value="<?= $blog_id ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="comment-action-btn <?= $reply['user_liked'] ? 'liked' : '' ?>"
                                                    >

                                                        <span class="comment-heart">
                                                            <?= $reply['user_liked'] ? '♥' : '♡' ?>
                                                        </span>

                                                        <?= (int) $reply['like_count'] ?>

                                                        <?= (int) $reply['like_count'] === 1
                                                            ? 'Like'
                                                            : 'Likes'
                                                        ?>

                                                    </button>

                                                </form>

                                            <?php else: ?>

                                                <a
                                                    href="login.php"
                                                    class="comment-action-btn"
                                                >
                                                    ♡ Like
                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </article>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </article>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</section>


<script>

function toggleReplyForm(commentId) {

    const form = document.getElementById(
        'reply-form-' + commentId
    );

    if (!form) {
        return;
    }

    if (form.style.display === 'block') {

        form.style.display = 'none';

    } else {

        form.style.display = 'block';

        const textarea = form.querySelector('textarea');

        if (textarea) {
            textarea.focus();
        }
    }
}


// =====================================================
// COMMENT DELETE MODAL
// =====================================================

let commentDeleteForm = null;


function openCommentDeleteModal(form) {

    commentDeleteForm = form;

    const modal = document.getElementById(
        'commentDeleteModal'
    );

    if (modal) {
        modal.classList.add('show');
    }

    return false;
}


function closeCommentDeleteModal() {

    const modal = document.getElementById(
        'commentDeleteModal'
    );

    if (modal) {
        modal.classList.remove('show');
    }

    commentDeleteForm = null;
}


function confirmCommentDelete() {

    if (commentDeleteForm) {

        commentDeleteForm.submit();

    }
}

</script>


<!-- Comment Delete Confirmation Modal -->

<div
    id="commentDeleteModal"
    class="delete-modal"
>

    <div class="delete-modal-content">

        <h3>
            Delete Comment?
        </h3>

        <p>
            Are you sure you want to delete this comment?
            This action cannot be undone.
        </p>

        <div class="delete-modal-actions">

            <button
                type="button"
                class="delete-cancel-btn"
                onclick="closeCommentDeleteModal()"
            >
                Cancel
            </button>

            <button
                type="button"
                class="delete-confirm-btn"
                onclick="confirmCommentDelete()"
            >
                Delete
            </button>

        </div>

    </div>

</div>

<script>
window.addEventListener('beforeunload', function () {
    sessionStorage.setItem('singleBlogScrollPosition', window.scrollY);
});

window.addEventListener('load', function () {
    const savedPosition = sessionStorage.getItem('singleBlogScrollPosition');

    if (savedPosition !== null) {
        window.scrollTo(0, parseInt(savedPosition, 10));
        sessionStorage.removeItem('singleBlogScrollPosition');
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>