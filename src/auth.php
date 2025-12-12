<?php
// src/auth.php
require_once __DIR__ . '/db.php';

/**
 * Coba daftarkan pengguna baru
 * @param string $username
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'error' => string]
 */
function auth_register($username, $email, $password) {
    global $pdo; // Akses koneksi DB global
    
    // Cek apakah username atau email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR username = :username");
    $stmt->execute(['email' => $email, 'username' => $username]);
    
    // Debug mysqli_fetch_row di sini (baris 19)
    // var_dump($stmt->fetch(PDO::FETCH_NUM)); die();
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Username atau Email sudah terdaftar.'];
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Masukkan pengguna baru
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :pass)");
        $stmt->execute(['username' => $username, 'email' => $email, 'pass' => $hash]);
        return ['success' => true];
        // DEBUG: var_dump($stmt); // Cek hasil insert
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Registrasi gagal: ' . $e->getMessage()];
    }
}

/**
 * Coba login pengguna
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'user' => array, 'error' => string]
 */
function auth_login($username, $password, $remember = false) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    
    // Debug print_r di sini (baris 47) - Cek array params
    // print_r(['username' => $username]); die();

    // Debug mysqli_fetch_assoc di sini (baris 48) - Cek hasil fetch
    // var_dump($stmt->fetch(PDO::FETCH_ASSOC)); die();
    $user = $stmt->fetch();
    
    // Debug var_dump di sini (baris 51) - Cek isi variabel $user
    // var_dump($user); die();
    
    // 1. Cek apakah username ada
    if (!$user) {
        return [
            'success' => false, 
            'error_type' => 'user_not_found',
            'error' => 'Anda belum terdaftar, silakan register terlebih dahulu.'
        ];
    }

    // 2. Gabungkan Cek Password (Panjang < 8 ATAU Hash Salah)
    if (strlen($password) < 8 || !password_verify($password, $user['password_hash'])) {
        return [
            'success' => false, 
            'error_type' => 'invalid_password',
            'error' => 'Password salah atau kurang dari 8 karakter.'
        ];
    }

    // 3. Sukses
    $_SESSION['user_id'] = $user['id'];
    
    // Cek apakah ini login pertama (last_login masih NULL)
    $isFirstLogin = is_null($user['last_login']);
    
    // Update last_login
    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
    $updateStmt->execute(['id' => $user['id']]);

    return [
        'success' => true, 
        'user' => $user,
        'is_first_login' => $isFirstLogin
    ];
}

/**
 * Dapatkan info pengguna yang sedang login atau null
 * @return array|null
 */
function auth_current_user() {
    global $pdo;
    
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

/**
 * Keluar (Logout) pengguna
 */
function auth_logout() {
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/'); // Hapus cookie
}

/**
 * Cek jika pengguna login, jika tidak alihkan
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }
}
