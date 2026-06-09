<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

$roles = ['super_admin', 'moderator', 'regular_user', 'guest'];

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username and password are required.';
    } elseif (!in_array($role, $roles, true)) {
        $error = 'Please choose a valid role.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = 'That username is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $username, $hash, $role);
            $stmt->execute();

            $success = 'Account created as ' . roleLabel($role) . '! You can now log in.';
        }
    }
}

$selectedRole = $_POST['role'] ?? 'regular_user';

require_once __DIR__ . '/includes/header.php';
?>

<h1>Register</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<?php if ($success !== ''): ?>
    <p class="success"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post" class="card form">
    <label>Username
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <label>Role
        <select name="role" required>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r ?>" <?= $r === $selectedRole ? 'selected' : '' ?>>
                    <?= roleLabel($r) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn">Create account</button>
</form>

<p class="muted">Already have an account? <a href="login.php">Login here</a>.</p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
