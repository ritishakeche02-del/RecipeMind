<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo '<div class="empty-state"><span class="empty-icon">⚠️</span><h3>Recipe not found</h3><p>Invalid recipe ID.</p></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}
$stmt = $pdo->prepare('SELECT r.*, u.name AS author_name FROM recipes r LEFT JOIN users u ON r.author_id = u.id WHERE r.id = ? AND r.status = "published"');
$stmt->execute([$id]);
$recipe = $stmt->fetch();
if (!$recipe) {
    echo '<div class="empty-state"><span class="empty-icon">🔍</span><h3>Recipe not found</h3><p>The requested recipe does not exist or is not published.</p></div>';
    require_once __DIR__ . '/footer.php';
    exit;
}

$isFavorited = false;
$user = currentUser();
if ($user) {
    $stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?');
    $stmt->execute([$user['id'], $id]);
    $isFavorited = (bool)$stmt->fetch();
}
?>

<!-- Recipe Header -->
<div class="card fade-in" style="margin-bottom:28px;padding:32px;">
    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;flex-wrap:wrap;gap:16px;">
        <div style="flex:1;min-width:200px;">
            <h1 style="margin-bottom:10px;font-size:2rem;"><?= htmlspecialchars($recipe['title']) ?></h1>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="color:var(--t3);font-size:0.9rem;">
                    <strong>👨‍🍳</strong> <?= htmlspecialchars($recipe['author_name'] ?? 'Anonymous Chef') ?>
                </span>
                <?php if (!empty($recipe['cuisine'])): ?>
                    <span class="cuisine-tag"><?= htmlspecialchars($recipe['cuisine']) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($user): ?>
            <form method="post" action="toggle_favorite.php">
                <input type="hidden" name="recipe_id" value="<?= $recipe['id'] ?>">
                <input type="hidden" name="redirect" value="recipe_detail.php?id=<?= $recipe['id'] ?>">
                <button type="submit" class="btn <?= $isFavorited ? 'btn-outline' : 'btn-accent' ?>" style="padding:10px 20px;">
                    <?= $isFavorited ? '❤️ Favorited' : '🤍 Add to Favorites' ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($recipe['image_url'])): ?>
        <div class="recipe-detail-image">
            <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-full-image">
        </div>
    <?php endif; ?>
</div>

<!-- Recipe Content in Sections -->
<?php if (!empty($recipe['description'])): ?>
    <div class="card fade-in fade-in-delay-1" style="margin-bottom:24px;">
        <h2 style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <span style="font-size:1.3rem;">📝</span> Description
        </h2>
        <p style="line-height:1.85;color:var(--t2);"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
    </div>
<?php endif; ?>

<?php if (!empty($recipe['ingredients'])): ?>
    <div class="card fade-in fade-in-delay-2" style="margin-bottom:24px;">
        <h2 style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <span style="font-size:1.3rem;">🥘</span> Ingredients
        </h2>
        <div style="background:rgba(200,149,108,0.04);padding:20px 24px;border-radius:var(--radius);border-left:4px solid var(--accent);">
            <p style="line-height:1.9;color:var(--text);white-space:pre-wrap;font-family:inherit;"><?= nl2br(htmlspecialchars($recipe['ingredients'])) ?></p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($recipe['instructions'])): ?>
    <div class="card fade-in fade-in-delay-3" style="margin-bottom:24px;">
        <h2 style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <span style="font-size:1.3rem;">👨‍🍳</span> Instructions
        </h2>
        <div style="background:rgba(108,92,231,0.03);padding:20px 24px;border-radius:var(--radius);border-left:4px solid var(--accent3);">
            <p style="line-height:1.95;color:var(--text);white-space:pre-wrap;font-family:inherit;"><?= nl2br(htmlspecialchars($recipe['instructions'])) ?></p>
        </div>
    </div>
<?php endif; ?>

<!-- Back Button -->
<div style="display:flex;gap:12px;margin-top:32px;" class="fade-in fade-in-delay-3">
    <a class="btn btn-outline btn-lg" href="recipes.php">← Back to Recipes</a>
    <a class="btn btn-ghost btn-lg" href="index.php">Home</a>
</div>

<?php require_once __DIR__ . '/footer.php';
