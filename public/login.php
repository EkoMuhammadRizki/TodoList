<?php
// public/login.php (Combined Auth)
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

// Redirect jika sudah login
if (auth_current_user()) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$successMessage = '';
$username = '';
$email = '';

// Tangani Request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'register') {
        // --- LOGIKA REGISTRASI (AJAX & Normal) ---
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $response = ['success' => false, 'message' => ''];

        if (empty($username) || empty($email) || empty($password)) {
            $response['message'] = 'Semua kolom wajib diisi.';
        } elseif ($password !== $confirmPassword) {
            $response['message'] = 'Konfirmasi password tidak cocok.';
        } elseif (strlen($password) < 8) {
            $response['message'] = 'Password minimal 8 karakter.';
        } else {
            $result = auth_register($username, $email, $password);
            if ($result['success']) {
                $response['success'] = true;
                $response['message'] = 'Registrasi berhasil! Silakan login.';
            } else {
                $response['message'] = $result['error']; // Error string from auth_register
            }
        }

        // Kembalikan JSON jika request AJAX
        if (isset($_POST['ajax_register'])) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }

        // Fallback untuk non-JS (menjaga perilaku logika lama untuk jaga-jaga)
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

        if (empty($username) || empty($password)) {
            $errors['login'] = 'Username dan Password wajib diisi.';
        } else {
            $loginResult = auth_login($username, $password, $remember);
            if ($loginResult['success']) {
                $greeting = $loginResult['is_first_login'] ? 'Selamat Datang' : 'Selamat Datang Kembali';
                $name = htmlspecialchars($loginResult['user']['username']);
                set_flash('success', "$greeting, $name!");
                
                header("Location: dashboard.php");
                exit;
            } else {
                $errors['login'] = $loginResult['error'] ?? 'Username atau password salah.';
            }
        }
    }
}

// Tentukan panel aktif berdasarkan GET atau error POST sebelumnya
$mode = isset($mode) ? $mode : ($_GET['mode'] ?? 'login');
$containerClass = ($mode === 'register') ? 'right-panel-active' : '';

$title = 'Login / Daftar';
require_once __DIR__ . '/../src/views/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 text-center">
        <?php if ($successMessage): ?>
            <div class="alert alert-success d-inline-block fade-in"><?= $successMessage ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Main Auth Container -->
<div class="auth-wrapper <?= $containerClass ?>" id="authContainer">
    
    <!-- Sign Up Form (Register) -->
    <div class="form-container sign-up-container">
        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="action" value="register">
            <h2 class="fw-bold mb-3 text-primary">Buat Akun</h2>
            <div class="social-container mb-3">
                 <!-- Optional Social Icons could go here -->
            </div>
            <span class="text-muted small mb-3">Gunakan email dan username unik</span>
            
            <!-- Error handled by SweetAlert -->

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
            
            <p class="mobile-toggle" onclick="toggleAuth('login')">Sudah punya akun? Login</p>
        </form>
    </div>

    <!-- Sign In Form (Login) -->
    <div class="form-container sign-in-container">
        <form action="login.php" method="POST" class="auth-form">
            <input type="hidden" name="action" value="login">
            <h2 class="fw-bold mb-3 text-primary">Selamat Datang</h2>
            <span class="text-muted small mb-4">Login dengan akun Anda</span>
            
            <!-- Error handled by SweetAlert -->

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

    <!-- Overlay Container -->
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h2 class="fw-bold text-white">Selamat Datang Kembali!</h2>
                <p class="text-white-50 mb-4">Untuk tetap terhubung, silakan login dengan info pribadi Anda.</p>
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

    // Mobile Toggle
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

    // Cek Error PHP dengan SweetAlert
    <?php if (isset($errors['register'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= $errors['register'] ?>',
            didClose: () => {
                container.classList.add("right-panel-active"); // Pastikan tetap di panel register
            }
        });
    <?php endif; ?>

    <?php if (isset($errors['login'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal Login',
            text: '<?= $errors['login'] ?>',
            didClose: () => {
                container.classList.remove("right-panel-active"); // Pastikan tetap di panel login
            }
        });
    <?php endif; ?>

    // Validasi Client-side untuk Register mencegah reload/slide
    // Registrasi AJAX
    const registerForm = document.querySelector('.sign-up-container form');
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const password = this.querySelector('input[name="password"]').value;
        const confirmValues = this.querySelector('input[name="confirm_password"]').value;
        
        // Pengecekan cepat Client-side
        if (password.length < 8) {
            Swal.fire({ icon: 'warning', title: 'Password Lemah', text: 'Password minimal harus 8 karakter!' });
            return;
        } else if (password !== confirmValues) {
            Swal.fire({ icon: 'warning', title: 'Password Tidak Cocok', text: 'Konfirmasi password tidak sesuai.' });
            return;
        }

        // Siapkan AJAX
        const formData = new FormData(this);
        formData.append('ajax_register', '1');

        // Tampilkan status loading (opsional, tapi UX bagus)
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
                    // Slide ke Login
                    container.classList.remove("right-panel-active");
                    // Update state URL
                    history.pushState(null, null, '?mode=login');
                    // Reset form
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
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem.'
            });
        })
        .finally(() => {
            btn.innerText = originalText;
            btn.disabled = false;
        });
    });

    // Validasi Client-side untuk Login (Opsional tapi diminta)
    const loginForm = document.querySelector('.sign-in-container form');
    loginForm.addEventListener('submit', function(e) {
        const password = this.querySelector('input[name="password"]').value;
        const username = this.querySelector('input[name="username"]').value;

        if (password.length < 8) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Password Salah Atau Terlalu Pendek',
                text: 'Password minimal harus 8 karakter!'
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../src/views/footer.php'; ?>
