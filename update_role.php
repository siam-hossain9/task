<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if (!canManageUsers() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

$id   = (int)($_POST['id'] ?? 0);
$role = $_POST['role'] ?? '';

$validRoles = ['super_admin', 'moderator', 'regular_user', 'guest'];

if ($id !== currentUserId() && in_array($role, $validRoles, true)) {
    $stmt = $conn->prepare('UPDATE users SET role = ? WHERE id = ?');
    $stmt->bind_param('si', $role, $id);
    $stmt->execute();
}

header('Location: users.php');
exit;
