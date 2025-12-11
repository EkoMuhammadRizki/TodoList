<?php
// public/task_create.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

require_login();
$title = 'Create Task';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleInput = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'todo';
    
    if (empty($titleInput)) {
        $errors['title'] = 'Title is required.';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, status) VALUES (:uid, :title, :desc, :status)");
            $stmt->execute([
                'uid' => $_SESSION['user_id'],
                'title' => $titleInput,
                'desc' => $description,
                'status' => $status
            ]);
            
            set_flash('success', 'Task created successfully!');
            header("Location: " . BASE_URL);
            exit;
        } catch (PDOException $e) {
            set_flash('error', 'Error creating task: ' . $e->getMessage());
        }
    }
}

require_once __DIR__ . '/../src/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Create New Task</div>
            <div class="card-body">
                <form action="task_create.php" method="POST">
                    <div class="mb-3">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= isset($titleInput) ? e($titleInput) : '' ?>" required>
                        <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= $errors['title'] ?></div><?php endif; ?>
                    </div>
                    
                    <div class="mb-3">
                        <label>Description (Optional)</label>
                        <textarea name="description" class="form-control" rows="3"><?= isset($description) ? e($description) : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-select">
                            <option value="todo" selected>To Do</option>
                            <option value="doing">Doing</option>
                            <option value="done">Done</option>
                        </select>
                    </div>
                    
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
