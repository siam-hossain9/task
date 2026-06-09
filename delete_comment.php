<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: posts.php');
    exit;
}

$comment_id = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare('SELECT * FROM comments WHERE id = ?');
$stmt->bind_param('i', $comment_id);
$stmt->execute();
$comment = $stmt->get_result()->fetch_assoc();

if (!$comment) {
    header('Location: posts.php');
    exit;
}

$stmt = $conn->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->bind_param('i', $comment['post_id']);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if ($post && canDeleteComment($comment, $post)) {
    $stmt = $conn->prepare('DELETE FROM comments WHERE id = ?');
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();
}

header('Location: view_post.php?id=' . (int)$comment['post_id']);
exit;
