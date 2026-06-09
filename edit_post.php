<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$post_id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $conn->prepare('SELECT * FROM posts WHERE id = ?');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    require_once __DIR__ . '/includes/header.php';
    echo '<p class="error">Post not found.</p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

if (!canEditPost($post)) {
    header('Location: posts.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if ($title === '' || $content === '') {
        $error = 'Both a title and content are required.';
    } else {
        $stmt = $conn->prepare('UPDATE posts SET title = ?, content = ? WHERE id = ?');
        $stmt->bind_param('ssi', $title, $content, $post_id);
        $stmt->execute();

        header('Location: view_post.php?id=' . $post_id);
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<h1>Edit Post</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="card form">
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
    <label>Title
        <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
    </label>
    <label>Content
        <textarea name="content" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
    </label>
    <button type="submit" class="btn">Save changes</button>
    <a href="view_post.php?id=<?= (int)$post['id'] ?>" class="btn-sm">Cancel</a>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
