<?php
require_once __DIR__ . '/auth_helpers.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'That email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, "user", "active")');
            $stmt->execute([$name, $email, $hash]);
            flash('success', 'Registration successful. Please log in.');
            header('Location: login.php');
            exit;
        }
    }
}
require_once __DIR__ . '/header.php';
?>
<div class="auth-card fade-in">
    <h1>🍳 Join RecipeMind</h1>
    <p class="auth-subtitle">Create your account and start sharing recipes</p>
    <?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" action="register.php">
        <div class="field">
            <label for="name">Full Name</label>
            <input id="name" name="name" type="text" placeholder="Your name" value="<?= old('name') ?>" required>
        </div>
        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" placeholder="you@example.com" value="<?= old('email') ?>" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Create a password" required>
        </div>
        <div class="field">
            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password" required>
        </div>
        <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top:8px;">Create Account</button>
    </form>
    <p style="text-align:center;margin-top:24px;font-size:0.9rem;">Already have an account? <a class="small-link" href="login.php">Sign in</a></p>
</div>
<?php require_once __DIR__ . '/footer.php';
