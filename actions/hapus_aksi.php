<?php
session_start();
include "../config/koneksi.php";

// 1. Cek proteksi (Hanya admin yang boleh akses file ini)
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// 2. Tangkap ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 3. Perintah SQL untuk menghapus data berdasarkan ID
    $query = "DELETE FROM barang WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        // Jika sukses, balik ke halaman barang
        header("Location: ../pages/barang.php?pesan=hapus_sukses");
    } else {
        // Jika gagal, tampilkan error
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    // Jika tidak ada ID, balik ke halaman barang
    header("Location: ../pages/barang.php");
}
?>