<?php
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: posts.php');
} else {
    header('Location: login.php');
}
exit;
