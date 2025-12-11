<?php
// public/task_edit.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

require_login();
$title = 'Edit Task';
$errors = [];

// Ambil ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil Tasks & Verifikasi Kepemilikan
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :uid");
$stmt->execute(['id' => $id, 'uid' => $_SESSION['user_id']]);
$task = $stmt->fetch();

if (!$task) {
    set_flash('error', 'Task not found or access denied.');
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleInput = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'todo';
    
    if (empty($titleInput)) {
        $errors['title'] = 'Judul wajib diisi.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :desc, status = :status, updated_at = NOW() WHERE id = :id AND user_id = :uid");
            $stmt->execute([
                'title' => $titleInput,
                'desc' => $description,
                'status' => $status,
                'id' => $id,
                'uid' => $_SESSION['user_id']
            ]);
            
            set_flash('success', 'Tugas berhasil diperbarui!');
            header("Location: " . BASE_URL . "dashboard.php");
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Gagal memperbarui tugas: ' . $e->getMessage());
        }
    }
} else {
    // Isi dari DB
    $titleInput = $task['title'];
    $description = $task['description'];
    $status = $task['status'];
}

require_once __DIR__ . '/../src/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Edit Tugas</div>
            <div class="card-body">
                <form action="task_edit.php?id=<?= $id ?>" method="POST">
                    <div class="mb-3">
                        <label>Judul</label>
                        <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= e($titleInput) ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?= e($description) ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="todo" <?= $status === 'todo' ? 'selected' : '' ?>>To Do</option>
                            <option value="doing" <?= $status === 'doing' ? 'selected' : '' ?>>Doing</option>
                            <option value="done" <?= $status === 'done' ? 'selected' : '' ?>>Done</option>
                        </select>
                    </div>
                    
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
