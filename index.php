<?php
require_once __DIR__ . '/db.php';
$statement = $pdo->query('SELECT id, title, description, cuisine, image_url FROM recipes WHERE status = "published" ORDER BY created_at DESC LIMIT 6');
$recipes = $statement->fetchAll();
require_once __DIR__ . '/header.php';
?>

<!-- Hero Section -->
<div class="page-hero fade-in" style="padding:56px 40px;margin-bottom:40px;">
    <h1 style="font-size:2.8rem;margin-bottom:16px;">🍳 Welcome to RecipeMind</h1>
    <p style="font-size:1.1rem;color:var(--t2);max-width:600px;margin:0 auto;line-height:1.8;">
        Your ultimate recipe hub for discovering delicious ideas, sharing culinary creations, and exploring community favorites.
    </p>
    <div style="display:flex;gap:14px;justify-content:center;margin-top:28px;flex-wrap:wrap;">
        <a href="recipes.php" class="btn btn-accent btn-lg">🔍 Explore Recipes</a>
        <a href="new_recipe_request.php" class="btn btn-outline btn-lg" style="border-color:var(--accent);color:var(--accent);">✍️ Submit Recipe</a>
    </div>
</div>

<!-- Features Strip -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:40px;" class="fade-in fade-in-delay-1">
    <div style="text-align:center;padding:24px 16px;background:rgba(255,255,255,0.6);border-radius:16px;border:1px solid rgba(0,0,0,0.04);">
        <span style="font-size:2rem;display:block;margin-bottom:8px;">📚</span>
        <strong style="font-size:0.85rem;color:var(--text);">Browse Recipes</strong>
        <p style="font-size:0.78rem;margin-top:4px;">Discover dishes from around the world</p>
    </div>
    <div style="text-align:center;padding:24px 16px;background:rgba(255,255,255,0.6);border-radius:16px;border:1px solid rgba(0,0,0,0.04);">
        <span style="font-size:2rem;display:block;margin-bottom:8px;">❤️</span>
        <strong style="font-size:0.85rem;color:var(--text);">Save Favorites</strong>
        <p style="font-size:0.78rem;margin-top:4px;">Bookmark recipes you love</p>
    </div>
    <div style="text-align:center;padding:24px 16px;background:rgba(255,255,255,0.6);border-radius:16px;border:1px solid rgba(0,0,0,0.04);">
        <span style="font-size:2rem;display:block;margin-bottom:8px;">✍️</span>
        <strong style="font-size:0.85rem;color:var(--text);">Share Yours</strong>
        <p style="font-size:0.78rem;margin-top:4px;">Submit your own creations</p>
    </div>
    <div style="text-align:center;padding:24px 16px;background:rgba(255,255,255,0.6);border-radius:16px;border:1px solid rgba(0,0,0,0.04);">
        <span style="font-size:2rem;display:block;margin-bottom:8px;">🌍</span>
        <strong style="font-size:0.85rem;color:var(--text);">World Cuisines</strong>
        <p style="font-size:0.78rem;margin-top:4px;">Filter by cuisine type</p>
    </div>
</div>

<!-- Latest Recipes Section -->
<div class="fade-in fade-in-delay-2">
    <h2 style="margin-bottom:24px;display:flex;align-items:center;gap:10px;">
        <span style="font-size:1.6rem;">⭐</span> Latest Recipes
    </h2>
    
    <?php if (empty($recipes)): ?>
        <div class="empty-state">
            <span class="empty-icon">🍽️</span>
            <h3>No published recipes yet</h3>
            <p>Check back soon for delicious recipes!</p>
        </div>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <div class="recipe-image-container">
                        <?php if (!empty($recipe['image_url'])): ?>
                            <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['title']) ?>" class="recipe-image">
                        <?php else: ?>
                            <span class="recipe-placeholder">🍽️</span>
                        <?php endif; ?>
                    </div>
                    <div class="recipe-content">
                        <h2><?= htmlspecialchars($recipe['title']) ?></h2>
                        <?php if (!empty($recipe['cuisine'])): ?>
                            <span class="cuisine-tag"><?= htmlspecialchars($recipe['cuisine']) ?></span>
                        <?php endif; ?>
                        <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 120))) ?></p>
                        <a class="btn btn-accent" href="recipe_detail.php?id=<?= $recipe['id'] ?>" style="margin-top:12px;">View Recipe →</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align:center;margin-top:36px;">
            <a href="recipes.php" class="btn btn-outline btn-lg">View All Recipes →</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/footer.php';
