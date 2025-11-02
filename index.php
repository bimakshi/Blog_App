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

<h2>Latest Blogs</h2>
<br>

<?php if (count($blogs) === 0): ?>
  <p>No blogs yet.</p>
<?php else: ?>
  <ul class="blog-list">
    <?php foreach ($blogs as $b): ?>
      <li>
        <a class="blog-title" href="single.php?id=<?= $b['id'] ?>"> 
          <?= sanitize($b['title']) ?>
        </a>
        <div class="meta">by <?= sanitize($b['username']) ?> on <?= sanitize($b['created_at']) ?></div> 
      </li>
      <br>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
