<?php
session_start();
require_once __DIR__ . '/db.php';

function currentUser() {
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    static $user = null;
    if ($user === null) {
        global $pdo;
        $stmt = $pdo->prepare('SELECT id, name, email, role, status FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user) {
            logout();
            return null;
        }
    }
    return $user;
}

function requireLogin() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    $user = currentUser();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo '<h1>Access denied</h1><p>You must be an administrator to view this page.</p>';
        exit;
    }
}

function logout() {
    session_unset();
    session_destroy();
}

function flash($name, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$name] = $message;
        return;
    }
    if (!empty($_SESSION['flash'][$name])) {
        $msg = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return $msg;
    }
    return null;
}

function old($key) {
    return htmlspecialchars($_POST[$key] ?? '');
}

function uploadRecipeImage($fileInput) {
    // Check if file was uploaded
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $file = $_FILES[$fileInput];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($fileType, $allowedTypes)) {
        return false;
    }

    // Validate file size (max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        return false;
    }

    // Create unique filename
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid('recipe_') . '.' . strtolower($ext);
    $uploadDir = __DIR__ . '/uploads/images/';
    
    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadPath = $uploadDir . $newFileName;

    // Move uploaded file
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        return 'uploads/images/' . $newFileName;
    }

    return false;
}
