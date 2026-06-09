<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/db.php';

if (isLoggedIn()) {
    header('Location: posts.php');
    exit;
}

$roles = ['super_admin', 'moderator', 'regular_user', 'guest'];

$error        = '';
$selectedType = $_POST['login_type'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';
    $loginType = $_POST['login_type'] ?? '';

    $stmt = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($password, $user['password'])) {
        $error = 'Invalid username or password.';
    } elseif ($user['role'] !== $loginType) {
        $error = 'This account is not a "' . roleLabel($loginType) . '". Please choose the correct login type.';
    } else {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];

        $log = $conn->prepare('INSERT INTO login_logs (user_id, username, login_type) VALUES (?, ?, ?)');
        $log->bind_param('iss', $user['id'], $user['username'], $loginType);
        $log->execute();

        header('Location: posts.php');
        exit;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<h1>Login</h1>

<?php if ($error !== ''): ?>
    <p class="error"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" class="card form">
    <label>Username
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
    </label>
    <label>Password
        <input type="password" name="password" required>
    </label>
    <label>Login as (role)
        <select name="login_type" required>
            <option value="">-- choose your role --</option>
            <?php foreach ($roles as $r): ?>
                <option value="<?= $r ?>" <?= $r === $selectedType ? 'selected' : '' ?>>
                    <?= roleLabel($r) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit" class="btn">Login</button>
</form>

<p class="muted">No account yet? <a href="register.php">Register here</a>.</p>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
