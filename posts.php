<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

$sql = 'SELECT posts.*, users.username AS author
        FROM posts
        JOIN users ON posts.user_id = users.id
        ORDER BY posts.created_at DESC';
$posts = $conn->query($sql);

require_once __DIR__ . '/includes/header.php';
?>

<h1>All Posts</h1>

<?php if (canCreate()): ?>
    <p><a href="create_post.php" class="btn">+ New Post</a></p>
<?php else: ?>
    <p class="muted">You are a guest &mdash; you can read posts but not create them.</p>
<?php endif; ?>

<?php if ($posts->num_rows === 0): ?>
    <p class="muted">No posts yet.</p>
<?php endif; ?>

<?php while ($post = $posts->fetch_assoc()): ?>
    <article class="card">
        <h2>
            <a href="view_post.php?id=<?= (int)$post['id'] ?>">
                <?= htmlspecialchars($post['title']) ?>
            </a>
        </h2>
        <p class="muted">
            by <?= htmlspecialchars($post['author']) ?>
            on <?= htmlspecialchars($post['created_at']) ?>
        </p>

        <p><?= nl2br(htmlspecialchars(substr($post['content'], 0, 180))) ?>&hellip;</p>

        <div class="actions">
            <a href="view_post.php?id=<?= (int)$post['id'] ?>" class="btn-sm">Read &amp; Comment</a>

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
<?php endwhile; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
