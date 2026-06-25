<?php
session_start();
include "../config/koneksi.php";

// 1. Ambil data dari form input (nama, kode, stok, dll...)
$kode_barang = $_POST['kode_barang'];
$nama_barang = $_POST['nama_barang'];
// ... sisa data lainnya ...

// 2. Query simpan barang ke tabel barang
$query_simpan = "INSERT INTO barang (kode_barang, nama_barang, ...) VALUES ('$kode_barang', '$nama_barang', ...)";

// 3. JIKA BARANG BERHASIL DISIMPAN, CATAT LOG NYA DI SINI
if (mysqli_query($conn, $query_section)) {
    
    // =================================================================
    $user_tambah = $_SESSION['username'] ?? 'Sistem';
    mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_tambah', 'Manambahkan produk baru ke katalog cabang.')");
    // =================================================================

    header("Location: ../pages/barang.php?pesan=tambah_berhasil");
    exit();

} else {
    // Jika gagal
    echo "<script>alert('Gagal!'); window.location.href='../pages/barang.php';</script>";
}
?>