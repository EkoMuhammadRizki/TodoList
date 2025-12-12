<?php
// src/db.php
// GLOBAL $pdo

require_once __DIR__ . '/config.php';

try {
    // DSN = Data Source Name
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lempar exception jika ada error
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Kembalikan array asosiatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Gunakan prepared statements asli
    ];
    
    // Buat instance PDO (GLOBAL)
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    
    // DEBUG: var_dump($pdo); // Uncomment untuk mengetes objek koneksi
    
} catch (PDOException $e) {
    // Hentikan eksekusi jika koneksi DB gagal
    die("Koneksi DB Gagal: " . $e->getMessage());
}
