<?php
// public/index.php
// LANDING PAGE
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

// Jika user sudah login, arahkan ke dashboard? 
// User request: index.php adalah landing page. 
// Kalau sudah login, tombol start bisa berubah jadi "Dashboard"
$user = auth_current_user();        

$title = 'Selamat Datang';
require_once __DIR__ . '/../src/views/header.php';
?>


<div class="row align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-lg-8 text-center fade-in">
        <div class="mb-5">
             <i class="bi bi-check-circle-fill text-primary" style="font-size: 5rem;"></i>
        </div>
        <h1 class="display-3 fw-bold mb-4">
            Kelola Tugas, <br> <span class="text-primary">Capai Lebih Banyak</span>
        </h1>
        <p class="lead text-muted mb-5 px-lg-5">
            Solusi manajemen tugas yang simpel, elegan, dan produktif. 
            Atur keseharian Anda dengan mudah dan fokus pada hal yang paling penting.
        </p>
        
        <?php if ($user): ?>
            <div class="alert alert-primary d-inline-flex align-items-center px-4 py-3 rounded-pill shadow-sm border-0 mb-4">
                <i class="bi bi-person-circle me-2 fs-5"></i>
                <span>Halo, <strong><?= e($user['username']) ?></strong>!</span>
            </div>
            <br>
            <a href="dashboard.php" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg">
                Buka Dashboard <i class="bi bi-arrow-right ms-2"></i>
            </a>
        <?php else: ?>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="login.php?mode=register" class="btn btn-primary btn-lg px-5 py-3 shadow-lg">
                    Mulai Gratis <i class="bi bi-rocket-takeoff ms-2"></i>
                </a>
                <a href="login.php?mode=login" class="btn btn-outline-secondary btn-lg px-5 py-3">
                    Masuk <i class="bi bi-box-arrow-in-right ms-2"></i>
                </a>
            </div>
            <p class="mt-4 text-muted small">
                Tidak perlu kartu kredit. Gabung bersama kami hari ini.
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
