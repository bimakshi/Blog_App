<?php
require_once __DIR__ . '/functions.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>BlogNest</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/Blog_App/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>

<body>

<header class="site-header">

    <div class="container">

        <h1 class="logo">BlogNest</h1>

        <nav class="nav">

    <?php if (is_logged_in()): ?>

        <a href="index.php" class="nav-link">Home</a>

        <a href="explore.php" class="nav-link">Explore</a>

        <a href="myblogs.php" class="nav-link">My Blogs</a>

        <a href="create.php" class="write-btn">
            Write a Story
        </a>

        <a href="logout.php" class="nav-link logout-link">
            Sign Out
        </a>

    <?php else: ?>

        <a href="index.php" class="nav-link">Home</a>

        <a href="explore.php" class="nav-link">Explore</a>

        <a href="login.php" class="nav-link">Sign In</a>

        <a href="register.php" class="write-btn">
            Sign Up
        </a>

    <?php endif; ?>

</nav>

    </div>

</header>

<main class="container">
