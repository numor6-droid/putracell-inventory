<?php
session_start();
include "../config/koneksi.php";

// Pastikan yang akses udah login
if (!isset($_SESSION['is_logged_in'])) {
    header("Location: ../pages/login.php");
    exit();
}

// Tangkap ID request dari link tombol yang diklik
$id_request = $_GET['id'] ?? '';
$user_karyawan = $_SESSION['username'] ?? 'Karyawan';

if (!empty($id_request)) {
    // 1. AMBIL DATA REQUESTNYA
    $cek_req = mysqli_query($conn, "SELECT * FROM request_barang WHERE id = '$id_request'");
    $data_req = mysqli_fetch_assoc($cek_req);
    
    if ($data_req) {
        $kode = $data_req['kode_barang'];
        $jumlah = $data_req['jumlah_minta'];
        $cabang = $data_req['dari_cabang'];
        $nama_brg = $data_req['nama_barang'];

        // 2. CEK APAKAH BARANG UDAH ADA DI KATALOG CABANG INI?
        $cek_katalog = mysqli_query($conn, "SELECT * FROM barang WHERE kode_barang = '$kode' AND cabang_toko = '$cabang'");
        
        if (mysqli_num_rows($cek_katalog) > 0) {
            // JIKA UDAH ADA: Update (Tambahkan) stoknya aja
            $query_eksekusi = "UPDATE barang SET stok = stok + $jumlah WHERE kode_barang = '$kode' AND cabang_toko = '$cabang'";
        } else {
            // JIKA BELUM ADA (Toko Kosong): Insert sebagai barang baru
            // Kita kasih default kategori 'Lainnya' dan satuan 'Pcs'
            $query_eksekusi = "INSERT INTO barang (kode_barang, nama_barang, kategori, satuan, lokasi_rak, cabang_toko, stok) 
                               VALUES ('$kode', '$nama_brg', 'Lainnya', 'Pcs', 'Gudang', '$cabang', '$jumlah')";
        }
        
        // 3. JALANKAN PERINTAH SQL-NYA
        if (mysqli_query($conn, $query_eksekusi)) {
            
            // 4. UBAH STATUS REQUEST JADI 'Selesai'
            mysqli_query($conn, "UPDATE request_barang SET status_request = 'Selesai' WHERE id = '$id_request'");

            // 5. CATAT LOG AKTIVITAS 
            mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_karyawan', 'Menerima kiriman $jumlah item $nama_brg dan otomatis update katalog.')");

            // Berhasil, lempar balik ke Dashboard dengan pesan sukses
            echo "<script>alert('Mantap! Barang diterima. Stok $nama_brg otomatis masuk ke katalog cabang $cabang.'); window.location.href='../index.php';</script>";
        } else {
            echo "<script>alert('Ups! Gagal memproses stok barang ke database!'); window.location.href='../index.php';</script>";
        }
    } else {
        echo "<script>alert('Data laporan/request tidak ditemukan!'); window.location.href='../index.php';</script>";
    }
} else {
    header("Location: ../index.php");
}
?>