<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$post_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    'SELECT posts.*, users.username AS author
     FROM posts
     JOIN users ON posts.user_id = users.id
     WHERE posts.id = ?'
);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    require_once __DIR__ . '/includes/header.php';
    echo '<p class="error">Post not found.</p>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$stmt = $conn->prepare(
    'SELECT comments.*, users.username AS author
     FROM comments
     JOIN users ON comments.user_id = users.id
     WHERE comments.post_id = ?
     ORDER BY comments.created_at ASC'
);
$stmt->bind_param('i', $post_id);
$stmt->execute();
$comments = $stmt->get_result();

require_once __DIR__ . '/includes/header.php';
?>

<article class="card">
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p class="muted">
        by <?= htmlspecialchars($post['author']) ?>
        on <?= htmlspecialchars($post['created_at']) ?>
    </p>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

    <div class="actions">
        <?php if (canEditPost($post)): ?>
            <a href="edit_post.php?id=<?= (int)$post['id'] ?>" class="btn-sm">Edit</a>
        <?php endif; ?>
        <?php if (canDeletePost($post)): ?>
            <form method="post" action="delete_post.php" data-confirm="Delete this post?" style="display:inline">
                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                <button type="submit" class="btn-sm danger">Delete</button>
            </form>
        <?php endif; ?>
    </div>
</article>

<h2>Comments</h2>

<?php if ($comments->num_rows === 0): ?>
    <p class="muted">No comments yet.</p>
<?php endif; ?>

<?php while ($comment = $comments->fetch_assoc()): ?>
    <div class="card comment">
        <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
        <p class="muted">
            by <?= htmlspecialchars($comment['author']) ?>
            on <?= htmlspecialchars($comment['created_at']) ?>
        </p>

        <?php if (canDeleteComment($comment, $post)): ?>
            <form method="post" action="delete_comment.php" data-confirm="Delete this comment?" style="display:inline">
                <input type="hidden" name="id" value="<?= (int)$comment['id'] ?>">
                <button type="submit" class="btn-sm danger">Delete</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<?php if (canCreate()): ?>
    <h3>Add a comment</h3>
    <form method="post" action="add_comment.php" class="card form">
        <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
        <textarea name="content" rows="3" required placeholder="Write a comment..."></textarea>
        <button type="submit" class="btn">Comment</button>
    </form>
<?php else: ?>
    <p class="muted">Guests cannot comment.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
