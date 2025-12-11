<?php
// public/dashboard.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

require_login();
$user = auth_current_user();
$title = 'Dashboard';

// --- Handling Request Parameters for Filtering & Sorting ---
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$perPage = 5;

$filter_status = $_GET['filter_status'] ?? '';
$sort_by = $_GET['sort_by'] ?? 'created_at';
$sort_order = $_GET['sort_order'] ?? 'DESC';

// --- Handle Form Submissions ---
$errors = [];
$titleInput = '';
$description = '';
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Add Task Submission
    if (isset($_POST['add_task'])) {
        $titleInput = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'todo';
        $priority = $_POST['priority'] ?? 'medium';
        // Handle empty date as NULL
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
        
        if (empty($titleInput)) {
            $errors['title'] = 'Judul tugas wajib diisi.';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO tasks (user_id, title, description, status, priority, due_date) VALUES (:uid, :title, :desc, :status, :priority, :due_date)");
                $stmt->execute([
                    'uid' => $_SESSION['user_id'],
                    'title' => $titleInput,
                    'desc' => $description,
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $due_date
                ]);
                
                set_flash('success', 'Tugas berhasil dibuat!');
                // Reset POST to avoid resubmission on refresh
                header("Location: dashboard.php");
                exit;
            } catch (PDOException $e) {
                set_flash('error', 'Gagal membuat tugas: ' . $e->getMessage());
            }
        } else {
            $showModal = true;
        }
    }

    // 2. Direct Status Update
    if (isset($_POST['update_status'])) {
        $taskId = (int)$_POST['task_id'];
        $newStatus = $_POST['status'];
        
        if (in_array($newStatus, ['todo', 'doing', 'done'])) {
            try {
                $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :id AND user_id = :uid");
                $stmt->execute(['status' => $newStatus, 'id' => $taskId, 'uid' => $_SESSION['user_id']]);
                set_flash('success', 'Status diperbarui!');
            } catch (PDOException $e) {
                set_flash('error', 'Gagal update status.');
            }
        }
        // Redirect keeping query params
        header("Location: dashboard.php?" . http_build_query($_GET));
        exit;
    }

    // 3. Edit Task Submission
    if (isset($_POST['update_task'])) {
        $id = (int)$_POST['task_id'];
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $status = $_POST['status'];
        $priority = $_POST['priority'];
        $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

        if (empty($title)) {
            set_flash('error', 'Judul tidak boleh kosong.');
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE tasks SET title = :title, description = :desc, status = :status, priority = :priority, due_date = :due_date WHERE id = :id AND user_id = :uid");
                $stmt->execute([
                    'title' => $title,
                    'desc' => $description,
                    'status' => $status,
                    'priority' => $priority,
                    'due_date' => $due_date,
                    'id' => $id,
                    'uid' => $_SESSION['user_id']
                ]);
                set_flash('success', 'Tugas berhasil diperbarui!');
            } catch (PDOException $e) {
                set_flash('error', 'Gagal memperbarui tugas.');
            }
        }
        header("Location: dashboard.php?" . http_build_query($_GET));
        exit;
    }
}

// --- Fetch Data ---
$pagination = get_tasks_paginated($pdo, $_SESSION['user_id'], $page, $perPage, $filter_status, $sort_by, $sort_order);
$tasks = $pagination['data'];
$totalPages = $pagination['last_page'];

// Time-based Greeting Logic
$hour = date('H');
if ($hour >= 5 && $hour < 11) {
    $timeGreeting = 'Pagi 🌅';
} elseif ($hour >= 11 && $hour < 15) {
    $timeGreeting = 'Siang ☀️';
} elseif ($hour >= 15 && $hour < 18) {
    $timeGreeting = 'Sore 🌇';
} else {
    $timeGreeting = 'Malam 🌙';
}

require_once __DIR__ . '/../src/views/header.php';
?>

<div class="row align-items-center mb-4 fade-in">
    <div class="col-md-8">
        <h2 class="fw-bold text-primary mb-0">Selamat <?= $timeGreeting ?>, <?= htmlspecialchars($user['username']) ?>.</h2>
        <p class="text-muted small mb-0">Semoga hari Anda produktif.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
         <button type="button" class="btn btn-primary shadow-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addTaskModal">
            <i class="bi bi-plus-lg me-2"></i> Buat Baru
        </button>
    </div>
