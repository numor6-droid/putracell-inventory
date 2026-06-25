<?php
session_start();
include "../config/koneksi.php";

// Pastikan hanya admin yang bisa melakukan aksi ini
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../pages/barang.php");
    exit();
}

// Tangkap semua data dari form edit
$id          = $_POST['id'];
$kode_barang = $_POST['kode_barang'];
$nama_barang = $_POST['nama_barang'];
$kategori    = $_POST['kategori'];
$satuan      = $_POST['satuan'];
$lokasi_rak  = $_POST['lokasi_rak'];
$cabang_toko = $_POST['cabang_toko'];
$stok        = $_POST['stok'];

// Jalankan query UPDATE
$query = "UPDATE barang SET 
            kode_barang = '$kode_barang',
            nama_barang = '$nama_barang',
            kategori    = '$kategori',
            satuan      = '$satuan',
            lokasi_rak  = '$lokasi_rak',
            cabang_toko = '$cabang_toko',
            stok        = '$stok'
          WHERE id = '$id'";

if (mysqli_query($conn, $query)) {
    // Balik ke halaman barang dengan pesan sukses
    header("Location: ../pages/barang.php?pesan=edit_berhasil");
    exit();
} else {
    echo "Gagal update barang bro: " . mysqli_error($conn);
}
?>