<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';

// Redirect logged-in users to home page
if (is_logged_in()) {
    redirect('index.php');
}
?>

<main class="container">
    <h1>Welcome to the Blog!</h1>
    <p>Discover amazing blogs. Please register or sign in to continue.</p>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
