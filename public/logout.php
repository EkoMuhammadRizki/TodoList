<?php
// public/logout.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

auth_logout();
set_flash('success', 'You have been logged out.');
header("Location: " . BASE_URL . "index.php");
exit;
