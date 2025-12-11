<?php
// src/config.php
// GLOBAL settings - Pengaturan GLOBAL

// Definisikan Base URL - sesuaikan jika nama folder berbeda
define('BASE_URL', 'http://localhost/ToDoList/public/');

// Kredensial Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'todo_app'); // Pastikan ini sesuai dengan nama DB Anda
define('DB_USER', 'root');
define('DB_PASS', '');

// Mulai Session secara global jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
