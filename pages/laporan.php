<?php
session_start();
include "../config/koneksi.php"; // Memanggil jembatan database

// --- LOGIKA AUTO-LOGIN DARI COOKIE ---
if (!isset($_SESSION['is_logged_in']) && isset($_COOKIE['remember_user'])) {
    // Jika session kosong tapi ada cookie, maka "hidupkan" lagi session-nya
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user_email'] = $_COOKIE['remember_user'];
    $_SESSION['role'] = $_COOKIE['remember_role'] ?? 'viewer';
}

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_login  = $_SESSION['user_email'] ?? 'Viewer';
$user_role   = $_SESSION['role'] ?? 'viewer';
$cabang_user = $_SESSION['cabang'] ?? '';

// --- LOGIKA PERCABANGAN (DINAMIS) ---
$where_clause = "";
$nama_cabang_laporan = "Semua Cabang (Global)";
$lokasi_ttd = "Pusat";

if ($user_role == 'admin') {
    // Kalo admin, cek apakah dia lagi filter cabang tertentu
    $filter_cabang = $_GET['cabang'] ?? 'semua';
    if ($filter_cabang !== 'semua') {
        $where_clause = "WHERE cabang_toko = '$filter_cabang'";
        $nama_cabang_laporan = "Cabang " . $filter_cabang;
        $lokasi_ttd = $filter_cabang;
    }
} else {
    // Kalo karyawan, mutlak cuma bisa akses cabangnya sendiri
    $where_clause = "WHERE cabang_toko = '$cabang_user'";
    $nama_cabang_laporan = "Cabang " . $cabang_user;
    $lokasi_ttd = $cabang_user;
}

