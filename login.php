<?php
require_once __DIR__ . '/includes/functions.php';
// Redirect if already logged in
if (is_logged_in()) {
    redirect('index.php');
}
require_once __DIR__ . '/includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if ($email === '' || $password === '') {
        set_flash('error', 'Please fill in both fields.');
    } else {   
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email=? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            set_flash('success', 'Login successful.');
            redirect('index.php');
        } else { 
            set_flash('error', 'Invalid credentials.');
        }
    }
}
?>

<h2>Log in</h2>
<br>
<form method="post" class="form" autocomplete="off">
  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  
  <!-- Hidden fake field to prevent autofill -->
  <input type="text" style="display:none">
  
  <label>Email <input type="email" name="email" required autocomplete="new-email"></label>
  <label>Password <input type="password" name="password" required autocomplete="new-password"></label>
  <button type="submit">Log in</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
