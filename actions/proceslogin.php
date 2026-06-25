<?php
session_start();
include "../config/koneksi.php";

$user = $_POST['username']; 
$sandi = $_POST['password'];

// 1. CEK AKUN STATIS (ADMIN/VIEWER)
if ($user == "admin" && $sandi == "12345") {
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user_email']   = "Administrator";
    $_SESSION['role']         = "admin"; 
    $_SESSION['cabang']       = "Pusat"; // Admin pusat
    header('Location: ../index.php');
    exit;

} else if ($user == "viewer" && $sandi == "123") {
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user_email']   = "Tamu / Viewer";
    $_SESSION['role']         = "viewer"; 
    $_SESSION['cabang']       = "Umum";
    header('Location: ../index.php');
    exit;

// 2. CEK DATABASE (UNTUK AKUN REGISTRASI)
} else {
    $sql = "SELECT * FROM pelanggan_cell WHERE username = '$user'"; 
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi password (sama dengan password di DB)
        if ($sandi == $row['password']) { 
            $_SESSION['is_logged_in'] = true;
            
            // AMBIL DATA DARI DATABASE
            $_SESSION['user_email']   = $row['nama_lengkap']; 
            $_SESSION['role']         = $row['role'];    // Otomatis 'admin' atau 'karyawan' dari DB
            $_SESSION['cabang']       = $row['cabang'];  // Otomatis cabang dari DB
            $_SESSION['foto_user']    = $row['foto'];
            
            header('Location: ../index.php');
            exit;
        } else {
            echo "<script>alert('Password salah!'); window.location.href = '../pages/login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak terdaftar!'); window.location.href = '../pages/login.php';</script>";
    }
}
?>