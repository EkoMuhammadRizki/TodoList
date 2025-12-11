# Manajer Tugas Pribadi (To-Do List)

Aplikasi web Manajer Tugas sederhana, aman, dan kaya fitur yang dibangun dengan PHP native, MySQL, Bootstrap 5, dan SweetAlert2.

## 🚀 Fitur Utama

-   **Autentikasi Pengguna**: Login & Registrasi Aman dengan hashing password (`password_hash`).
    -   **Multi-Panel Login/Register**: Desain sliding UI modern untuk beralih antara login dan daftar.
    -   **Fitur Ingat Saya**: Sesi persisten menggunakan cookie.
    -   **Sapaan Personal**: Menyapa pengguna berdasarkan waktu (Pagi/Siang/Sore/Malam) dan status login pertama.
-   **Manajemen Tugas (CRUD)**:
    -   **Modal Interaktif**: Tambah dan Edit tugas menggunakan popup modal tanpa pindah halaman.
    -   **Detail Tugas**: Judul, Deskripsi, Status, **Prioritas** (Tinggi/Sedang/Rendah), dan **Tenggat Waktu**.
    -   **Update Status Cepat**: Ubah status tugas langsung dari tabel dashboard.
-   **Organisasi & Tampilan**:
    -   **Filter & Sortir**: Filter berdasarkan status dan urutkan berdasarkan Prioritas atau Tenggat Waktu.
    -   **Pagination**: Navigasi halaman tugas dengan mudah.
    -   **Desain Responsif**: Tampilan rapi di desktop maupun mobile dengan Bootstrap 5.
-   **Notifikasi Cerdas**: Integrasi SweetAlert2 untuk pesan error, sukses, dan konfirmasi hapus yang estetik.
-   **Keamanan**:
    -   PDO Prepared Statements mencegah SQL injection.
    -   Proteksi XSS (output escaping).
    -   Validasi password kuat (min 8 karakter) di sisi klien dan server.

## 🛠 Teknologi

-   **Frontend**: HTML5, CSS3 (Custom + Bootstrap 5), JavaScript (AJAX + SweetAlert2).
-   **Backend**: Native PHP 8+.
-   **Database**: MySQL.

## 📂 Struktur Folder

```
project-root/
├─ public/           # File yang dapat diakses publik
│  ├─ dashboard.php  # Halaman Utama (Daftar Tugas) & Landing Page User
│  ├─ login.php      # Halaman Autentikasi (Login & Register Gabungan)
│  ├─ logout.php     # Skrip Logout
│  ├─ migrate_v2.php # Skrip Migrasi (Prioritas & Tenggat Waktu)
│  ├─ migrate_v3.php # Skrip Migrasi (Last Login)
│  └─ css/           # File CSS Kustom
├─ src/              # Logika Backend
│  ├─ config.php     # Konfigurasi DB & Konstanta
│  ├─ db.php         # Koneksi PDO
│  ├─ auth.php       # Helper Autentikasi
│  ├─ functions.php  # Helper Umum & Pagination
│  └─ views/         # Potongan Layout (header/footer)
├─ sql/              # Skema Database
│  └─ skema.sql
└─ README.md
```

## ⚙️ Instalasi & Pengaturan

1.  **Clone/Download** repositori ini ke root web server Anda (misal: `xampp/htdocs/ToDoList`).
2.  **Pengaturan Database**:
    -   Buat database MySQL baru bernama `todolist` (atau sesuai keinginan).
    -   Impor `sql/skema.sql` ke dalam database.
3.  **Konfigurasi**:
    -   Buka `src/config.php`.
    -   Sesuaikan `DB_NAME`, `DB_USER`, dan `DB_PASS` dengan lingkungan lokal Anda.
4.  **Jalankan**:
    -   Buka browser dan akses `http://localhost/ToDoList/public/`.
    -   Jika ini instalasi baru, mungkin perlu menjalankan skrip migrasi jika tabel belum lengkap (opsional, `skema.sql` sudah mencakup semua).

## 📖 Panduan Penggunaan

### Registrasi & Login
-   Akses halaman utama, jika belum login akan diarahkan ke halaman Autentikasi.
-   Gunakan panel kanan untuk **Daftar** akun baru.
-   Gunakan panel kiri untuk **Masuk**.
-   Centang "Ingat Saya" agar tidak perlu login ulang setiap membuka browser.

### Dashboard
-   **Tambah Tugas**: Klik tombol "Buat Baru" di kanan atas.
-   **Filter**: Gunakan dropdown di atas tabel untuk menyaring tugas berdasarkan status atau mengurutkannya.
-   **Update Status**: Klik langsung pada kolom Status di tabel untuk mengubahnya (misal: dari "Akan Dilakukan" ke "Selesai").
-   **Edit/Hapus**: Gunakan tombol aksi di sebelah kanan setiap baris tugas.

## 🐛 Debugging

Kode program dilengkapi dengan komentar (dalam Bahasa Indonesia) untuk memudahkan pemahaman alur.
Cari komentar `// DEBUG:` di file sumber jika ingin mengaktifkan mode debug sederhana.

---
**Dibuat untuk Tujuan Edukasi.**
