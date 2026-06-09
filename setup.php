<?php
require_once __DIR__ . '/config/db.php';

$check = $conn->query("SELECT id FROM users WHERE username = 'superadmin'");
if ($check && $check->num_rows > 0) {
    exit('Setup already done. Demo accounts exist. You can delete setup.php now. <a href="login.php">Go to login</a>.');
}

$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

$demoUsers = [
    'superadmin' => 'super_admin',
    'moderator'  => 'moderator',
    'alice'      => 'regular_user',
    'bob'        => 'regular_user',
    'charlie'    => 'regular_user',
    'guest'      => 'guest',
];

$stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
foreach ($demoUsers as $username => $role) {
    $stmt->bind_param('sss', $username, $hash, $role);
    $stmt->execute();
}

function userId($conn, $username) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    return (int)$stmt->get_result()->fetch_assoc()['id'];
}

$aliceId   = userId($conn, 'alice');
$bobId     = userId($conn, 'bob');

$stmt = $conn->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
$title   = 'Welcome to the RBAC demo';
$content = "This post was written by alice.\n\nTry logging in as different users to see which buttons appear.";
$stmt->bind_param('iss', $aliceId, $title, $content);
$stmt->execute();
$alicePostId = $conn->insert_id;

$title2   = 'Bob says hello';
$content2 = "A second post, written by bob, so you can compare permissions across owners.";
$stmt->bind_param('iss', $bobId, $title2, $content2);
$stmt->execute();

$stmt = $conn->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
$c1 = "Nice post, alice! (This comment is by bob.)";
$stmt->bind_param('iis', $alicePostId, $bobId, $c1);
$stmt->execute();

echo 'Setup complete! Demo accounts and sample content were created.<br>';
echo 'All passwords are <b>password123</b>.<br><br>';
echo '<a href="login.php">Go to the login page</a>';
