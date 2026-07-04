<?php
require_once __DIR__ . '/auth_helpers.php';
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $stmt = $pdo->prepare('SELECT id, password_hash, status, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] !== 'active') {
            $error = 'Your account is not active. Contact the administrator.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            flash('success', 'Login successful.');
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }
    }
}
require_once __DIR__ . '/header.php';
?>
<div class="auth-card fade-in">
    <h1>👋 Welcome Back</h1>
    <p class="auth-subtitle">Sign in to your RecipeMind account</p>
    <?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" action="login.php">
        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" name="email" type="email" placeholder="you@example.com" value="<?= old('email') ?>" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top:8px;">Sign In</button>
    </form>
    <p style="text-align:center;margin-top:24px;font-size:0.9rem;">Don't have an account? <a class="small-link" href="register.php">Create one</a></p>
</div>
<?php require_once __DIR__ . '/footer.php';