// AMBIL DATA DARI DATABASE (Urut berdasarkan Nama Barang)
$query = "SELECT * FROM barang $where_clause ORDER BY nama_barang ASC";
$ambil_data = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Info | Rekap Laporan</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">

  <style>
    html { overflow-y: scroll; } /* Menghilangkan efek 'lompat' antar halaman */
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .main-sidebar { background-color: #0f172a !important; }
    .nav-link.active { background-color: #3b82f6 !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important; }
    .card { border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .table thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; border: none; }
    .user-panel img { width: 2.1rem; height: 2.1rem; object-fit: cover; }

    /* LOGIKA CETAK */
    @media print {
        .main-sidebar, .main-header, .btn-print, .main-footer, .content-header, .filter-area { display: none !important; }
        .content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .card { box-shadow: none; border: 1px solid #ddd; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
        table { width: 100% !important; }
    }
    .print-header { display: none; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars text-muted"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link font-weight-bold">Monitoring System</span>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="btn btn-light btn-sm text-danger font-weight-bold" href="../actions/logout.php">
          <i class="fas fa-power-off mr-1"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../index.php" class="brand-link border-0 py-3" style="padding-left: 1.2rem;">
        <img src="../dist/img/poto_logo_putracell.png" alt="Logo Putra Cell" class="brand-image img-circle elevation-2" style="opacity: 1; margin-top: -3px;">
        <span class="brand-text font-weight-bold" style="letter-spacing: 1px;">PUTRA CELL</span>
      </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-0">
        <div class="image">
          <?php 
          // Panggil session foto, kalau kosong pakai default
          $foto_sidebar = !empty($_SESSION['foto_user']) ? $_SESSION['foto_user'] : '../dist/img/default.jpg'; 
          ?>
          <img src="<?php echo $foto_sidebar; ?>" class="img-circle elevation-2" alt="User" style="width: 34px; height: 34px; object-fit: cover; background: #fff;">
        </div>
        <div class="info">
          <a href="profil.php" class="d-block font-weight-bold text-white"><?php echo $user_login; ?></a>
          <small class="text-success"><i class="fas fa-circle text-xs mr-1"></i> Mode: <?php echo ucfirst($user_role); ?></small>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item">
            <a href="../index.php" class="nav-link">
              <i class="nav-icon fas fa-th-large"></i>
              <p>Ringkasan Global</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="barang.php" class="nav-link">
              <i class="nav-icon fas fa-list-ul"></i>
              <p>Katalog Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="laporan.php" class="nav-link active">
              <i class="nav-icon fas fa-print"></i>
              <p>Rekap Laporan</p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-dark">Rekapitulasi Stok</h4>
            
            <div class="d-flex filter-area">
                <?php if ($user_role == 'admin'): ?>
                <form action="" method="GET" class="mr-2 mb-0 d-flex align-items-center">
                    <select name="cabang" class="form-control form-control-sm mr-1" onchange="this.form.submit()">
                        <option value="semua" <?php echo (isset($_GET['cabang']) && $_GET['cabang'] == 'semua') ? 'selected' : ''; ?>>Semua Cabang</option>
                        <option value="Kuningan" <?php echo (isset($_GET['cabang']) && $_GET['cabang'] == 'Kuningan') ? 'selected' : ''; ?>>Kuningan</option>
                        <option value="Jakarta" <?php echo (isset($_GET['cabang']) && $_GET['cabang'] == 'Jakarta') ? 'selected' : ''; ?>>Jakarta</option>
                        <option value="Bandung" <?php echo (isset($_GET['cabang']) && $_GET['cabang'] == 'Bandung') ? 'selected' : ''; ?>>Bandung</option>
                    </select>
                </form>
                <?php endif; ?>

                <button onclick="window.print()" class="btn btn-primary btn-print shadow-sm">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">
        
        <div class="print-header">
            <h2 class="mb-0">LAPORAN INVENTARIS BARANG</h2>
            <h4 class="mb-3 text-uppercase">Putra Cell - <?php echo $nama_cabang_laporan; ?></h4>
            <p class="small">Laporan Ketersediaan Gudang dan Inventaris Sistem</p>
            <p class="small">Dicetak pada: <?php echo date('d F Y, H:i'); ?> | Oleh: <?php echo $user_login; ?></p>
            <hr style="border: 1px solid #000;">
        </div>

        <div class="card shadow-sm">
          <div class="card-body p-0">
            <table class="table table-bordered mb-0">
              <thead>
                <tr>
                  <th style="width: 50px" class="text-center">No</th>
                  <th class="text-center">Kode</th>
                  <th>Nama Produk</th>
                  <th class="text-center">Kategori</th>
                  <?php if ($user_role == 'admin'): ?>
                  <th class="text-center">Cabang</th>
                  <?php endif; ?>
                  <th class="text-center">Sisa Stok</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if (mysqli_num_rows($ambil_data) > 0):
                    $no = 1; 
                    while ($item = mysqli_fetch_assoc($ambil_data)) : 
                ?>
                <tr>
                  <td class="text-center"><?php echo $no++; ?></td>
                  <td class="font-weight-bold text-center"><?php echo $item['kode_barang']; ?></td>
                  <td><?php echo $item['nama_barang']; ?></td>
                  <td class="text-center"><span class="text-uppercase small"><?php echo $item['kategori']; ?></span></td>
                  
                  <?php if ($user_role == 'admin'): ?>
                  <td class="text-center font-weight-bold text-primary"><?php echo $item['cabang_toko']; ?></td>
                  <?php endif; ?>

                  <td class="text-center font-weight-bold <?php echo ($item['stok'] <= 0) ? 'text-danger' : ''; ?>">
                    <?php echo $item['stok'] . " " . $item['satuan']; ?>
                  </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="<?php echo ($user_role == 'admin') ? '6' : '5'; ?>" class="text-center py-4 text-muted">Data barang tidak ditemukan.</td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="mt-5 d-flex print-header" style="display: none !important;"> <div class="ml-auto text-center" style="min-width: 250px;">
                <p class="mb-5"><?php echo ucfirst($lokasi_ttd); ?>, <?php echo date('d M Y'); ?><br><b>Kepala Gudang</b></p>
                <br>
                <p class="font-weight-bold mb-0">( <?php echo strtoupper($user_login); ?> )</p>
                <small class="text-muted">Putra Cell Inventory System</small>
            </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="main-footer bg-white border-0 text-sm">
    <div class="float-right d-none d-sm-inline">Inventory Info V1.0</div>
    <strong>Copyright &copy; 2026 Putra Cell Inventory.</strong> All rights reserved.
  </footer>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
<script>
  $(document).ready(function() {
    $('[data-widget="pushmenu"]').PushMenu('init');
  });
</script>
</body>
</html>