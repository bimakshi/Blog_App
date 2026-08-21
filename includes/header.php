<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BlogNest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<header class="site-header">

    <div class="container">

        <!-- Logo -->
        <a href="<?= BASE_URL ?>" class="logo">
            BlogNest
        </a>


        <!-- Navigation -->
        <nav class="nav">

            <?php if (is_logged_in()): ?>

                <!-- Navigation for logged-in users -->

                <a href="index.php" class="nav-link">
                    Home
                </a>

                <a href="#" class="nav-link">
                    Explore
                </a>

                <a href="myblogs.php" class="nav-link">
                    My Blogs
                </a>

                <a href="create.php" class="write-btn">
                    Write a Story
                </a>

                <a href="logout.php" class="nav-link logout-link">
                    Log out
                </a>

            <?php else: ?>

                <!-- Navigation for visitors -->

                <a href="index.php" class="nav-link">
                    Home
                </a>

                <a href="#" class="nav-link">
                    Explore
                </a>

                <a href="login.php" class="nav-link">
                    Log in
                </a>

                <a href="register.php" class="write-btn">
                    Create Account
                </a>

            <?php endif; ?>

        </nav>

    </div>

</header>


<main class="container">

<?php if ($flash): ?>

    <div class="flash <?= sanitize($flash['type']) ?>">
        <?= sanitize($flash['msg']) ?>
    </div>

<?php endif; ?>