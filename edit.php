<?php
// edit.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/header.php';

// Get blog ID from query parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;  
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ? LIMIT 1");  //Fetch blog to edit
$stmt->execute([$id]);
$blog = $stmt->fetch();

// Check if blog exists
if (!$blog) {
    set_flash('error', 'Blog not found.');
    header('Location: myblogs.php'); exit;
}

// Authorization check
if ($blog['user_id'] != current_user_id()) {
    set_flash('error', 'You are not authorized to edit this blog.');
    header('Location: myblogs.php'); exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if ($title === '' || $content === '') {
        set_flash('error', 'Title and content required.');
    } else {
        $stmt = $pdo->prepare("UPDATE blogs SET title=?, content=?, updated_at = NOW() WHERE id=?");
        $stmt->execute([$title, $content, $id]);
        set_flash('success', 'Blog updated.');
        header('Location: myblogs.php'); exit;
    }
}
?>

<h2>Edit Blog</h2>
<br>

<form method="post" class="form">

  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <label>Title <input type="text" name="title" required value="<?= sanitize($blog['title']) ?>"></label>
  <label>Content <textarea name="content" rows="10" required><?= sanitize($blog['content']) ?></textarea></label>
  <button type="submit">Update</button>

</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?> 
