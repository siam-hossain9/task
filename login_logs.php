<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';
requireLogin();

if (!canManageUsers()) {
    header('Location: posts.php');
    exit;
}

$logs = $conn->query('SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 100');

require_once __DIR__ . '/includes/header.php';
?>

<h1>Login History</h1>
<p class="muted">Every successful login is saved here with its login type. Only the Super Admin can see this page.</p>

<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Login type</th>
        <th>Time</th>
    </tr>

    <?php if ($logs->num_rows === 0): ?>
        <tr><td colspan="4" class="muted">No logins recorded yet.</td></tr>
    <?php endif; ?>

    <?php while ($row = $logs->fetch_assoc()): ?>
        <tr>
            <td><?= (int)$row['id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars(roleLabel($row['login_type'])) ?></td>
            <td><?= htmlspecialchars($row['login_time']) ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
