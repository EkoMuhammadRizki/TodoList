<?php
// public/migrate_v3.php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/db.php';

try {
    echo "Checking database columns...<br>";

    // keyword: last_login
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_login'");
    if (!$stmt->fetch()) {
        echo "Adding 'last_login' column...<br>";
        $pdo->exec("ALTER TABLE users ADD COLUMN last_login TIMESTAMP NULL DEFAULT NULL AFTER created_at");
    } else {
        echo "'last_login' column already exists.<br>";
    }

    echo "Migration V3 completed successfully! <a href='login.php'>Back to Login</a>";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
