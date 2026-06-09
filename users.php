<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if (!canManageUsers()) {
    header('Location: posts.php');
    exit;
}

$roles = ['super_admin', 'moderator', 'regular_user', 'guest'];

$users = $conn->query('SELECT id, username, role, created_at FROM users ORDER BY id ASC');

require_once __DIR__ . '/includes/header.php';
?>

<h1>Manage Users</h1>
<p class="muted">Only the Super Admin can see this page.</p>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Actions</th>
    </tr>

    <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['role']) ?></td>
            <td>
                <?php if ($u['id'] == currentUserId()): ?>
                    <span class="muted">(this is you)</span>
                <?php else: ?>
                    <div class="actions">

                        <form method="post" action="update_role.php" style="display:inline">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <select name="role">
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>>
                                        <?= $r ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn-sm">Update</button>
                        </form>

                        <form method="post" action="delete_user.php"
                              data-confirm="Delete this user and all their posts/comments?"
                              style="display:inline">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <button type="submit" class="btn-sm danger">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
