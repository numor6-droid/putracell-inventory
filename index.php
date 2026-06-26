<?php
session_start();
include "config/koneksi.php"; 

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: pages/login.php");
    exit();
}

$user_login  = $_SESSION['user_email'] ?? 'Viewer';
$user_name   = $_SESSION['username'] ?? $user_login; 
$user_role   = $_SESSION['role'] ?? 'viewer';
$cabang_user = $_SESSION['cabang'] ?? '';

// --- LOGIKA DINAMIS PER CABANG ---
$kondisi_cabang = ($user_role == 'admin') ? "1=1" : "cabang_toko = '$cabang_user'";

// 1. HITUNG TOTAL JENIS PRODUK 
$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE $kondisi_cabang");
$total_produk = mysqli_fetch_assoc($q_total)['total'] ?? 0;

// 2. HITUNG STOK MENIPIS (1 sampai 5)
$q_menipis = mysqli_query($conn, "SELECT COUNT(*) as menipis FROM barang WHERE $kondisi_cabang AND stok > 0 AND stok <= 5");
$total_menipis = mysqli_fetch_assoc($q_menipis)['menipis'] ?? 0;

// 3. HITUNG STOK HABIS (0 atau kurang) 
$q_habis = mysqli_query($conn, "SELECT COUNT(*) as habis FROM barang WHERE $kondisi_cabang AND stok <= 0");
$total_habis = mysqli_fetch_assoc($q_habis)['habis'] ?? 0;

// 4. PRODUK MASUK HARI INI 
$q_hari_ini = mysqli_query($conn, "SELECT COUNT(*) as total_hari_ini FROM barang WHERE $kondisi_cabang AND DATE(tanggal_masuk) = CURDATE()");
$res_hari_ini = @mysqli_fetch_assoc($q_hari_ini); 
$produk_hari_ini = $res_hari_ini['total_hari_ini'] ?? 0;

// 5. AMBIL 5 DATA TERBARU UNTUK ISI TABEL 
$data_terbaru = mysqli_query($conn, "SELECT * FROM barang WHERE $kondisi_cabang ORDER BY id DESC LIMIT 5");
$jumlah_data_tabel = mysqli_num_rows($data_terbaru);

// 6. AMBIL DATA PENGUMUMAN DARI DATABASE
$data_pengumuman = mysqli_query($conn, "SELECT * FROM pengumuman ORDER BY id DESC");

// 7. AMBIL DATA REQUEST STOK
if ($user_role == 'admin') {
    $q_request = mysqli_query($conn, "SELECT * FROM request_barang ORDER BY tanggal_lapor DESC LIMIT 5");
} else {
    $q_request = mysqli_query($conn, "SELECT * FROM request_barang WHERE dari_cabang = '$cabang_user' ORDER BY tanggal_lapor DESC LIMIT 5");
}

// 8. AMBIL DATA LOG AKTIVITAS (DINAMIS SESUAI ROLE)
if ($user_role == 'admin') {
    $q_log = mysqli_query($conn, "SELECT * FROM log_aktivitas ORDER BY tanggal_dibuat DESC LIMIT 5");
} else {
    $q_log = mysqli_query($conn, "SELECT * FROM log_aktivitas WHERE username = '$user_name' ORDER BY tanggal_dibuat DESC LIMIT 5");
}

