<?php
session_start();
include "../config/koneksi.php"; // Wajib dipanggil biar bisa nulis ke database

// 1. CATAT LOG AKTIVITAS DULU (Sebelum session dihapus)
$user_keluar = $_SESSION['username'] ?? 'Sistem';
mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_keluar', 'Keluar dari aplikasi sistem.')");

// 2. HANCURKAN SEMUA SESSION (Kartu Akses Sementara)
session_unset();
session_destroy();

// 3. HANCURKAN SEMUA COOKIE (Catatan "Ingat Saya" di Browser)
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, "/");
}

if (isset($_COOKIE['remember_role'])) {
    setcookie('remember_role', '', time() - 3600, "/");
}

// 4. LEMPAR KEMBALI KE HALAMAN LOGIN
header("Location: ../pages/login.php");
exit(); 
?>