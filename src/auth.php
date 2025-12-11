<?php
// src/auth.php
require_once __DIR__ . '/db.php';

/**
 * Coba daftarkan user baru
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
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Username atau Email sudah terdaftar.'];
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :pass)");
        $stmt->execute(['username' => $username, 'email' => $email, 'pass' => $hash]);
        return ['success' => true];
        // DEBUG: var_dump($stmt); // Cek hasil insert
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Registration failed: ' . $e->getMessage()];
    }
}

/**
 * Coba login user
 * @param string $username
 * @param string $password
 * @return array ['success' => bool, 'user' => array, 'error' => string]
 */
function auth_login($username, $password, $remember = false) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    
    // DEBUG: var_dump($stmt->fetch()); // Note: ini mungkin mengosongkan result set jika dipanggil dua kali
    // DEBUG: var_dump($user);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        // Login berhasil
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
    
    return ['success' => false, 'error' => 'Username atau password salah.'];
}

/**
 * Dapatkan info user yang sedang login atau null
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
 * Logout user
 */
function auth_logout() {
    session_destroy();
    setcookie('remember_token', '', time() - 3600, '/'); // Hapus cookie
}

/**
 * Cek jika user login, jika tidak redirect
 */
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }
}
