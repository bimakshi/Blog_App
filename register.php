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
        $stmt = $pdo->prepare(
            "SELECT id FROM users WHERE username=? OR email=? LIMIT 1"
        );

        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            set_flash('error', 'Username or email already exists.');

        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (username,email,password) VALUES (?,?,?)"
            );

            $stmt->execute([$username, $email, $hash]);

            set_flash(
                'success',
                'Registration successful. Please log in.'
            );

            redirect('login.php');
        }
    }
}

// Get flash message AFTER processing the form
$flash = get_flash();
?>

<section class="auth-page">

    <div class="auth-card">

        <div class="auth-header">

            <span class="auth-label">CREATE ACCOUNT</span>

            <h2>Join BlogNest</h2>

            <p>
                Create an account and start sharing your stories.
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


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Choose a username"
                    required
                    autocomplete="username"
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    autocomplete="email"
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
                    placeholder="Create a password"
                    required
                    autocomplete="new-password"
                >

            </div>


            <div class="form-group">

                <label for="confirm">
                    Confirm Password
                </label>

                <input
                    type="password"
                    id="confirm"
                    name="confirm"
                    placeholder="Confirm your password"
                    required
                    autocomplete="new-password"
                >

            </div>


            <button type="submit" class="auth-submit">
                Sign Up
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Already have an account?
                <a href="login.php">Sign In</a>
            </p>

        </div>

    </div>

</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
