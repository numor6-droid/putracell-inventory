<?php
session_start();
include "../config/koneksi.php";

// Ambil data session karyawan
$cabang_user   = $_SESSION['cabang'];
$nama_karyawan = $_SESSION['user_email'];

// Tangkap data dari form
$nama_barang   = $_POST['nama_barang'];
$jumlah_minta  = $_POST['jumlah_minta'];
$kode_request  = "REQ-" . rand(1000, 9999); // Generate kode random seperti di database lu
$status        = "Pending";
$tanggal       = date('Y-m-d H:i:s');

// --- PROSES UPLOAD FILE BUKTI ---
$nama_file = $_FILES['bukti_foto']['name'];
$tmp_file  = $_FILES['bukti_foto']['tmp_name'];
$error     = $_FILES['bukti_foto']['error'];

// Bikin folder "uploads" kalau belum ada di dalam project lu
$folder_tujuan = "../uploads/";
if (!is_dir($folder_tujuan)) {
    mkdir($folder_tujuan, 0777, true);
}

// Bikin nama file jadi unik biar nggak bentrok kalau namanya sama
$file_baru = time() . "_" . basename($nama_file);
$path_simpan = $folder_tujuan . $file_baru;

if ($error === 0) {
    // Pindahkan file dari sementara ke folder uploads
    if (move_uploaded_file($tmp_file, $path_simpan)) {
        
        // Kalau upload sukses, masukkan ke database
        $query = "INSERT INTO request_barang (kode_barang, nama_barang, jumlah_minta, dari_cabang, oleh_karyawan, status_request, tanggal_lapor, bukti_foto) 
                  VALUES ('$kode_request', '$nama_barang', '$jumlah_minta', '$cabang_user', '$nama_karyawan', '$status', '$tanggal', '$file_baru')";
                  
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Laporan Stok Kosong dan Bukti berhasil dikirim ke Admin!'); window.location.href='../index.php';</script>";
        } else {
            echo "Gagal insert DB: " . mysqli_error($conn);
        }

    } else {
        echo "<script>alert('Gagal mengupload file bukti!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Error pada file yang diupload!'); window.history.back();</script>";
}
?>