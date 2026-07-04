<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth_helpers.php';
requireLogin();
$user = currentUser();

$stmt = $pdo->prepare('
    SELECT r.id, r.title, r.description, r.cuisine, r.image_url 
    FROM recipes r 
    JOIN favorites f ON r.id = f.recipe_id 
    WHERE f.user_id = ? AND r.status = "published"
    ORDER BY f.created_at DESC
');
$stmt->execute([$user['id']]);
$recipes = $stmt->fetchAll();

require_once __DIR__ . '/header.php';
?>
<div class="page-hero fade-in">
    <h1>❤️ My Favorites</h1>
    <p style="font-size:1rem;color:var(--t2);max-width:500px;margin:0 auto;">
        All your saved recipes in one place
    </p>
</div>

<?php if (empty($recipes)): ?>
    <div class="empty-state fade-in fade-in-delay-1">
        <span class="empty-icon">❤️</span>
        <h3>You haven't saved any recipes yet</h3>
        <p>Browse the collection and save your favorites!</p>
        <a href="recipes.php" class="btn btn-accent">Browse Recipes</a>
    </div>
<?php else: ?>
    <div class="grid fade-in fade-in-delay-1">
        <?php foreach ($recipes as $recipe): ?>
            <div class="recipe-card">
                <?php if (!empty($recipe['image_url'])): ?>
                    <div class="recipe-image-container">
                        <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
                    </div>
                <?php else: ?>
                    <div class="recipe-image-container recipe-no-image">
                        <span class="recipe-placeholder">🍽️</span>
                    </div>
                <?php endif; ?>
                <div class="recipe-content">
                    <h2><?= htmlspecialchars($recipe['title']) ?></h2>
                    <?php if (!empty($recipe['cuisine'])): ?>
                        <span class="cuisine-tag"><?= htmlspecialchars($recipe['cuisine']) ?></span>
                    <?php endif; ?>
                    <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 120))) ?></p>
                    <a class="btn btn-accent" href="recipe_detail.php?id=<?= $recipe['id'] ?>" style="margin-top:auto;">View details →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php';