</div>

<!-- Filter & Sort Controls -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body py-3">
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-12 col-sm">
                <select name="filter_status" class="form-select form-select-sm shadow-sm pe-5" style="cursor: pointer;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="todo" <?= $filter_status === 'todo' ? 'selected' : '' ?>>Akan Dilakukan</option>
                    <option value="doing" <?= $filter_status === 'doing' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                    <option value="done" <?= $filter_status === 'done' ? 'selected' : '' ?>>Selesai</option>
                </select>
            </div>
            <div class="col-12 col-sm">
                <select name="sort_by" class="form-select form-select-sm shadow-sm pe-5" style="cursor: pointer;" onchange="this.form.submit()">
                    <option value="created_at" <?= $sort_by === 'created_at' ? 'selected' : '' ?>>Tanggal Dibuat</option>
                    <option value="due_date" <?= $sort_by === 'due_date' ? 'selected' : '' ?>>Tenggat Waktu</option>
                    <option value="priority" <?= $sort_by === 'priority' ? 'selected' : '' ?>>Prioritas</option>
                </select>
            </div>
            <div class="col-12 col-sm">
                 <select name="sort_order" class="form-select form-select-sm shadow-sm pe-5" style="cursor: pointer;" onchange="this.form.submit()">
                    <option value="DESC" <?= $sort_order === 'DESC' ? 'selected' : '' ?>>Terbaru / Tinggi</option>
                    <option value="ASC" <?= $sort_order === 'ASC' ? 'selected' : '' ?>>Terlama / Rendah</option>
                </select>
            </div>
        </form>
    </div>
</div>

<?php if (empty($tasks)): ?>
    <div class="alert alert-light text-center py-5 shadow-sm border-0">
        <i class="bi bi-clipboard-check display-4 text-muted mb-3 d-block"></i>
        <p class="h5 text-muted">Belum ada tugas yang cocok.</p> 
        <?php if (!empty($filter_status)): ?>
            <a href="dashboard.php" class="btn btn-outline-primary mt-2">Reset Filter</a>
        <?php else: ?>
            <p>Mulai dengan membuat tugas baru!</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="py-3 ps-4 border-0" width="5%">#</th>
                            <th class="py-3 border-0" width="40%">Judul & Deskripsi</th>
                            <th class="py-3 border-0 text-center" width="10%">Prioritas</th>
                            <th class="py-3 border-0" width="15%">Tenggat Waktu</th>
                            <th class="py-3 border-0" width="20%">Status</th>
                            <th class="text-end py-3 pe-4 border-0" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $index => $task): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-muted"><?= ($pagination['current_page'] - 1) * $pagination['per_page'] + $index + 1 ?></td>
                            <td>
                                <strong><?= e($task['title']) ?></strong>
                                <?php if (!empty($task['description'])): ?>
                                    <br><small class="text-muted"><?= e(substr($task['description'], 0, 50)) . (strlen($task['description']) > 50 ? '...' : '') ?></small>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php
                                $pBadge = match($task['priority']) {
                                    'high' => 'bg-danger',
                                    'medium' => 'bg-warning text-dark',
                                    'low' => 'bg-info text-white',
                                    default => 'bg-secondary'
                                };
                                $pLabel = match($task['priority']) {
                                    'high' => 'Tinggi',
                                    'medium' => 'Sedang',
                                    'low' => 'Rendah',
                                    default => 'Netral'
                                };
                                ?>
                                <span class="badge rounded-pill <?= $pBadge ?> small"><?= $pLabel ?></span>
                            </td>
                            <td>
                                <?php if ($task['due_date']): ?>
                                    <?php 
                                        $due = strtotime($task['due_date']);
                                        $isOverdue = $due < time() && $task['status'] !== 'done';
                                        $dClass = $isOverdue ? 'text-danger fw-bold' : 'text-muted';
                                    ?>
                                    <span class="<?= $dClass ?>"><i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', $due) ?></span>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Direct Status Update -->
                                <form action="dashboard.php" method="POST" class="d-inline">
                                    <input type="hidden" name="update_status" value="1">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <!-- Keep filters alive -->
                                    <?php foreach($_GET as $key => $val): ?>
                                        <input type="hidden" name="<?= e($key) ?>" value="<?= e($val) ?>">
                                    <?php endforeach; ?>
                                    
                                    <select name="status" class="form-select form-select-sm border-0 shadow-none fw-bold 
                                        <?= $task['status'] === 'done' ? 'text-success' : ($task['status'] === 'doing' ? 'text-warning' : 'text-secondary') ?>" 
                                        style="background-color: transparent; cursor: pointer;"
                                        onchange="this.form.submit()">
                                        <option value="todo" class="text-secondary" <?= $task['status'] === 'todo' ? 'selected' : '' ?>>Akan Dilakukan</option>
                                        <option value="doing" class="text-warning" <?= $task['status'] === 'doing' ? 'selected' : '' ?>>Sedang Dikerjakan</option>
                                        <option value="done" class="text-success" <?= $task['status'] === 'done' ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </form>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick='openEditModal(<?= json_encode($task) ?>)'
                                            data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="#" onclick="confirmDelete(<?= $task['id'] ?>)" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <?php
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = http_build_query($queryParams);
        $link = '?' . ($queryString ? $queryString . '&' : '') . 'page=';
    ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $link . ($page - 1) ?>">Sebelumnya</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $link . $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="<?= $link . ($page + 1) ?>">Selanjutnya</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>

