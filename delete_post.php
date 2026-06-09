<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

$post_id = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($post && canDeletePost($post)) {
    $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
}

header('Location: posts.php');
exit;
