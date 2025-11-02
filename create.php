<?php
// create.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $title = trim($_POST['title'] ?? '');   
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if ($title === '' || $content === '') {
        set_flash('error', 'Title and content required.');  
    } else {   
        $stmt = $pdo->prepare("INSERT INTO blogs (user_id, title, content) VALUES (?,?,?)");   
        $stmt->execute([current_user_id(), $title, $content]);   
        set_flash('success', 'Blog created.');   
        header('Location: myblogs.php'); exit;   
    }
}
?>

<h2>Create Blog</h2>
<br>

<form method="post" class="form">

  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <label>Title <input type="text" name="title" required></label>
  <label>Content <textarea name="content" rows="10" required></textarea></label>
  <button type="submit">Create</button>
  
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
