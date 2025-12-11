<?php
// public/task_delete.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :uid");
        $stmt->execute(['id' => $id, 'uid' => $_SESSION['user_id']]);
        
        if ($stmt->rowCount() > 0) {
            set_flash('success', 'Task deleted successfully.');
        } else {
            set_flash('error', 'Task not found or access denied.');
        }
    } catch (PDOException $e) {
        set_flash('error', 'Database error: ' . $e->getMessage());
    }
}

header("Location: " . BASE_URL . "dashboard.php");
exit;
