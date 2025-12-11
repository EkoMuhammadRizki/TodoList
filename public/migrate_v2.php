<?php
// public/migrate_v2.php
require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../src/db.php';

try {
    echo "Checking database columns...<br>";

    // keyword: priority
    $stmt = $pdo->query("SHOW COLUMNS FROM tasks LIKE 'priority'");
    if (!$stmt->fetch()) {
        echo "Adding 'priority' column...<br>";
        $pdo->exec("ALTER TABLE tasks ADD COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium' AFTER status");
    } else {
        echo "'priority' column already exists.<br>";
    }

    // keyword: due_date
    $stmt = $pdo->query("SHOW COLUMNS FROM tasks LIKE 'due_date'");
    if (!$stmt->fetch()) {
        echo "Adding 'due_date' column...<br>";
        $pdo->exec("ALTER TABLE tasks ADD COLUMN due_date DATE NULL AFTER priority");
    } else {
        echo "'due_date' column already exists.<br>";
    }

    echo "Migration V2 completed successfully! <a href='dashboard.php'>Back to Dashboard</a>";

} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
