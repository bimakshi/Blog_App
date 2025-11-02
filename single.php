<?php
// single.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Get blog ID from query parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    set_flash('error', 'Invalid blog ID.');
    header('Location: index.php');
    exit;
}

// Fetch blog details
$stmt = $pdo->prepare("SELECT b.*, u.username FROM blogs b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();
if (!$blog) {
    set_flash('error', 'Blog not found.');
    header('Location: index.php');
    exit;
}
?>

<article class="single-blog">
  <h2><?= sanitize($blog['title']) ?></h2>
  <div class="meta">by <?= sanitize($blog['username']) ?> on <?= sanitize($blog['created_at']) ?></div>
  <div class="content"><?= nl2br(sanitize($blog['content'])) ?></div>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
