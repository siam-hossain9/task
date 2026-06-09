<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

if (!canCreate()) {
    header('Location: posts.php');
    exit;
}

$post_id = (int)($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');

if ($post_id > 0 && $content !== '') {
    $user_id = currentUserId();
    $stmt = $conn->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
    $stmt->bind_param('iis', $post_id, $user_id, $content);
    $stmt->execute();
}

header('Location: view_post.php?id=' . $post_id);
exit;
