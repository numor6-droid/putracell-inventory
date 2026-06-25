<?php
session_start();
include "../config/koneksi.php"; 

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fitur ini KHUSUS ADMIN. Kalau karyawan nyasar ke sini, tendang balik!
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$id_request = $_GET['id'];

// Ambil data request spesifik dari database
$query = mysqli_query($conn, "SELECT * FROM request_barang WHERE id = '$id_request'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='../index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Putra Cell | Edit Request Stok</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .form-control { border-radius: 6px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <div class="content-wrapper ml-0" style="padding-top: 30px;">
    <div class="content">
      <div class="container" style="max-width: 600px;">
        
        <div class="mb-3">
            <a href="../index.php" class="btn btn-default btn-sm text-dark font-weight-bold" style="border-radius: 6px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="card card-outline card-info shadow-sm">
          <div class="card-header bg-white border-0 py-3">
            <h5 class="card-title font-weight-bold text-dark m-0"><i class="fas fa-edit text-info mr-2"></i> Penyesuaian Request Cabang</h5>
          </div>
          
          <form action="../actions/edit_request_aksi.php" method="POST">
            <div class="card-body bg-light">
                
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                <input type="hidden" name="nama_barang" value="<?php echo $data['nama_barang']; ?>">
                <input type="hidden" name="cabang" value="<?php echo $data['dari_cabang']; ?>">

                <div class="form-group">
                    <label class="text-muted small text-uppercase font-weight-bold">Informasi Barang</label>
                    <input type="text" class="form-control bg-white" value="<?php echo $data['kode_barang'] . " - " . $data['nama_barang']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="text-muted small text-uppercase font-weight-bold">Cabang Peminta</label>
                    <input type="text" class="form-control bg-white font-weight-bold text-primary" value="<?php echo strtoupper($data['dari_cabang']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="text-dark small text-uppercase font-weight-bold">Jumlah Disetujui (Pcs) <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah_minta" class="form-control border-info" value="<?php echo $data['jumlah_minta']; ?>" min="1" required>
                    <small class="text-muted">Ubah angka ini jika stok di pusat tidak mencukupi permintaan cabang.</small>
                </div>

                <div class="form-group">
                    <label class="text-dark small text-uppercase font-weight-bold">Pesan Untuk Cabang (Opsional)</label>
                    <textarea name="pesan_admin" class="form-control" rows="2" placeholder="Contoh: Stok di pusat menipis, hanya di-ACC 10 pcs dulu ya."><?php echo $data['pesan_admin']; ?></textarea>
                </div>

            </div>
            <div class="card-footer bg-white text-right py-3 border-0">
                <button type="submit" class="btn btn-info font-weight-bold px-4 shadow-sm" style="border-radius: 6px;">Simpan Perubahan</button>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>