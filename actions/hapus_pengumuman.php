<?php
session_start();
include "../config/koneksi.php";

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_pengumuman = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Ambil judul pengumumannya dulu buat dicatat di log aktivitas
    $q_get = mysqli_query($conn, "SELECT judul FROM pengumuman WHERE id = '$id_pengumuman'");
    $data_pengumuman = mysqli_fetch_assoc($q_get);

    if ($data_pengumuman) {
        $judul_dihapus = $data_pengumuman['judul'];
        
        // 2. Eksekusi hapus data
        $query_delete = "DELETE FROM pengumuman WHERE id = '$id_pengumuman'";

        if (mysqli_query($conn, $query_delete)) {
            
            // =================================================================
            // Catat ke Log Aktivitas
            $admin_name = $_SESSION['username'] ?? 'Admin Pusat';
            $aktivitas  = "Menghapus pengumuman: '$judul_dihapus'.";
            mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$admin_name', '$aktivitas')");
            // =================================================================

            echo "<script>
                    alert('Pengumuman berhasil dihapus!');
                    window.location.href='../index.php';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('Gagal menghapus pengumuman: " . mysqli_error($conn) . "');
                    window.location.href='../index.php';
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('Data pengumuman tidak ditemukan di database!');
                window.location.href='../index.php';
              </script>";
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>