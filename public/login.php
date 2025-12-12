<?php
// public/login.php (Autentikasi Gabungan)
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

// Redirect jika pengguna sudah login
if (auth_current_user()) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$successMessage = '';
$username = '';
$email = '';

// DEBUGGING: Aktifkan Pelaporan Error & Logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function log_debug($msg) {
    file_put_contents(__DIR__ . '/debug_login.txt', date('[Y-m-d H:i:s] ') . $msg . PHP_EOL, FILE_APPEND);
}

// Tangani Permintaan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        // --- LOGIKA REGISTRASI ---
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $response = ['success' => false, 'message' => ''];

        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'Semua kolom wajib diisi.';
        } elseif (strlen($password) < 8) {
             // Register Validation Rule 1
            $response['message'] = 'Password minimal 8 karakter.';
        } elseif ($password !== $confirmPassword) {
             // Register Validation Rule 2 (Simpler to combine logic here if needed, but handled by JS mostly)
            $response['message'] = 'Konfirmasi password tidak valid atau kurang dari 8 karakter.';
        } else {
            $result = auth_register($username, $email, $password);
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Registrasi berhasil! Silakan login.';
            } else {
                $response['message'] = $result['error']; 
            }
        }

        // Kembalikan JSON jika permintaan AJAX
        if (isset($_POST['ajax_register'])) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Fallback untuk non-JS
        if ($response['success']) {
            $successMessage = $response['message'];
            $mode = 'login'; 
        } else {
            $errors['register'] = $response['message'];
            $mode = 'register';
        }

    } else {
        // --- LOGIKA LOGIN ---
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        if (empty($username)) {
             $errors['login_general'] = 'Username wajib diisi.';
             if (isset($_POST['ajax_login'])) {
                 header('Content-Type: application/json');
                 echo json_encode(['success' => false, 'message' => 'Username wajib diisi.', 'error_type' => 'login_general']);
                 exit;
             }
        } else {
            try {
                log_debug("Login attempt using simplified logic: $username");
                $loginResult = auth_login($username, $password, $remember);
                
                if ($loginResult['success']) {
                    // Jika AJAX, return JSON
                    if (isset($_POST['ajax_login'])) {
                         $greeting = $loginResult['is_first_login'] ? 'Selamat Datang' : 'Selamat Datang Kembali';
                         $name = htmlspecialchars($loginResult['user']['username']);
                         // Kita set session flash di sini juga biar muncul di dashboard (opsional)
                         set_flash('success', "$greeting, $name!");
                         
                         header('Content-Type: application/json');
                         echo json_encode(['success' => true, 'redirect' => 'dashboard.php']);
                         exit;
                    }

                    $greeting = $loginResult['is_first_login'] ? 'Selamat Datang' : 'Selamat Datang Kembali';
                    $name = htmlspecialchars($loginResult['user']['username']);
                    set_flash('success', "$greeting, $name!");
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $type = $loginResult['error_type'] ?? 'unknown';
                    $msg = '';
                    
                    if ($type === 'user_not_found') {
                        $msg = 'Anda belum terdaftar, silakan register terlebih dahulu.';
                    } elseif ($type === 'invalid_password') {
                        $msg = 'Password salah atau kurang dari 8 karakter.';
                    } else {
                        $msg = $loginResult['error'];
                    }

                    if (isset($_POST['ajax_login'])) {
                        header('Content-Type: application/json');
                        echo json_encode(['success' => false, 'message' => $msg, 'error_type' => $type]);
                        exit;
                    }

                    // Fallback non-AJAX
                    if ($type === 'user_not_found') $errors['login_not_found'] = $msg;
                    elseif ($type === 'invalid_password') $errors['login_invalid'] = $msg;
                    else $errors['login_general'] = $msg;
                }
            } catch (Exception $e) {
                if (isset($_POST['ajax_login'])) {
                    echo json_encode(['success' => false, 'message' => "System Error: " . $e->getMessage()]);
                    exit;
                }
                $errors['login_general'] = "System Error: " . $e->getMessage();
            }
        }
    }
}

// Tentukan panel aktif berdasarkan GET atau error POST sebelumnya
$mode = isset($mode) ? $mode : ($_GET['mode'] ?? 'login');
$containerClass = ($mode === 'register') ? 'right-panel-active' : '';