// --- LOGIKA STATUS BANNER DINAMIS ---
if ($total_produk == 0) {
    $icon_status  = "fas fa-store-slash";
    $warna_alert  = "bg-danger text-white"; 
    $teks_status  = "<b>Toko Kosong Melompong!</b> Belum ada satupun data barang di katalog cabang ini. Segera tambahkan produk pertama Anda.";
} elseif ($total_produk > 0 && $total_habis == $total_produk) {
    $icon_status  = "fas fa-box-open";
    $warna_alert  = "bg-danger text-white"; 
    $teks_status  = "<b>Gudang Kosong Total!</b> Seluruh barang di toko telah habis terjual. Segera lakukan restock dan isi toko Anda secepatnya!";
} elseif ($total_habis > 0) {
    $icon_status  = "fas fa-exclamation-triangle";
    $warna_alert  = "bg-danger text-white"; 
    $teks_status  = "<b>Kondisi Kritis:</b> Terdeteksi ada <b>$total_habis jenis produk</b> yang stoknya kosong. Silakan cek katalog barang!";
} elseif ($total_menipis > 0) {
    $icon_status  = "fas fa-exclamation-circle";
    $warna_alert  = "bg-warning text-dark"; 
    $teks_status  = "<b>Peringatan:</b> Ada <b>$total_menipis jenis produk</b> yang stoknya menipis (sisa 5 atau kurang).";
} else {
    $icon_status  = "fas fa-check-circle";
    $warna_alert  = "bg-success text-white"; 
    $teks_status  = "<b>Kondisi Gudang Aman:</b> Seluruh produk berstatus tersedia dan siap jual.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Info | Dashboard</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .main-sidebar { background-color: #0f172a !important; }
    .nav-link.active { background-color: #3b82f6 !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important; }
    .small-box { border-radius: 12px; overflow: hidden; border: none; }
    .bg-gradient-info { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .bg-gradient-danger { background: linear-gradient(135deg, #f43f5e, #e11d48); }
    .card { border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
    .table thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border: none; }
    .user-panel img { width: 2.1rem; height: 2.1rem; object-fit: cover; }
    .text-blue { color: #3b82f6; }
    .news-item { border-left: 3px solid #3b82f6; padding-left: 10px; margin-bottom: 12px; }
    
    /* Custom CSS buat Log Aktivitas */
    .activity-log-item { border-left: 2px solid #e2e8f0; padding-left: 15px; position: relative; margin-bottom: 15px; }
    .activity-log-item::before { content: ''; position: absolute; left: -5px; top: 0; width: 9px; height: 9px; border-radius: 50%; background: #3b82f6; }
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
        <a class="btn btn-light btn-sm text-danger font-weight-bold" href="actions/logout.php" style="border-radius: 6px;">
          <i class="fas fa-power-off mr-1"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link border-0 py-3" style="padding-left: 1.2rem;">
      <img src="dist/img/poto_logo_putracell.png" alt="Logo Putra Cell" class="brand-image img-circle elevation-2" style="opacity: 1; margin-top: -3px;">
      <span class="brand-text font-weight-bold" style="letter-spacing: 1px;">PUTRA CELL</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-0">
        <div class="image">
          <?php 
          $foto_sidebar = !empty($_SESSION['foto_user']) ? $_SESSION['foto_user'] : 'dist/img/default.jpg'; 
          ?>
          <img src="<?php echo $foto_sidebar; ?>" class="img-circle elevation-2" alt="User" style="width: 34px; height: 34px; object-fit: cover; background: #fff;">
        </div>
        <div class="info">
          <a href="pages/profil.php" class="d-block font-weight-bold text-white"><?php echo $user_login; ?></a>
          <small class="text-success"><i class="fas fa-circle text-xs mr-1"></i> Mode: <?php echo ucfirst($user_role); ?></small>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column">
          <li class="nav-item">
            <a href="index.php" class="nav-link active">
              <i class="nav-icon fas fa-th-large"></i>
              <p>Ringkasan Global</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/barang.php" class="nav-link">
              <i class="nav-icon fas fa-list-ul"></i>
              <p>Katalog Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="pages/laporan.php" class="nav-link">
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
        <h4 class="m-0 font-weight-bold text-dark">Dashboard Informasi <?php echo ($user_role == 'admin') ? 'Pusat' : 'Cabang ' . strtoupper($cabang_user); ?></h4>
        <p class="text-muted small">Data ketersediaan gudang hari ini, <?php echo date('d M Y'); ?>.</p>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <div class="alert <?php echo $warna_alert; ?> border-0 text-sm shadow-sm mb-4" style="border-radius: 8px;">
          <i class="<?php echo $icon_status; ?> mr-2"></i> <?php echo $teks_status; ?>
        </div>

        <div class="row">
          <div class="col-lg-4 col-6">
            <a href="pages/barang.php?filter=semua" style="text-decoration: none; display: block;">
              <div class="small-box bg-gradient-info shadow-sm">
                <div class="inner text-white"><h3><?php echo $total_produk; ?></h3><p>Katalog Produk</p></div>
                <div class="icon"><i class="fas fa-box"></i></div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-6">
            <a href="pages/barang.php?filter=menipis" style="text-decoration: none; display: block;">
              <div class="small-box bg-gradient-warning shadow-sm text-white">
                <div class="inner"><h3><?php echo $total_menipis; ?></h3><p>Stok Hampir Habis</p></div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
              </div>
            </a>
          </div>
          <div class="col-lg-4 col-12">
            <a href="pages/barang.php?filter=kosong" style="text-decoration: none; display: block;">
              <div class="small-box bg-gradient-danger shadow-sm text-white">
                <div class="inner"><h3><?php echo $total_habis; ?></h3><p>Stok Kosong</p></div>
                <div class="icon"><i class="fas fa-times-circle"></i></div>
              </div>
            </a>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-8">
              
            <div class="card card-outline card-danger shadow-sm mb-4">
              <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold text-dark">
                  <i class="fas fa-bullhorn mr-2 text-danger"></i> Pantauan Laporan Stok Kosong
                </h3>
                <?php if ($user_role != 'admin'): ?>
                    <a href="pages/buat_request.php" class="btn btn-sm btn-danger font-weight-bold shadow-sm" style="border-radius: 20px;"><i class="fas fa-paper-plane mr-1"></i> Lapor Stok Habis</a>
                <?php endif; ?>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>KODE</th>
                                <th>BARANG DIMINTA</th>
                                <?php if ($user_role == 'admin') echo "<th>CABANG</th>"; ?>
                                <th>STATUS</th>
                                <th>INFO / BALASAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($q_request && mysqli_num_rows($q_request) > 0) {
                                while ($req = mysqli_fetch_assoc($q_request)) {
                                    $badge = ($req['status_request'] == 'Pending') ? 'badge-warning' : (($req['status_request'] == 'Disetujui' || $req['status_request'] == 'Selesai') ? 'badge-success' : 'badge-danger');
                            ?>
                                <tr>
                                    <td class="text-sm font-weight-bold text-muted"><?php echo $req['kode_barang']; ?></td>
                                    <td>
                                        <span class="d-block font-weight-bold"><?php echo $req['nama_barang']; ?></span>
                                        <small class="text-muted">Jml: <?php echo $req['jumlah_minta']; ?> Item</small>
                                    </td>
                                    <?php if ($user_role == 'admin'): ?>
                                        <td><span class="font-weight-bold text-primary"><?php echo strtoupper($req['dari_cabang']); ?></span></td>
                                    <?php endif; ?>
                                    
                                    <td><span class="badge <?php echo $badge; ?> p-2" style="border-radius: 6px;"><?php echo $req['status_request']; ?></span></td>
                                    
                                    <td class="text-nowrap">
                                        <?php if ($user_role == 'admin'): ?>
                                            <?php if ($req['status_request'] == 'Pending'): ?>
                                                <a href="pages/edit_request.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-info shadow-sm rounded mr-1" title="Edit Data"><i class="fas fa-edit"></i></a>
                                                <a href="pages/proses_acc.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-success shadow-sm rounded"><i class="fas fa-check mr-1"></i> Proses Data</a>
                                            <?php else: ?>
                                                <span class="badge badge-light text-muted border mr-1"><i class="fas fa-history"></i> Terekam</span>
                                                <a href="actions/hapus_request.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm rounded" title="Hapus Riwayat" onclick="return confirm('Hapus riwayat permintaan ini?');"><i class="fas fa-trash"></i></a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if ($req['status_request'] == 'Disetujui' || $req['status_request'] == 'Dikirim'): ?>
                                                <div class="mb-1 text-info text-xs font-weight-bold">
                                                    <i class="fas fa-truck text-info mr-1"></i> Sedang Dalam Perjalanan...
                                                </div>
                                                <a href="actions/terima_barang.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-primary shadow-sm rounded" onclick="return confirm('PENTING!\n\nJangan klik OK jika barang FISIK belum sampai di toko Anda!\n\nApakah barang benar-benar sudah tiba dan jumlahnya sesuai?');">
                                                    <i class="fas fa-box-open mr-1"></i> Konfirmasi Terima
                                                </a>
                                            <?php elseif ($req['status_request'] == 'Selesai' || $req['status_request'] == 'Ditolak'): ?>
                                                <span class="badge badge-light text-muted border mr-1"><i class="fas fa-check-double"></i> Selesai</span>
                                                <a href="actions/hapus_request.php?id=<?php echo $req['id']; ?>" class="btn btn-sm btn-outline-danger shadow-sm rounded" title="Hapus dari daftar" onclick="return confirm('Hapus riwayat ini dari pantauan?');"><i class="fas fa-trash"></i></a>
                                            <?php else: ?>
                                                <small class="font-italic text-muted d-block mt-1">
                                                    <?php echo !empty($req['pesan_admin']) ? "<i class='fas fa-reply text-success mr-1'></i> ".$req['pesan_admin'] : '<i class="fas fa-clock mr-1"></i> Menunggu...'; ?>
                                                </small>
                                            <?php endif; ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'>Belum ada laporan stok masuk/keluar.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
              </div>
              <div class="card-footer bg-white text-center border-top">
                <a href="pages/semua_request.php" class="text-danger small font-weight-bold">Lihat Semua Laporan Request <i class="fas fa-arrow-right ml-1"></i></a>
              </div>
            </div>

            <div class="card card-outline card-primary shadow-sm mb-4">
              <div class="card-header border-0 bg-white py-3">
                <h3 class="card-title font-weight-bold">
                  <i class="fas fa-history mr-2 text-primary"></i> 
                  <?php echo $produk_hari_ini; ?> Produk Baru Ditambahkan Hari Ini
                </h3>
              </div>
              <div class="card-body p-0">
                <table class="table table-hover mb-0">
                  <thead><tr><th>Produk</th><th>Kategori</th><th>Status</th><th class="text-right">Sisa Stok</th></tr></thead>
                  <tbody>
                    <?php 
                    if ($jumlah_data_tabel > 0):
                      while($row = mysqli_fetch_assoc($data_terbaru)) : 
                          if ($row['stok'] <= 0) { $badge = "badge-danger"; $text = "Kosong"; }
                          elseif ($row['stok'] <= 5) { $badge = "badge-warning"; $text = "Menipis"; }
                          else { $badge = "badge-success"; $text = "Stabil"; }
                      ?>
                      <tr>
                          <td><b><?php echo $row['nama_barang']; ?></b></td>
                          <td><?php echo $row['kategori']; ?></td>
                          <td><span class="badge badge-pill <?php echo $badge; ?>"><?php echo $text; ?></span></td>
                          <td class="text-right font-weight-bold"><?php echo $row['stok'] . " " . $row['satuan']; ?></td>
                      </tr>
                      <?php 
                      endwhile; 
                    else:
                    ?>
                      <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada data produk di database.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
              <div class="card-footer bg-white text-center">
                <a href="pages/barang.php" class="text-primary small font-weight-bold">Kelola Semua Barang <i class="fas fa-arrow-right ml-1"></i></a>
              </div>
            </div>

            <div class="card card-outline card-success shadow-sm mb-4">
              <div class="card-header border-0 bg-white py-3">
                <h3 class="card-title font-weight-bold text-dark"><i class="fas fa-chart-bar mr-2 text-success"></i> Grafik Analisis Ketersediaan Barang</h3>
              </div>
              <div class="card-body">
                <canvas id="chartGudang" style="min-height: 220px; height: 220px; max-height: 220px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
          
          <div class="col-md-4">
            
            <div class="card bg-white shadow-sm border-0 mb-4">
              <div class="card-body">
                <h6 class="font-weight-bold mb-3"><i class="fas fa-user-shield text-info mr-2"></i> Info Akun</h6>
                <div class="alert alert-info border-0 text-sm mb-3" style="background-color: #f0f9ff; color: #0c4a6e;">
                  Anda masuk sebagai <b><?php echo ucfirst($user_role); ?></b>.
                  <?php if($user_role == 'admin'): ?>
                    <br>Anda memiliki akses penuh untuk mengelola barang.
                  <?php else: ?>
                    <br>Akses Anda terbatas untuk memantau data Cabang <b><?php echo strtoupper($cabang_user); ?></b>.
                  <?php endif; ?>
                </div>
                <hr>
                
                <a href="pages/laporan.php" class="btn btn-outline-primary btn-block btn-sm mb-2 text-left"><i class="fas fa-print mr-2"></i> Cetak Laporan Stok</a>
                
                <a href="pages/barang.php" class="btn btn-outline-secondary btn-block btn-sm text-left"><i class="fas fa-list mr-2"></i> Buka Katalog Barang</a>
              </div>
            </div>

            <div class="card bg-white shadow-sm border-0 mb-4">
              <div class="card-body">
                <h6 class="font-weight-bold mb-3 text-dark d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-newspaper text-warning mr-2"></i> Pengumuman Pusat</span>
                    <?php if ($user_role == 'admin'): ?>
                        <button class="btn btn-xs btn-primary shadow-sm" data-toggle="modal" data-target="#modalPengumuman" style="border-radius: 4px;"><i class="fas fa-plus mr-1"></i> Buat</button>
                    <?php endif; ?>
                </h6>
                
                <?php 
                if ($data_pengumuman && mysqli_num_rows($data_pengumuman) > 0):
                  while ($news = mysqli_fetch_assoc($data_pengumuman)): 
                    $id_pengumuman = $news['id'] ?? '';
                    $status = $news['status'] ?? 'Info';
                    $judul = $news['judul'] ?? 'Pengumuman Baru';
                    $isi = $news['isi_memo'] ?? $news['isi_pengumuman'] ?? '';

                    $status_lower = strtolower($status);
                    if ($status_lower == 'penting') { $color = '#f43f5e'; $badge_class = 'badge-danger'; }
                    elseif ($status_lower == 'info') { $color = '#f59e0b'; $badge_class = 'badge-warning'; }
                    else { $color = '#10b981'; $badge_class = 'badge-success'; }
                ?>
                  <div class="news-item" style="border-left-color: <?php echo $color; ?>; position: relative;">
                    <span class="badge <?php echo $badge_class; ?> text-xs mb-1"><?php echo $status; ?></span>
                    
                    <?php if ($user_role == 'admin'): ?>
                        <a href="actions/hapus_pengumuman.php?id=<?php echo $id_pengumuman; ?>" class="text-danger float-right" title="Hapus Pengumuman" onclick="return confirm('Hapus pengumuman ini secara permanen?');">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>

                    <p class="mb-0 small font-weight-bold text-dark pr-3"><?php echo $judul; ?></p>
                    <small class="text-muted d-block mt-1"><?php echo $isi; ?></small>
                  </div>
                <?php 
                  endwhile;
                else: 
                ?>
                  <p class="small text-muted text-center py-3">Tidak ada pengumuman saat ini.</p>
                <?php endif; ?>

              </div>
            </div>

            <div class="card bg-white shadow-sm border-0 mb-4">
              <div class="card-body">
                <h6 class="font-weight-bold mb-3 text-dark"><i class="fas fa-history text-secondary mr-2"></i> Log Aktivitas Terkini</h6>
                
                <div class="mt-3">
                  <?php 
                  if ($q_log && mysqli_num_rows($q_log) > 0) {
                      while ($log = mysqli_fetch_assoc($q_log)) {
                          $waktu = date('d/m/Y H:i', strtotime($log['tanggal_dibuat']));
                  ?>
                          <div class="activity-log-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-xs font-weight-bold text-primary">
                                    <i class="fas fa-user-circle mr-1"></i> <?php echo htmlspecialchars($log['username']); ?>
                                </span>
                                <span class="text-xs text-muted">
                                    <i class="far fa-clock mr-1"></i> <?php echo $waktu; ?>
                                </span>
                            </div>
                            <p class="small text-muted mb-0" style="line-height: 1.4;">
                                <?php echo htmlspecialchars($log['aktivitas']); ?>
                            </p>
                          </div>
                  <?php
                      }
                  } else {
                      echo '<p class="small text-muted text-center py-3">Belum ada aktivitas terekam di sistem.</p>';
                  }
                  ?>
                </div>
              </div>
              
              <div class="card-footer bg-white text-center border-top">
              </div>
            </div>

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

<?php if ($user_role == 'admin'): ?>
<div class="modal fade" id="modalPengumuman">
  <div class="modal-dialog">
    <div class="modal-content border-0" style="border-radius: 14px;">
      <div class="modal-header bg-light border-0 py-3">
        <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-bullhorn text-warning mr-2"></i> Buat Pengumuman Baru</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form action="actions/tambah_pengumuman.php" method="POST">
        <div class="modal-body py-4">
          <div class="form-group">
            <label class="small font-weight-bold text-muted text-uppercase">Judul</label>
            <input type="text" name="judul" class="form-control" placeholder="Contoh: Info Restock Bulanan" required>
          </div>
          <div class="form-group">
            <label class="small font-weight-bold text-muted text-uppercase">Kategori / Status</label>
            <select name="status" class="form-control" required>
              <option value="Info">Info (Kuning)</option>
              <option value="Penting">Penting (Merah)</option>
              <option value="Sistem">Sistem (Hijau)</option>
            </select>
          </div>
          <div class="form-group">
            <label class="small font-weight-bold text-muted text-uppercase">Isi Pengumuman</label>
            <textarea name="isi_pengumuman" class="form-control" rows="3" placeholder="Ketik isi pengumuman di sini..." required></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 bg-light py-2">
          <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 6px;">Sebarkan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function() {
    $('[data-widget="pushmenu"]').PushMenu('init');

    // Chart.js
    var ctx = document.getElementById('chartGudang').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Total Produk', 'Stok Menipis', 'Stok Kosong'],
            datasets: [{
                label: 'Jumlah Jenis Barang',
                data: [<?php echo "$total_produk, $total_menipis, $total_habis"; ?>],
                backgroundColor: [
                    'rgba(14, 165, 233, 0.8)', 
                    'rgba(245, 158, 11, 0.8)', 
                    'rgba(244, 63, 94, 0.8)'   
                ],
                borderColor: ['#0ea5e9', '#f59e0b', '#f43f5e'],
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: { legend: { display: false } }
        }
    });
  });
</script>
</body>
</html>