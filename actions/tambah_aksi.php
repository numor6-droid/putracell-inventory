<?php
session_start();

// NYALAKAN FITUR INI SEMENTARA BIAR NGGAK BLANK PUTIH KALAU ADA ERROR
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/koneksi.php";

// 1. AMBIL SEMUA DATA DARI FORM MODAL BARANG 
$kode_barang = $_POST['kode_barang'] ?? '';
$nama_barang = $_POST['nama_barang'] ?? '';
$kategori    = $_POST['kategori'] ?? '';
$satuan      = $_POST['satuan'] ?? '';
$lokasi_rak  = $_POST['lokasi_rak'] ?? '';
$cabang_toko = $_POST['cabang_toko'] ?? '';
$stok        = $_POST['stok'] ?? 0;

// 2. QUERY SIMPAN BARANG (Tanpa ada titik-titik lagi)
$query_simpan = "INSERT INTO barang (kode_barang, nama_barang, kategori, satuan, lokasi_rak, cabang_toko, stok) 
                 VALUES ('$kode_barang', '$nama_barang', '$kategori', '$satuan', '$lokasi_rak', '$cabang_toko', '$stok')";

// 3. EKSEKUSI DAN LOGIKA LOG AKTIVITAS
if (mysqli_query($conn, $query_simpan)) {
    
    // =================================================================
    // Catat log aktivitas ke database
    $user_tambah = $_SESSION['username'] ?? 'Sistem';
    mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_tambah', 'Menambahkan produk $nama_barang ke katalog cabang.')");
    // =================================================================

    // Lempar kembali ke halaman barang dengan pesan sukses
    header("Location: ../pages/barang.php?pesan=tambah_berhasil");
    exit();

} else {
    // Jika gagal, tampilkan pesan error aslinya dari database
    echo "<script>
            alert('GAGAL MENYIMPAN! Error: " . mysqli_error($conn) . "'); 
            window.location.href='../pages/barang.php';
          </script>";
}
?>