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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Validate input
    if ($username === '' || $email === '' || $password === '') {
        set_flash('error', 'Please fill all fields.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash('error', 'Invalid email.');
    } elseif ($password !== $confirm) {
        set_flash('error', 'Passwords do not match.');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=? LIMIT 1");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            set_flash('error', 'Username or email already exists.');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
            $stmt->execute([$username, $email, $hash]);
            set_flash('success', 'Registration successful. Please log in.');
            redirect('login.php');
        }
    }
}
?>

<h2>Register</h2>
<br>

<form method="post" class="form">

  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
  <label>Username <input type="text" name="username" required></label>
  <label>Email <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label>Confirm Password <input type="password" name="confirm" required></label>
  <button type="submit">Register</button>

</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
