<?php
session_start();
include "../config/koneksi.php";

// --- PROTEKSI HALAMAN ---
// Pastikan yang akses beneran Admin yang lagi login
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

// Pastikan data dikirim via tombol Submit (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // 1. Tangkap semua data dari form edit (Gunakan escape string untuk keamanan)
    $id_request   = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_barang  = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $cabang       = mysqli_real_escape_string($conn, $_POST['cabang']);
    $jumlah_minta = mysqli_real_escape_string($conn, $_POST['jumlah_minta']);
    $pesan_admin  = mysqli_real_escape_string($conn, $_POST['pesan_admin']);

    // 2. Query untuk Update/Ubah data di database
    $query_update = "UPDATE request_barang 
                     SET jumlah_minta = '$jumlah_minta', 
                         pesan_admin = '$pesan_admin' 
                     WHERE id = '$id_request'";

    // 3. Eksekusi query-nya
    if (mysqli_query($conn, $query_update)) {
        
        // =================================================================
        // Catat ke Log Aktivitas biar ada bukti audit
        $admin_name = $_SESSION['username'] ?? 'Admin Pusat';
        $aktivitas  = "Mengedit data request '$nama_barang' dari Cabang " . strtoupper($cabang) . " (ACC: $jumlah_minta pcs).";
        mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$admin_name', '$aktivitas')");
        // =================================================================

        // Lempar balik ke dashboard dengan pop-up sukses
        echo "<script>
                alert('Data request berhasil disesuaikan!');
                window.location.href='../index.php';
              </script>";
        exit();

    } else {
        echo "<script>
                alert('Gagal menyimpan perubahan: " . mysqli_error($conn) . "');
                window.location.href='../pages/edit_request.php?id=$id_request';
              </script>";
        exit();
    }

} else {
    // Kalau ada user iseng ketik URL langsung tanpa lewat form
    header("Location: ../index.php");
    exit();
}
?>