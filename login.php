<?php
require_once __DIR__ . '/includes/functions.php';

// Get flash message for this page
$flash = get_flash();

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
            set_flash('error', 'The email or password you entered is incorrect. Please try again.');
        }
    }
}
?>

<div class="auth-page">

    <div class="auth-card">

        <div class="auth-header">
            <span class="auth-label">WELCOME BACK</span>

            <h2>Sign in to BlogNest</h2>

            <p>
                Welcome back. Enter your details to continue.
            </p>
        </div>

        <?php if ($flash): ?>

    <div class="flash <?= sanitize($flash['type']) ?>">
        <?= sanitize($flash['msg']) ?>
    </div>

<?php endif; ?>

        <form method="post" class="auth-form" autocomplete="off">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= csrf_token() ?>"
            >

            <!-- Hidden fake field to prevent autofill -->
            <input type="text" style="display:none">


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="you@example.com"
                    required
                    autocomplete="new-email"
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="new-password"
                >

            </div>


            <button type="submit" class="auth-submit">
                Sign In
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Don't have an account?
                <a href="register.php">Sign Up</a>
            </p>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
