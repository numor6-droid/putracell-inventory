<?php
session_start();
include "../config/koneksi.php";

// Pastikan yang bisa masuk sini cuma admin dewa
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];
$q_data = mysqli_query($conn, "SELECT * FROM request_barang WHERE id='$id'");
$data = mysqli_fetch_assoc($q_data);

// Kalau datanya nggak ada, cegah error
if (!$data) {
    die("<div style='padding:20px; font-family:sans-serif; text-align:center;'><h2>Data Laporan tidak ditemukan!</h2><a href='../index.php'>Kembali ke Dashboard</a></div>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Proses Laporan | Putra Cell</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
    </style>
</head>
<body class="p-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg" style="width: 100%; max-width: 600px; border-radius: 12px; border: none;">
        <div class="card-header bg-success text-white" style="border-radius: 12px 12px 0 0;">
            <h3 class="card-title font-weight-bold"><i class="fas fa-clipboard-check mr-2"></i> Validasi Request Stok</h3>
        </div>
        <form action="../actions/acc_aksi.php" method="POST">
            <div class="card-body">
                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                
                <div class="alert alert-light border shadow-sm pb-0 mb-4">
                    <table class="table table-sm table-borderless">
                        <tr><td width="35%" class="text-muted">Kode Request</td><td>: <b class="text-dark"><?php echo $data['kode_barang']; ?></b></td></tr>
                        <tr><td class="text-muted">Cabang Toko</td><td>: <span class="badge badge-primary px-2 py-1"><?php echo strtoupper($data['dari_cabang']); ?></span></td></tr>
                        <tr><td class="text-muted">Karyawan Pelapor</td><td>: <b class="text-dark"><?php echo $data['oleh_karyawan']; ?></b></td></tr>
                        <tr><td class="text-muted">Barang Diminta</td><td>: <b class="text-danger"><?php echo $data['nama_barang']; ?> (<?php echo $data['jumlah_minta']; ?> Unit)</b></td></tr>
                    </table>
                </div>

                <?php if(!empty($data['bukti_foto']) && $data['bukti_foto'] != 'NULL'): ?>
                <div class="form-group text-center mb-4">
                    <label class="small text-muted text-uppercase d-block text-left border-bottom pb-2 mb-3">Bukti Foto / Dokumen Rak Kosong</label>
                    <?php 
                        $ext = pathinfo($data['bukti_foto'], PATHINFO_EXTENSION);
                        if(in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
                            echo "<img src='../uploads/".$data['bukti_foto']."' alt='Bukti Foto' class='img-fluid rounded border shadow-sm' style='max-height: 250px;'>";
                        } else {
                            echo "<a href='../uploads/".$data['bukti_foto']."' target='_blank' class='btn btn-info btn-sm shadow-sm'><i class='fas fa-file-pdf mr-1'></i> Lihat Dokumen Bukti</a>";
                        }
                    ?>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-sm text-center">
                    <i class="fas fa-exclamation-triangle"></i> Karyawan tidak menyertakan file bukti.
                </div>
                <?php endif; ?>

                <hr>

                <div class="form-group">
                    <label class="small text-muted text-uppercase font-weight-bold">Keputusan Admin (Status)</label>
                    <select name="status_request" class="form-control font-weight-bold" required>
                        <option value="Disetujui" class="text-success">Disetujui & Barang Dikirim</option>
                        <option value="Ditolak" class="text-danger">Ditolak</option>
                        <option value="Pending" class="text-warning" selected>Pending (Tunda)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small text-muted text-uppercase font-weight-bold">Balasan / Catatan untuk Karyawan</label>
                    <textarea name="pesan_admin" class="form-control" rows="3" placeholder="Contoh: Stok sedang dikirim kurir hari ini, estimasi sampai jam 3 sore..." required></textarea>
                </div>

            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end" style="border-radius: 0 0 12px 12px;">
                <a href="../index.php" class="btn btn-light border shadow-sm mr-2 font-weight-bold">Batal</a>
                <button type="submit" class="btn btn-success shadow-sm font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Simpan Keputusan</button>
            </div>
        </form>
    </div>
</body>
</html>