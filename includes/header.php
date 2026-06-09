<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RBAC System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">RBAC&nbsp;System</div>
    <nav class="nav">
        <?php if (isLoggedIn()): ?>
            <a href="posts.php">Posts</a>

            <?php if (canCreate()): ?>
                <a href="create_post.php">New Post</a>
            <?php endif; ?>

            <?php if (canManageUsers()): ?>
                <a href="users.php">Users</a>
                <a href="login_logs.php">Login Logs</a>
            <?php endif; ?>

            <span class="role-badge">
                <?= htmlspecialchars($_SESSION['username']) ?>
                &middot; <?= htmlspecialchars(currentRole()) ?>
            </span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>
<main class="container">
