<?php
session_start();
include "../config/koneksi.php";

//  Pastikan yang login cuma Karyawan (Admin nggak perlu lapor ke diri sendiri)
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] == 'admin') {
    header("Location: ../index.php");
    exit();
}

$user_login  = $_SESSION['user_email'];
$cabang_user = $_SESSION['cabang'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lapor Stok Kosong | Putra Cell</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed bg-light">
<div class="wrapper">

    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
        <ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php"><i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard</a></li></ul>
    </nav>

    <div class="content-wrapper ml-0" style="padding-top: 40px;">
        <div class="container d-flex justify-content-center">
            <div class="card shadow-lg" style="width: 100%; max-width: 600px; border-radius: 12px; border: none;">
                <div class="card-header bg-danger text-white" style="border-radius: 12px 12px 0 0;">
                    <h3 class="card-title font-weight-bold"><i class="fas fa-bullhorn mr-2"></i> Form Laporan Stok Kosong</h3>
                </div>
                
                <form action="../actions/proses_request.php" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="alert alert-info text-sm" style="border-radius: 8px;">
                            <i class="fas fa-info-circle"></i> Anda akan mengirim request stok untuk Cabang <b><?php echo strtoupper($cabang_user); ?></b>.
                        </div>

                        <div class="form-group">
                            <label class="text-uppercase text-muted small">Nama Barang yang Habis</label>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: LCD iPhone 11" required>
                        </div>

                        <div class="form-group">
                            <label class="text-uppercase text-muted small">Jumlah Request (Permintaan)</label>
                            <input type="number" name="jumlah_minta" class="form-control" min="1" required>
                        </div>

                        <div class="form-group">
                            <label class="text-uppercase text-muted small">Upload Bukti Fisik (Foto Rak Kosong / PDF)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="bukti_foto" id="customFile" accept=".jpg,.jpeg,.png,.pdf" required>
                                <label class="custom-file-label" for="customFile">Pilih file...</label>
                            </div>
                            <small class="text-danger mt-1 d-block">* Wajib diisi. Format: JPG, PNG, atau PDF.</small>
                        </div>

                    </div>
                    <div class="card-footer bg-white text-right" style="border-radius: 0 0 12px 12px;">
                        <button type="submit" class="btn btn-danger font-weight-bold shadow-sm px-4"><i class="fas fa-paper-plane mr-1"></i> Kirim Laporan ke Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
</body>
</html>