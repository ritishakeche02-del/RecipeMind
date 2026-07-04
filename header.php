<?php
require_once __DIR__ . '/auth_helpers.php';
$user = currentUser();
$isAdminArea = strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false;
$currentPage = basename($_SERVER['SCRIPT_NAME'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RecipeMind<?= $isAdminArea ? ' — Admin' : '' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/recipemind/style.css">
</head>
<body>
<header>
    <!-- Left: Logo -->
    <a class="logo" href="/recipemind/index.php">
        <span class="logo-mark">R</span>
        <span class="logo-text">Recipe<span>Mind</span></span>
    </a>

    <!-- Center: Navigation -->
    <nav class="nav-center">
        <?php if (!$isAdminArea): ?>
            <a href="/recipemind/index.php" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                <span class="nav-icon">🏠</span> Home
            </a>
            <a href="/recipemind/recipes.php" class="nav-link <?= $currentPage === 'recipes' ? 'active' : '' ?>">
                <span class="nav-icon">📚</span> Recipes
            </a>
            <?php if ($user): ?>
                <a href="/recipemind/favorites.php" class="nav-link <?= $currentPage === 'favorites' ? 'active' : '' ?>">
                    <span class="nav-icon">❤️</span> Favorites
                </a>
                <a href="/recipemind/my_recipes.php" class="nav-link <?= $currentPage === 'my_recipes' ? 'active' : '' ?>">
                    <span class="nav-icon">👨‍🍳</span> My Recipes
                </a>
                <a href="/recipemind/new_recipe_request.php" class="nav-link <?= $currentPage === 'new_recipe_request' ? 'active' : '' ?>">
                    <span class="nav-icon">✍️</span> Submit
                </a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/recipemind/admin/dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="/recipemind/admin/users.php" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                <span class="nav-icon">👥</span> Users
            </a>
            <a href="/recipemind/admin/recipes.php" class="nav-link <?= $currentPage === 'recipes' ? 'active' : '' ?>">
                <span class="nav-icon">📖</span> Recipes
            </a>
            <a href="/recipemind/admin/requests.php" class="nav-link <?= $currentPage === 'requests' ? 'active' : '' ?>">
                <span class="nav-icon">📬</span> Requests
            </a>
            <a href="/recipemind/index.php" class="nav-link" style="opacity:0.6;">
                <span class="nav-icon">←</span> Back to Site
            </a>
        <?php endif; ?>
    </nav>

    <!-- Right: User Actions -->
    <div class="nav-right">
        <?php if ($user): ?>
            <div class="av-pill">
                <span class="av-pip"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                <?= htmlspecialchars($user['name']) ?>
            </div>
            <?php if ($user['role'] === 'admin' && !$isAdminArea): ?>
                <a href="/recipemind/admin/dashboard.php" class="btn btn-ghost" style="padding:8px 14px;font-size:0.82rem;">⚙️ Admin</a>
            <?php endif; ?>
            <a href="/recipemind/logout.php" class="btn btn-ghost" style="padding:8px 14px;font-size:0.82rem;color:var(--red);">Logout</a>
        <?php else: ?>
            <a href="/recipemind/login.php" class="btn btn-ghost" style="padding:8px 16px;">Sign In</a>
            <a href="/recipemind/register.php" class="btn btn-accent" style="padding:8px 18px;font-size:0.85rem;">Sign Up</a>
        <?php endif; ?>
    </div>

    <!-- Mobile Menu Toggle -->
    <button class="mobile-toggle" onclick="document.querySelector('.nav-center').classList.toggle('open')">☰</button>
</header>
<main class="main">
<?php if ($msg = flash('success')): ?>
    <div class="flash success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div class="flash error"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>
