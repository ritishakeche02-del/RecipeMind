<?php
require_once __DIR__ . '/auth_helpers.php';
requireLogin();
$user = currentUser();
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cuisine = trim($_POST['cuisine'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $imageUrl = '';

    if ($title === '' || $ingredients === '' || $instructions === '') {
        $error = 'Title, ingredients, and instructions are required.';
    } else {
        // Handle image upload
        if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['size'] > 0) {
            $uploadedImage = uploadRecipeImage('recipe_image');
            if ($uploadedImage === false) {
                $error = 'Image upload failed. Please use a valid image (JPG, PNG, GIF, WebP) under 5MB.';
            } else {
                $imageUrl = $uploadedImage;
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare('INSERT INTO recipe_requests (user_id, title, description, ingredients, instructions, cuisine, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, "pending")');
            $stmt->execute([$user['id'], $title, $description, $ingredients, $instructions, $cuisine, $imageUrl]);
            flash('success', 'Recipe request submitted. An admin will review it shortly.');
            header('Location: index.php');
            exit;
        }
    }
}
require_once __DIR__ . '/header.php';
?>

<!-- Page Header -->
<div style="background:linear-gradient(135deg, rgba(212,165,116,0.1) 0%, rgba(255,107,74,0.05) 100%);padding:40px;border-radius:20px;margin-bottom:32px;text-align:center;">
    <h1 style="font-size:2.2rem;margin-bottom:12px;">✍️ Submit a Recipe</h1>
    <p style="font-size:1rem;color:var(--t2);max-width:500px;margin:0 auto;">
        Share your favorite recipe with our community. Our admin team will review and publish it soon!
    </p>
</div>

<!-- Form Card -->
<div class="card" style="max-width:700px;margin:0 auto;">
    <?php if ($error): ?>
        <div class="flash error" style="margin-bottom:24px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form method="post" action="new_recipe_request.php" enctype="multipart/form-data">
        <!-- Title Section -->
        <div class="field">
            <label for="title">🍴 Recipe Title <span style="color:var(--red);">*</span></label>
            <input id="title" name="title" type="text" placeholder="e.g., Homemade Pasta Carbonara" value="<?= old('title') ?>" required style="font-size:1rem;padding:14px;">
        </div>

        <!-- Cuisine Section -->
        <div class="field">
            <label for="cuisine">🌍 Cuisine</label>
            <input id="cuisine" name="cuisine" type="text" placeholder="e.g., Italian, Mexican, Indian" value="<?= old('cuisine') ?>" style="font-size:1rem;padding:14px;">
        </div>

        <!-- Description -->
        <div class="field">
            <label for="description">📝 Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Tell us about your recipe..." style="font-size:0.95rem;"><?= old('description') ?></textarea>
        </div>

        <!-- Image Upload -->
        <div class="field" style="padding:16px;background:var(--s2);border-radius:var(--radius);border:2px dashed var(--bd2);">
            <label for="recipe_image">📸 Recipe Image (Optional)</label>
            <input id="recipe_image" name="recipe_image" type="file" accept="image/jpeg,image/png,image/gif,image/webp" style="background:transparent;padding:8px 0;">
            <small style="color:var(--t3);margin-top:8px;">
                💡 Supported formats: JPG, PNG, GIF, WebP | Max size: 5MB
            </small>
        </div>

        <!-- Ingredients -->
        <div class="field">
            <label for="ingredients">🥘 Ingredients <span style="color:var(--red);">*</span></label>
            <textarea id="ingredients" name="ingredients" rows="5" placeholder="List ingredients one per line" style="font-size:0.95rem;" required><?= old('ingredients') ?></textarea>
            <small>Include quantities and measurements for each ingredient</small>
        </div>

        <!-- Instructions -->
        <div class="field">
            <label for="instructions">👨‍🍳 Cooking Instructions <span style="color:var(--red);">*</span></label>
            <textarea id="instructions" name="instructions" rows="6" placeholder="Step-by-step cooking instructions..." style="font-size:0.95rem;" required><?= old('instructions') ?></textarea>
            <small>Be detailed to help others follow your recipe</small>
        </div>

        <!-- Submit Button -->
        <div style="display:flex;gap:12px;margin-top:28px;padding-top:24px;border-top:2px solid var(--bd1);">
            <button type="submit" class="btn btn-accent btn-lg">📤 Submit Recipe</button>
            <a href="index.php" class="btn btn-ghost btn-lg">Cancel</a>
        </div>
    </form>

    <!-- Info Box -->
    <div style="margin-top:28px;padding:16px;background:rgba(212,165,116,0.08);border-radius:var(--radius);border-left:4px solid var(--accent);">
        <p style="margin:0;color:var(--t2);font-size:0.9rem;">
            <strong>ℹ️ Info:</strong> Your recipe will be reviewed by our admin team before being published to the community.
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php';
