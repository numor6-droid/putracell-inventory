<?php
session_start();
include "../config/koneksi.php"; 

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_login  = $_SESSION['user_email'] ?? 'Viewer';
$user_role   = $_SESSION['role'] ?? 'viewer';
$cabang_user = $_SESSION['cabang'] ?? '';

// --- CONFIGURATION PAGINATION (Sistem Pembagi Halaman) ---
$batas_per_halaman = 5; // Lu mau batesin 5 baris kan? Atur di sini bro
$halaman_aktif     = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$data_awal         = ($halaman_aktif > 1) ? ($halaman_aktif * $batas_per_halaman) - $batas_per_halaman : 0;

// Logika filter sesuai role cabang
$kondisi_cabang = ($user_role == 'admin') ? "1=1" : "dari_cabang = '$cabang_user'";

// 1. Hitung total seluruh data request di database
$q_hitung_total = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM request_barang WHERE $kondisi_cabang");
$total_data     = mysqli_fetch_assoc($q_hitung_total)['jumlah'];
$total_halaman  = ceil($total_data / $batas_per_halaman);

// 2. Ambil data dari database secara terbatas (Menggunakan LIMIT data_awal, batas)
$q_request_pagination = mysqli_query($conn, "SELECT * FROM request_barang WHERE $kondisi_cabang ORDER BY tanggal_lapor DESC LIMIT $data_awal, $batas_per_halaman");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Putra Cell | Semua Request</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .card { border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .table thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; border: none; padding: 15px; }
    .table td { vertical-align: middle !important; padding: 15px; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <div class="content-wrapper ml-0" style="padding-top: 20px;">
    <div class="content">
      <div class="container-fluid">
        
        <div class="mb-3">
            <a href="../index.php" class="btn btn-default btn-sm text-dark font-weight-bold" style="border-radius: 6px;">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="card shadow-sm">
          <div class="card-header border-0 bg-white py-3">
            <h5 class="m-0 font-weight-bold text-dark"><i class="fas fa-bullhorn text-danger mr-2"></i> Rekap Seluruh Arsip Laporan Stok Habis</h5>
            <small class="text-muted">Menampilkan data dengan sistem pembatas maksimal <?php echo $batas_per_halaman; ?> baris.</small>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>KODE</th>
                            <th>BARANG DIMINTA</th>
                            <th>CABANG ASAL</th>
                            <th>STATUS REQUEST</th>
                            <th>TANGGAL LAPOR</th>
                            <th>AKSI/BALASAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($q_request_pagination) > 0) {
                            while ($req = mysqli_fetch_assoc($q_request_pagination)) {
                                $badge = ($req['status_request'] == 'Pending') ? 'badge-warning' : (($req['status_request'] == 'Disetujui' || $req['status_request'] == 'Selesai') ? 'badge-success' : 'badge-danger');
                        ?>
                            <tr>
                                <td class="font-weight-bold text-muted"><?php echo $req['kode_barang']; ?></td>
                                <td>
                                    <span class="d-block font-weight-bold text-dark"><?php echo $req['nama_barang']; ?></span>
                                    <small class="text-muted">Jumlah: <?php echo $req['jumlah_minta']; ?> Pcs</small>
                                </td>
                                <td><span class="badge badge-secondary"><?php echo strtoupper($req['dari_cabang']); ?></span></td>
                                <td><span class="badge <?php echo $badge; ?> p-2" style="border-radius:6px;"><?php echo $req['status_request']; ?></span></td>
                                <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($req['tanggal_lapor'])); ?></td>
                                <td class="text-nowrap">
                                    <?php if ($user_role == 'admin'): ?>
                                        <?php if ($req['status_request'] == 'Pending'): ?>
                                            <a href="edit_request.php?id=<?php echo $req['id']; ?>" class="btn btn-xs btn-info py-1 px-2 rounded"><i class="fas fa-edit mr-1"></i> Edit</a>
                                            <a href="proses_acc.php?id=<?php echo $req['id']; ?>" class="btn btn-xs btn-success py-1 px-2 rounded"><i class="fas fa-check mr-1"></i> Proses</a>
                                        <?php else: ?>
                                            <a href="../actions/hapus_request.php?id=<?php echo $req['id']; ?>" class="btn btn-xs btn-outline-danger py-1 px-2 rounded" onclick="return confirm('Hapus dari database?');"><i class="fas fa-trash"></i> Hapus</a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <small class="text-muted font-italic"><?php echo !empty($req['pesan_admin']) ? $req['pesan_admin'] : '-'; ?></small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-muted py-4'>Tidak ada riwayat laporan ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
          </div>
          
          <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
              <div class="small text-muted font-weight-bold">
                  Total Data: <?php echo $total_data; ?> | Halaman <?php echo $halaman_aktif; ?> dari <?php echo $total_halaman; ?>
              </div>
              <ul class="pagination pagination-sm m-0">
                  <li class="page-item <?php if($halaman_aktif <= 1) { echo 'disabled'; } ?>">
                      <a class="page-link" href="<?php if($halaman_aktif > 1){ echo "?halaman=".($halaman_aktif - 1); } else { echo '#'; } ?>">&laquo; Previous</a>
                  </li>
                  
                  <?php for($x=1; $x<=$total_halaman; $x++): ?>
                      <li class="page-item <?php echo ($halaman_aktif == $x) ? 'active' : ''; ?>">
                          <a class="page-link" href="?halaman=<?php echo $x; ?>"><?php echo $x; ?></a>
                      </li>
                  <?php endfor; ?>
                  
                  <li class="page-item <?php if($halaman_aktif >= $total_halaman) { echo 'disabled'; } ?>">
                      <a class="page-link" href="<?php if($halaman_aktif < $total_halaman){ echo "?halaman=".($halaman_aktif + 1); } else { echo '#'; } ?>">Next &raquo;</a>
                  </li>
              </ul>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>