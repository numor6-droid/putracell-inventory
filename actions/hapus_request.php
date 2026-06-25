<?php
session_start();
include "../config/koneksi.php";

// --- PROTEKSI ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../pages/login.php");
    exit();
}

// Pastikan ada ID yang dikirim dari URL
if (isset($_GET['id'])) {
    $id_request = $_GET['id'];
    
    // 1. Ambil data request-nya dulu sebelum dihapus (buat kebutuhan Log Aktivitas)
    $query_get = mysqli_query($conn, "SELECT nama_barang, dari_cabang FROM request_barang WHERE id = '$id_request'");
    $data_req = mysqli_fetch_assoc($query_get);

    if ($data_req) {
        $nama_barang = $data_req['nama_barang'];
        $cabang = $data_req['dari_cabang'];

        // 2. Eksekusi query hapus
        $query_hapus = "DELETE FROM request_barang WHERE id = '$id_request'";

        if (mysqli_query($conn, $query_hapus)) {
            
            // =================================================================
            // 3. Catat ke Log Aktivitas biar jejaknya nggak benar-benar hilang
            $user_login = $_SESSION['username'] ?? 'Sistem';
            $aktivitas = "Menghapus riwayat request barang '$nama_barang' dari pantauan (Cabang: $cabang).";
            mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_login', '$aktivitas')");
            // =================================================================

            // Munculin pop-up sukses dan balikin ke index
            echo "<script>
                    alert('Riwayat request berhasil dihapus dari pantauan!');
                    window.location.href='../index.php';
                  </script>";
            exit();

        } else {
            // Kalau gagal hapus
            echo "<script>
                    alert('Gagal menghapus riwayat: " . mysqli_error($conn) . "');
                    window.location.href='../index.php';
                  </script>";
            exit();
        }
    } else {
        // Kalau ID nya ngasal / datanya udah nggak ada di database
        echo "<script>
                alert('Data request tidak ditemukan!');
                window.location.href='../index.php';
              </script>";
        exit();
    }
} else {
    // Kalau ada user iseng buka file ini langsung tanpa ID
    header("Location: ../index.php");
    exit();
}
?>