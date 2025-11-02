<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash(); 
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Blog Nest</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
<header class="site-header">
  <div class="container">
    <h1 class="logo"><a href="<?= BASE_URL ?>">Blog Nest</a></h1>
    <nav class="nav">
      <?php if (is_logged_in()): ?>
        <a href="index.php">Home</a>
        <a href="myblogs.php">My Blogs</a>
        <a href="logout.php">Log out</a>
      <?php else: ?>
        <a href="register.php">Register</a>
        <a href="login.php">Log in</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">

<?php if ($flash): ?>
  <div class="flash <?= sanitize($flash['type']) ?>"><?= sanitize($flash['msg']) ?></div>
<?php endif; ?>
