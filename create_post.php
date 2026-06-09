<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if (!canCreate()) {
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
        $user_id = currentUserId();
        $stmt = $conn->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $user_id, $title, $content);
        $stmt->execute();

        header('Location: posts.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<h1>New Post</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="card form">
    <label>Title
        <input type="text" name="title" required>
    </label>
    <label>Content
        <textarea name="content" rows="6" required></textarea>
    </label>
    <button type="submit" class="btn">Publish</button>
    <a href="posts.php" class="btn-sm">Cancel</a>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
