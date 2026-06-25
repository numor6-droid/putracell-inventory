<?php
session_start();
include "../config/koneksi.php";

// Pastikan hanya admin
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Tangkap lemparan data dari proses_acc.php
$id = $_POST['id'];
$status_request = $_POST['status_request'];
$pesan_admin = $_POST['pesan_admin'];

// Update database request_barang
$query = "UPDATE request_barang SET 
            status_request = '$status_request', 
            pesan_admin = '$pesan_admin' 
          WHERE id = '$id'";

if (mysqli_query($conn, $query)) {
    // Kalau sukses, balik ke halaman dashboard
    echo "<script>alert('Laporan berhasil divalidasi dan pesan telah terkirim ke Karyawan!'); window.location.href='../index.php';</script>";
} else {
    echo "Gagal memproses laporan: " . mysqli_error($conn);
}
?>