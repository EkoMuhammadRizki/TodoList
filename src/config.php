<?php
// src/config.php
// Pengaturan GLOBAL

// Definisikan URL Dasar - sesuaikan jika nama folder berbeda
define('BASE_URL', 'http://localhost/ToDoList/public/');

// Kredensial Basis Data
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_app'); // Pastikan ini sesuai dengan nama DB Anda
define('DB_USER', 'root');
define('DB_PASS', '');

// Mulai Sesi secara global jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
