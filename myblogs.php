<?php
// myblogs.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/header.php';

// get user's blogs
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([current_user_id()]);
$blogs = $stmt->fetchAll();
?>
<h2>My Blogs</h2>
<br>
<p><a href="create.php" class="btn">Add New Blog</a></p>
<br>

<?php if (count($blogs) === 0): ?>
  <p>You have not written any blogs yet.</p>
<?php else: ?>
  <?php foreach ($blogs as $b): ?>
    <div class="blog-item">
      <h3><?= sanitize($b['title']) ?></h3>
      <div class="meta">Created: <?= sanitize($b['created_at']) ?> | Updated: <?= sanitize($b['updated_at'] ?? '-') ?></div>
      <div class="content-preview"><?= nl2br(sanitize(substr($b['content'],0,400))) ?><?php if (strlen($b['content'])>400) echo '...'; ?></div>
      <p>
        <a href="edit.php?id=<?= $b['id'] ?>" class="btn">Edit</a>
        <a href="delete.php?id=<?= $b['id'] ?>" class="btn danger" onclick="return confirm('Delete this blog?');">Delete</a>
      </p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
