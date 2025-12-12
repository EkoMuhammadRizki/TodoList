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


<!-- SPLASH SCREEN -->
<style>
    #splash-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #ffffff;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease-out, visibility 0.5s;
    }
    #splash-content {
        text-align: center;
        animation: pop-in 0.8s forwards;
        opacity: 0;
        transform: scale(0.9);
    }
    #splash-title {
        font-family: var(--font-family); /* Gunakan font tema (Nunito) */
        font-size: 3rem;
        font-weight: 800; /* Lebih tebal agar mirip headings landing page */
        color: var(--primary); /* Blue matches landing page */
        margin-bottom: 10px;
    }
    #splash-subtitle {
        font-family: var(--font-family); /* Gunakan font tema (Nunito) */
        font-size: 1.2rem;
        font-weight: 600;
        color: #666;
        letter-spacing: 1px;
    }
    @keyframes pop-in {
        to { opacity: 1; transform: scale(1); }
    }
    /* Hero Icon Animation */
    .hero-icon {
        transition: transform 0.8s ease-in-out;
        cursor: pointer;
    }
    .hero-icon:hover {
        transform: rotateY(360deg);
    }
</style>

<div id="splash-screen">
    <div id="splash-content">
        <div id="splash-title">Todo - Manager</div>
        <div id="splash-subtitle">Solusi Manajemen Tugas yang Simpel & Elegan</div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        // Cek apakah baru pertama kali buka sesi ini (opsional, tapi user minta "ketika masuk")
        // Untuk sekarang, kita tampilkan setiap reload agar terlihat.
        const splash = document.getElementById('splash-screen');
        setTimeout(() => {
            splash.style.opacity = '0';
            splash.style.visibility = 'hidden';
            setTimeout(() => { splash.remove(); }, 500);
        }, 2000); 
    });
</script>
<!-- END SPLASH SCREEN -->

<div class="row align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="col-lg-8 text-center fade-in">
        <div class="mb-4">
            <!-- Custom Todo-Manager SVG Illustration -->
            <svg width="150" height="150" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="text-primary hero-icon">
                <path d="M9 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H15M9 5C9 6.10457 9.89543 7 11 7H13C14.1046 7 15 6.10457 15 5M9 5C9 3.89543 9.89543 3 11 3H13C14.1046 3 15 3.89543 15 5M9 14L10.5 15.5L14.5 11.5M9 18L10.5 19.5L14.5 15.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
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
