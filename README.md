# Manajer Tugas Pribadi (To-Do List)

Aplikasi web Manajer Tugas sederhana, aman, dan kaya fitur yang dibangun dengan PHP native, MySQL, Bootstrap 5, dan SweetAlert2.

## 🚀 Fitur Utama

-   **Autentikasi Pengguna**: Login & Registrasi Aman dengan hashing password (`password_hash`).
    -   **Multi-Panel Login/Register**: Desain sliding UI modern untuk beralih antara login dan daftar.
    -   **Fitur Ingat Saya**: Sesi persisten menggunakan cookie.
    -   **Sapaan Personal**: Menyapa pengguna berdasarkan waktu (Pagi/Siang/Sore/Malam) dan status login pertama.
    -   **Validasi Username**: Username hanya boleh mengandung huruf dan angka (alphanumeric).
    -   **Layar Loading**: Indikator loading yang halus saat proses login.
-   **Pengalaman Pengguna (UX/UI)**:
    -   **Landing Page**: Halaman muka yang informatif dan menarik.
    -   **Splash Screen**: Efek intro yang elegan saat pertama kali membuka aplikasi.
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
-   **Basis Data**: MySQL.

## 📂 Struktur Folder

```
project-root/
├─ public/           # File yang dapat diakses publik
│  ├─ dashboard.php  # Halaman Utama (Daftar Tugas)
│  ├─ index.php      # Landing Page & Splash Screen
│  ├─ login.php      # Halaman Autentikasi (Login & Register Gabungan)
│  ├─ logout.php     # Skrip Logout
│  └─ css/           # File CSS Kustom
├─ src/              # Logika Backend
│  ├─ config.php     # Konfigurasi DB & Konstanta
│  ├─ db.php         # Koneksi PDO
│  ├─ auth.php       # Helper Autentikasi & Validasi
│  ├─ functions.php  # Helper Umum & Pagination
│  └─ views/         # Potongan Layout (header/footer)
├─ sql/              # Skema Basis Data
│  └─ skema.sql
└─ README.md
```

## ⚙️ Instalasi & Pengaturan

1.  **Clone/Unduh** repositori ini ke root web server Anda (misal: `xampp/htdocs/ToDoList`).
2.  **Pengaturan Basis Data**:
    -   Buat database MySQL baru bernama `todolist` (atau sesuai keinginan).
    -   Impor `sql/skema.sql` ke dalam database Anda.
3.  **Konfigurasi**:
    -   Buka `src/config.php`.
    -   Sesuaikan `DB_NAME`, `DB_USER`, dan `DB_PASS` dengan lingkungan lokal Anda.
4.  **Jalankan**:
    -   Buka browser dan akses `http://localhost/ToDoList/public/`.

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


---
**Dibuat Agar Hari Anda Lebih Terstruktur.**
