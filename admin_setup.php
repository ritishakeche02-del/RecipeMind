<?php
require_once __DIR__ . '/db.php';

// Check if setup has already been done
$adminCount = $pdo->query('SELECT COUNT(*) FROM users WHERE role = "admin"')->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm_password'] ?? '');
    $error = null;
    $success = null;

    if ($email === '' || $password === '' || $confirm === '') {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'This email is already registered.';
        } else {
            // Create admin user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, role, status) VALUES (?, ?, ?, "admin", "active")');
            $stmt->execute(['Admin', $email, $hash]);
            $success = 'Admin account created successfully! You can now log in.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeMind - Admin Setup</title>
    <link rel="stylesheet" href="/recipemind/style.css">
</head>
<body>
<header>
    <a class="logo" href="/recipemind/index.php">
        <span class="logo-mark">R</span>
        <span class="logo-text">RecipeMind</span>
    </a>
</header>
<main class="main">
    <div class="card" style="max-width:500px;margin:40px auto;">
        <h1 style="text-align:center;margin-bottom:32px;">🔐 Admin Setup</h1>

        <?php if ($adminCount > 0): ?>
            <div style="text-align:center;padding:48px;background:rgba(45,156,111,0.08);border-radius:var(--radius-lg);border:2px solid rgba(45,156,111,0.3);">
                <p style="font-size:1.1rem;color:var(--text);margin-bottom:12px;">✅ Admin account already exists</p>
                <p style="color:var(--t2);margin-bottom:24px;">Your admin panel has already been set up.</p>
                <a href="/recipemind/login.php" class="btn btn-accent btn-lg">Go to Login</a>
            </div>
        <?php else: ?>
            <div style="text-align:center;margin-bottom:28px;">
                <p style="color:var(--t2);font-size:0.95rem;">Create your admin account to manage the RecipeMind platform</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="flash success" style="margin-bottom:24px;">✅ <?= htmlspecialchars($success) ?></div>
                <div style="text-align:center;">
                    <a href="/recipemind/login.php" class="btn btn-accent btn-lg">Login to Admin Panel</a>
                </div>
            <?php else: ?>
                <?php if (isset($error)): ?>
                    <div class="flash error" style="margin-bottom:24px;">❌ <?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="admin_setup.php">
                    <div class="field">
                        <label for="email">Admin Email</label>
                        <input id="email" name="email" type="email" placeholder="admin@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label for="password">Admin Password</label>
                        <input id="password" name="password" type="password" placeholder="Enter a strong password" required>
                        <small>Minimum 6 characters</small>
                    </div>

                    <div class="field">
                        <label for="confirm_password">Confirm Password</label>
                        <input id="confirm_password" name="confirm_password" type="password" placeholder="Confirm your password" required>
                    </div>

                    <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top:24px;">Create Admin Account</button>
                </form>

                <div style="margin-top:28px;padding:16px;background:rgba(212,165,116,0.08);border-radius:var(--radius);border-left:4px solid var(--accent);">
                    <p style="margin:0;color:var(--t2);font-size:0.85rem;">
                        <strong>ℹ️ This setup page:</strong> This page creates the first admin account only once. For security, it cannot be used again after the first admin is created.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>
</body>
</html>
