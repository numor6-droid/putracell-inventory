<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/koneksi.php";

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul          = mysqli_real_escape_string($conn, $_POST['judul']);
    $status         = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Ini ngambil dari form HTML (name="isi_pengumuman")
    $isi_pengumuman = mysqli_real_escape_string($conn, $_POST['isi_pengumuman']);

   
    $query_insert = "INSERT INTO pengumuman (judul, status, isi_memo) 
                     VALUES ('$judul', '$status', '$isi_pengumuman')";

    if (mysqli_query($conn, $query_insert)) {
        
        // Catat ke Log Aktivitas
        $admin_name = $_SESSION['username'] ?? 'Admin Pusat';
        $aktivitas  = "Menyiarkan pengumuman baru: '$judul'.";
        mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$admin_name', '$aktivitas')");

        echo "<script>
                alert('Pengumuman berhasil disebarkan ke seluruh cabang!');
                window.location.href='../index.php';
              </script>";
        exit();
    } else {
        
        $error_db = addslashes(mysqli_error($conn));
        
        echo "<script>
                alert('Gagal menyiarkan pengumuman: $error_db');
                window.location.href='../index.php';
              </script>";
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>