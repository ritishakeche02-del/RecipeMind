<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';
$search = trim($_GET['q'] ?? '');
$filterCuisine = trim($_GET['cuisine'] ?? '');

$cuisinesStmt = $pdo->query('SELECT DISTINCT cuisine FROM recipes WHERE status = "published" AND cuisine IS NOT NULL AND cuisine != "" ORDER BY cuisine ASC');
$availableCuisines = $cuisinesStmt->fetchAll(PDO::FETCH_COLUMN);

$sql = 'SELECT id, title, description, cuisine, image_url FROM recipes WHERE status = "published"';
$params = [];

if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR description LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

if ($filterCuisine !== '') {
    $sql .= ' AND cuisine = ?';
    $params[] = $filterCuisine;
}

$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recipes = $stmt->fetchAll();
?>
<!-- Page Header -->
<div class="page-hero fade-in">
    <h1>📚 All Recipes</h1>
    <p style="font-size:1rem;color:var(--t2);max-width:500px;margin:0 auto;margin-bottom:28px;">
        Browse our complete collection of delicious recipes shared by our community
    </p>
    
    <form method="get" action="recipes.php" style="display:flex;flex-wrap:wrap;justify-content:center;max-width:700px;margin:0 auto;gap:10px;">
        <input type="text" name="q" placeholder="Search recipes..." value="<?= htmlspecialchars($search) ?>" style="flex:1;min-width:200px;padding:13px 18px;border:2px solid var(--bd1);border-radius:var(--radius);outline:none;font-size:0.95rem;font-family:inherit;background:rgba(255,255,255,0.8);transition:all .3s;" onfocus="this.style.borderColor='var(--accent)';this.style.boxShadow='0 0 0 4px rgba(200,149,108,.1)'" onblur="this.style.borderColor='var(--bd1)';this.style.boxShadow='none'">
        
        <select name="cuisine" style="padding:13px 16px;border:2px solid var(--bd1);border-radius:var(--radius);outline:none;font-size:0.95rem;font-family:inherit;background:rgba(255,255,255,0.8);cursor:pointer;transition:all .3s;" onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--bd1)'">
            <option value="">All Cuisines</option>
            <?php foreach ($availableCuisines as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $filterCuisine === $c ? 'selected' : '' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-accent">🔍 Search</button>
        <?php if ($search !== '' || $filterCuisine !== ''): ?>
            <a href="recipes.php" class="btn btn-outline" style="padding:12px 18px;">✕ Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($recipes)): ?>
    <div class="empty-state fade-in fade-in-delay-1">
        <span class="empty-icon">🍽️</span>
        <h3><?= ($search !== '' || $filterCuisine !== '') ? 'No recipes match your search' : 'No recipes have been published yet' ?></h3>
        <p><?= ($search !== '' || $filterCuisine !== '') ? 'Try a different search term or cuisine filter' : 'Be the first to share a delicious recipe!' ?></p>
        <?php if ($search !== '' || $filterCuisine !== ''): ?>
            <a href="recipes.php" class="btn btn-accent">View All Recipes</a>
        <?php else: ?>
            <a href="new_recipe_request.php" class="btn btn-accent">Submit a Recipe</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div style="margin-bottom:20px;color:var(--t2);font-size:0.9rem;display:flex;align-items:center;gap:8px;" class="fade-in fade-in-delay-1">
        <strong style="color:var(--text);"><?= count($recipes) ?></strong> recipe<?= count($recipes) !== 1 ? 's' : '' ?> found
        <?php if ($filterCuisine !== ''): ?>
            <span class="cuisine-tag"><?= htmlspecialchars($filterCuisine) ?></span>
        <?php endif; ?>
    </div>
    
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
                    <p><?= nl2br(htmlspecialchars(substr($recipe['description'], 0, 240))) ?></p>
                    <a class="btn btn-accent" href="recipe_detail.php?id=<?= $recipe['id'] ?>" style="margin-top:auto;">View details →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/footer.php';