$title = 'Masuk / Daftar';
require_once __DIR__ . '/../src/views/header.php';
?>



<div class="row justify-content-center">
    <div class="col-12 text-center">
        <?php if ($successMessage): ?>
            <div class="alert alert-success d-inline-block fade-in"><?= $successMessage ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Wadah Autentikasi Utama -->
<div class="auth-wrapper <?= $containerClass ?>" id="authContainer">
    
    <!-- Formulir Pendaftaran (Register) -->
    <div class="form-container sign-up-container">
        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="action" value="register">
            <h2 class="fw-bold mb-3 text-primary">Buat Akun</h2>
            <div class="social-container mb-3"></div>
            <span class="text-muted small mb-3">Gunakan email dan username unik</span>
            
            <input type="text" name="username" placeholder="Username" class="form-control mb-2" value="<?= e($username) ?>" />
            <input type="email" name="email" placeholder="Email" class="form-control mb-2" value="<?= e($email) ?>" />
            <div class="input-group mb-2">
                <input type="password" name="password" placeholder="Password (Min 8)" class="form-control border-end-0 rounded-start" />
                <span class="input-group-text bg-white border-start-0 toggle-password" style="cursor: pointer; border-radius: 0 0.5rem 0.5rem 0;">
                    <i class="bi bi-eye"></i>
                </span>
            </div>
            <div class="input-group mb-3">
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" class="form-control border-end-0 rounded-start" />
                <span class="input-group-text bg-white border-start-0 toggle-password" style="cursor: pointer; border-radius: 0 0.5rem 0.5rem 0;">
                    <i class="bi bi-eye"></i>
                </span>
            </div>
            
            <button type="submit" class="btn btn-primary rounded-pill px-4">Daftar Sekarang</button>
            <p class="mobile-toggle" onclick="toggleAuth('login')">Sudah punya akun? Masuk</p>
        </form>
    </div>

    <!-- Formulir Masuk (Login) -->
    <div class="form-container sign-in-container">
        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="action" value="login">
            <h2 class="fw-bold mb-3 text-primary">Selamat Datang</h2>
            <span class="text-muted small mb-4">Masuk dengan akun Anda</span>
            
            <input type="text" name="username" placeholder="Username" class="form-control mb-3" value="<?= htmlspecialchars($username) ?>" />
            <div class="input-group mb-3">
                <input type="password" name="password" placeholder="Password" class="form-control border-end-0 rounded-start" />
                <span class="input-group-text bg-white border-start-0 toggle-password" style="cursor: pointer; border-radius: 0 0.5rem 0.5rem 0;">
                     <i class="bi bi-eye"></i>
                </span>
            </div>
            
            <div class="mb-3 form-check text-start w-100 px-4">
                <input type="checkbox" name="remember" class="form-check-input" id="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?>>
                <label class="form-check-label small text-muted" for="remember">Ingat Saya</label>
            </div>
            
            <button type="submit" class="btn btn-primary rounded-pill px-5">Masuk</button>
             <p class="mobile-toggle" onclick="toggleAuth('register')">Belum punya akun? Daftar</p>
        </form>
    </div>

    <!-- Wadah Overlay -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h2 class="fw-bold text-white">Selamat Datang Kembali!</h2>
                <p class="text-white-50 mb-4">Untuk tetap terhubung, silakan masuk dengan info pribadi Anda.</p>
                <button class="btn btn-outline-light ghost rounded-pill px-4" id="signIn">Masuk</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h2 class="fw-bold text-white">Halo, Sobat!</h2>
                <p class="text-white-50 mb-4">Masukkan detail pribadi Anda dan mulailah perjalanan produktif bersama kami.</p>
                <button class="btn btn-outline-light ghost rounded-pill px-4" id="signUp">Daftar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('authContainer');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
        history.pushState(null, null, '?mode=register');
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
        history.pushState(null, null, '?mode=login');
    });

    function toggleAuth(mode) {
        if (mode === 'register') {
            container.classList.add("right-panel-active");
        } else {
            container.classList.remove("right-panel-active");
        }
    }

    // Toggle Password Visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    });

    // --- SweetAlert Logic ---

    // 1. Error Register
    <?php if (isset($errors['register'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= addslashes($errors['register']) ?>',
            didClose: () => {
                container.classList.add("right-panel-active");
            }
        });
    <?php endif; ?>

    // 2. Login: User Tidak Ditemukan
    <?php if (isset($errors['login_not_found'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Masuk',
            text: '<?= $errors['login_not_found'] ?>',
            showCancelButton: true,
            confirmButtonText: 'Daftar Sekarang',
            cancelButtonText: 'Tutup'
        }).then((result) => {
             if (result.isConfirmed) {
                 toggleAuth('register');
             }
        });
    <?php endif; ?>

    // 3. Login: Password Salah ATAU Pendek (Gabungan)
    <?php if (isset($errors['login_invalid'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Login',
            text: '<?= $errors['login_invalid'] ?>'
        });
    <?php endif; ?>

    // 4. Login: Error Umum
    <?php if (isset($errors['login_general'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Login',
            text: '<?= $errors['login_general'] ?>'
        });
    <?php endif; ?>


    // --- Client Side Validation (Register Only) ---
    const registerForm = document.querySelector('.sign-up-container form');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = this.querySelector('input[name="username"]').value;
        const password = this.querySelector('input[name="password"]').value;
        const confirmValues = this.querySelector('input[name="confirm_password"]').value;
        
        // Aturan Register: Password minimal 8 karakter
        if (password.length < 8) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Password Lemah', 
                text: 'Password minimal 8 karakter.' 
            });
            return;
        } 
        
        // Aturan Register: Konfirmasi Password
        if (password !== confirmValues || confirmValues.length < 8) {
            Swal.fire({ 
                icon: 'warning', 
                title: 'Password Tidak Valid', 
                text: 'Konfirmasi password tidak valid atau kurang dari 8 karakter.' 
            });
            return;
        }

        // AJAX Register
        const formData = new FormData(this);
        formData.append('ajax_register', '1');

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.innerText = 'Loading...';
        btn.disabled = true;

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    container.classList.remove("right-panel-active");
                    history.pushState(null, null, '?mode=login');
                    registerForm.reset();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Registrasi',
                    text: data.message
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });

    // --- AJAX LOGIN HANDLER (For Loading Screen) ---
    const loginForm = document.querySelector('.sign-in-container form');
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('ajax_login', '1');

        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerText;
        btn.innerText = 'Memproses...';
        btn.disabled = true;

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan Loading Screen
                const loader = document.getElementById('login-loader');
                const progressBar = document.getElementById('login-progress-bar');
                loader.style.display = 'flex';
                
                // Animate Progress Bar
                setTimeout(() => {
                    progressBar.style.width = '100%';
                }, 100);

                // Redirect setelah animasi selesai (misal 2 detik)
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 2000);
            } else {
                // Error Handling via SweetAlert
                let title = 'Gagal Login';
                if (data.error_type === 'user_not_found') title = 'Gagal Masuk';
                if (data.error_type === 'invalid_password') title = 'Gagal Login';

                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: data.message,
                    showCancelButton: (data.error_type === 'user_not_found'),
                    confirmButtonText: (data.error_type === 'user_not_found') ? 'Daftar Sekarang' : 'OK',
                    cancelButtonText: 'Tutup'
                }).then((result) => {
                    if (data.error_type === 'user_not_found' && result.isConfirmed) {
                        toggleAuth('register');
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });
</script>

<!-- LOADING SCREEN HTML -->
<style>
    #login-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        z-index: 10000;
        display: none; /* Hidden by default */
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    #login-loader-text {
        font-family: var(--font-family);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 20px;
    }
    .progress-container {
        width: 300px;
        height: 6px;
        background-color: #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }
    #login-progress-bar {
        width: 0%;
        height: 100%;
        background-color: var(--primary);
        transition: width 1.8s cubic-bezier(0.22, 1, 0.36, 1);
    }
</style>
<div id="login-loader">
    <div id="login-loader-text">Todo - Manager</div>
    <div class="progress-container">
        <div id="login-progress-bar"></div>
    </div>
</div>
<!-- END LOADING SCREEN -->

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
