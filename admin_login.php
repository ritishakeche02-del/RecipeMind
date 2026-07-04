<?php
require_once __DIR__ . '/auth_helpers.php';
if (!empty($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header('Location: admin/dashboard.php');
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
        } elseif ($user['role'] !== 'admin') {
            $error = 'You do not have administrator privileges.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            flash('success', 'Admin login successful.');
            header('Location: admin/dashboard.php');
            exit;
        }
    }
}
require_once __DIR__ . '/header.php';
?>
<div class="auth-card fade-in" style="border-top:none;">
    <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg, var(--accent3), #7d76f0, var(--accent3));"></div>
    <div style="text-align:center;margin-bottom:24px;">
        <span style="display:inline-flex;align-items:center;justify-content:center;width:64px;height:64px;background:linear-gradient(135deg,var(--accent3),#7d76f0);border-radius:18px;font-size:28px;margin-bottom:12px;box-shadow:0 8px 24px rgba(108,92,231,0.25);">🔐</span>
        <h1 style="color:var(--accent3);font-size:1.6rem;">Admin Portal</h1>
        <p class="auth-subtitle">Sign in with administrator credentials</p>
    </div>
    <?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" action="admin_login.php">
        <div class="field">
            <label for="email">Admin Email</label>
            <input id="email" name="email" type="email" placeholder="admin@recipemind.com" value="<?= old('email') ?>" required>
        </div>
        <div class="field">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-accent btn-lg btn-block" style="background:linear-gradient(135deg, var(--accent3) 0%, #7d76f0 100%);box-shadow:0 4px 20px rgba(108,92,231,0.3);margin-top:12px;">Sign In to Admin Panel</button>
    </form>
    <p style="text-align:center;margin-top:20px;font-size:0.85rem;"><a href="/recipemind/index.php" style="color:var(--t3);">← Back to website</a></p>
</div>
<?php require_once __DIR__ . '/footer.php';
