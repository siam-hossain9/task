<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentRole() {
    return $_SESSION['role'] ?? 'guest';
}

function currentUserId() {
    return (int)($_SESSION['user_id'] ?? 0);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function canCreate() {
    return currentRole() !== 'guest';
}

function canEditPost($post) {
    $role = currentRole();
    if ($role === 'super_admin') {
        return true;
    }
    if ($role === 'regular_user' && $post['user_id'] == currentUserId()) {
        return true;
    }
    return false;
}

function canDeletePost($post) {
    $role = currentRole();
    if ($role === 'super_admin') {
        return true;
    }
    if ($role === 'moderator') {
        return true;
    }
    if ($role === 'regular_user' && $post['user_id'] == currentUserId()) {
        return true;
    }
    return false;
}

function canDeleteComment($comment, $post) {
    $role = currentRole();
    if ($role === 'super_admin') {
        return true;
    }
    if ($role === 'moderator') {
        return true;
    }
    if ($role === 'regular_user') {
        if ($comment['user_id'] == currentUserId()) {
            return true;
        }

        if ($post['user_id'] == currentUserId()) {
            return true;
        }
    }
    return false;
}

function canManageUsers() {
    return currentRole() === 'super_admin';
}

function roleLabel($role) {
    $labels = [
        'super_admin'  => 'Super Admin',
        'moderator'    => 'Moderator',
        'regular_user' => 'Regular User',
        'guest'        => 'Guest',
    ];
    return $labels[$role] ?? $role;
}
