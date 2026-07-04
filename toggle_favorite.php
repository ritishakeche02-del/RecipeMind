<?php
require_once __DIR__ . '/auth_helpers.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = filter_input(INPUT_POST, 'recipe_id', FILTER_VALIDATE_INT);
    $redirect = $_POST['redirect'] ?? 'recipes.php';
    $user = currentUser();

    if ($recipe_id && $user) {
        $stmt = $pdo->prepare('SELECT id FROM favorites WHERE user_id = ? AND recipe_id = ?');
        $stmt->execute([$user['id'], $recipe_id]);
        $favorite = $stmt->fetch();

        if ($favorite) {
            $stmt = $pdo->prepare('DELETE FROM favorites WHERE id = ?');
            $stmt->execute([$favorite['id']]);
            flash('success', 'Recipe removed from favorites.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)');
            $stmt->execute([$user['id'], $recipe_id]);
            flash('success', 'Recipe added to favorites!');
        }
    }
    header('Location: ' . $redirect);
    exit;
}
header('Location: index.php');
exit;