<?php endif; ?>

<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak akan dapat mengembalikan data ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `task_delete.php?id=${id}`;
        }
    })
}
</script>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="dashboard.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold text-primary" id="addTaskModalLabel">Tambah Tugas Baru</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="add_task" value="1">
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Judul Tugas <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" value="<?= isset($titleInput) ? e($titleInput) : '' ?>" placeholder="Contoh: Belajar PHP Dasar">
                <?php if (isset($errors['title'])): ?><div class="invalid-feedback"><?= $errors['title'] ?></div><?php endif; ?>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small text-muted">Prioritas</label>
                    <select name="priority" class="form-select">
                        <option value="low">Rendah</option>
                        <option value="medium" selected>Sedang</option>
                        <option value="high">Tinggi</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                     <label class="form-label fw-bold small text-muted">Tenggat Waktu</label>
                     <input type="date" name="due_date" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Status Awal</label>
                <select name="status" class="form-select">
                    <option value="todo" selected>Akan Dilakukan</option>
                    <option value="doing">Sedang Dikerjakan</option>
                    <option value="done">Selesai</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Deskripsi</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Tambahkan detail tugas..."><?= isset($description) ? e($description) : '' ?></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Tugas</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="dashboard.php" method="POST">
          <div class="modal-header">
            <h5 class="modal-title fw-bold text-primary">Edit Tugas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="update_task" value="1">
            <input type="hidden" name="task_id" id="edit_task_id">
            
            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Judul Tugas <span class="text-danger">*</span></label>
                <input type="text" name="title" id="edit_title" class="form-control" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small text-muted">Prioritas</label>
                    <select name="priority" id="edit_priority" class="form-select">
                        <option value="low">Rendah</option>
                        <option value="medium">Sedang</option>
                        <option value="high">Tinggi</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                     <label class="form-label fw-bold small text-muted">Tenggat Waktu</label>
                     <input type="date" name="due_date" id="edit_due_date" class="form-control">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select name="status" id="edit_status" class="form-select">
                    <option value="todo">Akan Dilakukan</option>
                    <option value="doing">Sedang Dikerjakan</option>
                    <option value="done">Selesai</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">Deskripsi</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary rounded-pill px-4">Update Tugas</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
// Function to populate and open Edit Modal
function openEditModal(task) {
    document.getElementById('edit_task_id').value = task.id;
    document.getElementById('edit_title').value = task.title;
    document.getElementById('edit_description').value = task.description;
    document.getElementById('edit_status').value = task.status;
    document.getElementById('edit_priority').value = task.priority;
    document.getElementById('edit_due_date').value = task.due_date;
    
    var editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
    editModal.show();
}

// Open modal if errors exist (from PHP)
<?php if ($showModal): ?>
document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('addTaskModal'));
    myModal.show();
});
<?php endif; ?>

// Tooltip init
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
