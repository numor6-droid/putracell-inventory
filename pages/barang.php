<?php
session_start();
include "../config/koneksi.php"; 

// 1.LOGIKA AUTO-LOGIN DARI COOKIE  
if (!isset($_SESSION['is_logged_in']) && isset($_COOKIE['remember_user'])) {
    $_SESSION['is_logged_in'] = true;
    $_SESSION['user_email'] = $_COOKIE['remember_user'];
    $_SESSION['role'] = $_COOKIE['remember_role'] ?? 'viewer';
}

// 2.PROTEKSI HALAMAN (Satpam Utama) 
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$user_login = $_SESSION['user_email'] ?? 'Viewer';
$user_role  = $_SESSION['role'] ?? 'viewer';
$cabang_user = $_SESSION['cabang'] ?? ''; 
$kondisi_cabang = ($user_role == 'admin') ? "1=1" : "cabang_toko = '$cabang_user'";

// SETUP PAGINATION, FILTER, & SEARCH
$filter = $_GET['filter'] ?? 'semua';
$search = $_GET['search'] ?? ''; // TAMBAHAN: Tangkap inputan search
$batas_per_halaman = 10;
$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$data_awal = ($halaman_aktif > 1) ? ($halaman_aktif * $batas_per_halaman) - $batas_per_halaman : 0;

// Logika Filter
if ($filter == 'menipis') {
    $kondisi_filter = "AND stok > 0 AND stok <= 5";
    $judul_tabel = "Katalog Persediaan (Stok Menipis)";
} elseif ($filter == 'kosong') {
    $kondisi_filter = "AND stok <= 0";
    $judul_tabel = "Katalog Persediaan (Stok Kosong)";
} else {
    $kondisi_filter = "";
    $judul_tabel = "Katalog Persediaan";
}

// TAMBAHAN: Logika Search (Bisa nyari berdasarkan Nama atau Kode Barang)
$kondisi_search = "";
if (!empty($search)) {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $kondisi_search = "AND (nama_barang LIKE '%$search_escaped%' OR kode_barang LIKE '%$search_escaped%')";
}

// 1. Hitung total data (Gabungan Filter & Search)
$q_hitung = mysqli_query($conn, "SELECT COUNT(*) as jumlah FROM barang WHERE $kondisi_cabang $kondisi_filter $kondisi_search");
$total_data = mysqli_fetch_assoc($q_hitung)['jumlah'];
$total_halaman = ceil($total_data / $batas_per_halaman);

// 2. Query Utama
$query = "SELECT * FROM barang WHERE $kondisi_cabang $kondisi_filter $kondisi_search ORDER BY id DESC LIMIT $data_awal, $batas_per_halaman";
$ambil_data = mysqli_query($conn, $query) or die("<div style='padding: 20px; color: red;'><b>Error DB:</b> " . mysqli_error($conn) . "</div>");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Info | Katalog Barang</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">

  <style>
    html { overflow-y: scroll; } 
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .main-sidebar { background-color: #0f172a !important; }
    .nav-link.active { background-color: #3b82f6 !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important; }
    .card { border-radius: 12px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
    .table thead th { background: #f1f5f9; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; border: none; padding: 15px; }
    .table td { vertical-align: middle !important; padding: 15px; border-top: 1px solid #f1f5f9; }
    .status-pill { padding: 6px 14px; border-radius: 50px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
    .status-aman { background-color: #dcfce7; color: #15803d; }
    .status-menipis { background-color: #fef3c7; color: #b45309; }
    .status-habis { background-color: #fee2e2; color: #b91c1c; }
    .item-name { color: #1e293b; font-weight: 600; display: block; margin-bottom: 2px; }
    .item-code { font-size: 11px; color: #94a3b8; }
    .form-control { border-radius: 6px; }
    .modal-content { border-radius: 14px; border: none; }
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
        <a class="btn btn-light btn-sm text-danger font-weight-bold" href="../actions/logout.php" style="border-radius: 6px;">
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
            <a href="barang.php" class="nav-link active">
              <i class="nav-icon fas fa-list-ul"></i>
              <p>Katalog Barang</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="laporan.php" class="nav-link">
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
            <div>
                <h4 class="m-0 font-weight-bold text-dark">Katalog Persediaan</h4>
                <p class="text-muted small mb-0">Daftar inventaris barang.</p>
            </div>
            <?php if ($user_role == 'admin') : ?>
                <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#modalTambah" style="border-radius: 6px; font-weight: 600;">
                    <i class="fas fa-plus mr-1"></i> Tambah Produk
                </button>
            <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="content">
      <div class="container-fluid">

        <?php if ($user_role != 'admin') : ?>
          <div class="alert alert-info shadow-sm" style="border-radius: 8px; border-left: 4px solid #0284c7; background-color: #e0f2fe; color: #0369a1; padding: 12px 20px;">
            <i class="fas fa-info-circle mr-2"></i> Anda memantau stok khusus cabang <b><?php echo strtoupper($cabang_user); ?></b>. Hubungi Administrator jika ada kesalahan data.
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'tambah_berhasil'): ?>
          <div class="alert alert-success border-0 text-sm py-2">Produk baru berhasil disimpan ke database!</div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
              <span class="text-muted small font-weight-bold mr-2">Filter Data:</span>
              <a href="barang.php?filter=semua&search=<?php echo urlencode($search); ?>" class="btn btn-sm <?php echo ($filter == 'semua') ? 'btn-primary' : 'btn-light'; ?> px-3 mr-1" style="border-radius:20px;">Semua</a>
              <a href="barang.php?filter=menipis&search=<?php echo urlencode($search); ?>" class="btn btn-sm <?php echo ($filter == 'menipis') ? 'btn-warning text-white' : 'btn-light'; ?> px-3 mr-1" style="border-radius:20px;">Stok Menipis</a>
              <a href="barang.php?filter=kosong&search=<?php echo urlencode($search); ?>" class="btn btn-sm <?php echo ($filter == 'kosong') ? 'btn-danger' : 'btn-light'; ?> px-3" style="border-radius:20px;">Stok Kosong</a>
          </div>

          <form action="barang.php" method="GET" class="m-0">
              <input type="hidden" name="filter" value="<?php echo $filter; ?>">
              <div class="input-group input-group-sm shadow-sm" style="width: 250px; border-radius: 20px; overflow: hidden;">
                  <input type="text" name="search" class="form-control border-0" placeholder="Cari nama atau kode..." value="<?php echo htmlspecialchars($search); ?>">
                  <div class="input-group-append">
                      <button type="submit" class="btn btn-primary border-0"><i class="fas fa-search"></i></button>
                  </div>
              </div>
          </form>
        </div>
        
        <div class="card shadow-sm">
          <div class="card-header border-0 bg-white py-3">
            <h5 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-boxes text-primary mr-2"></i> 
                <?php 
                echo $judul_tabel; 
                if (!empty($search)) {
                    echo " <small class='text-muted ml-1'>(Hasil pencarian: '$search')</small>";
                }
                ?>
            </h5>
          </div>
          <div class="card-body p-0">
            <table class="table table-hover mb-0">
              <thead>
                <tr>
                  <th>Informasi Produk</th>
                  <th>Kategori</th>
                  <th><i class="fas fa-map-marker-alt text-secondary mr-1"></i> Lokasi Rak</th>
                  <th>Sisa Persediaan</th>
                  <th>Status</th>
                  <?php if ($user_role == 'admin') : ?><th class="text-center" style="width: 120px;">Aksi</th><?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php 
                if (mysqli_num_rows($ambil_data) > 0):
                  while ($item = mysqli_fetch_assoc($ambil_data)) : 
                      if ($item['stok'] <= 0) { $class = "status-habis"; $text = "Kosong"; }
                      elseif ($item['stok'] <= 5) { $class = "status-menipis"; $text = "Menipis"; }
                      else { $class = "status-aman"; $text = "Stabil"; }

                      $lokasi = (!empty($item['lokasi_rak'])) ? $item['lokasi_rak'] : 'Rak Utama';
                ?>
                  <tr>
                    <td>
                      <span class="item-name"><?php echo $item['nama_barang']; ?></span>
                      <span class="item-code">ID: <?php echo $item['kode_barang']; ?> <?php echo ($user_role == 'admin') ? "| Cabang: ".$item['cabang_toko'] : ""; ?></span>
                    </td>
                    <td><span class="text-muted small font-weight-bold text-uppercase"><?php echo $item['kategori']; ?></span></td>
                    <td><span class="text-dark small font-weight-bold"><?php echo $lokasi; ?></span></td>
                    <td><span class="font-weight-bold text-dark"><?php echo $item['stok']; ?> <?php echo $item['satuan']; ?></span></td>
                    <td><span class="status-pill <?php echo $class; ?>"><i class="fas fa-dot-circle mr-1 text-xs"></i> <?php echo $text; ?></span></td>
                    
                    <?php if ($user_role == 'admin') : ?>
                    <td class="text-center text-nowrap">
                        <a href="edit_barang.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs text-warning border-0 mr-1" title="Edit">
                          <i class="fas fa-edit" style="font-size: 15px;"></i></a>
                        <a href="../actions/hapus_aksi.php?id=<?php echo $item['id']; ?>" class="btn btn-default btn-xs text-danger border-0" title="Hapus" onclick="return confirm('Yakin hapus, bos?')">
                          <i class="fas fa-trash" style="font-size: 15px;"></i></a>
                    </td>
                    <?php endif; ?>
                  </tr>
                <?php 
                  endwhile; 
                else:
                ?>
                  <tr>
                    <td colspan="<?php echo ($user_role == 'admin') ? '6' : '5'; ?>" class="text-center text-muted py-4">Data barang tidak ditemukan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
              <div class="small text-muted font-weight-bold">
                  Total Produk: <?php echo $total_data; ?> | Halaman <?php echo $halaman_aktif; ?> dari <?php echo $total_halaman; ?>
              </div>
              
              <?php if ($total_halaman > 1): ?>
              <ul class="pagination pagination-sm m-0">
                  <li class="page-item <?php if($halaman_aktif <= 1) { echo 'disabled'; } ?>">
                      <a class="page-link" href="?filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>&halaman=<?php echo ($halaman_aktif - 1); ?>">&laquo; Prev</a>
                  </li>
                  
                  <?php for($x = 1; $x <= $total_halaman; $x++): ?>
                      <li class="page-item <?php echo ($halaman_aktif == $x) ? 'active' : ''; ?>">
                          <a class="page-link" href="?filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>&halaman=<?php echo $x; ?>"><?php echo $x; ?></a>
                      </li>
                  <?php endfor; ?>
                  
                  <li class="page-item <?php if($halaman_aktif >= $total_halaman) { echo 'disabled'; } ?>">
                      <a class="page-link" href="?filter=<?php echo $filter; ?>&search=<?php echo urlencode($search); ?>&halaman=<?php echo ($halaman_aktif + 1); ?>">Next &raquo;</a>
                  </li>
              </ul>
              <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0 bg-light py-3">
          <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-plus-circle text-primary mr-2"></i>Tambah Barang Baru</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form action="../actions/tambah_aksi.php" method="POST">
          <div class="modal-body py-4">
            <div class="form-group">
              <label class="small font-weight-bold text-muted text-uppercase">Kode Barang</label>
              <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: B001" required>
            </div>
            <div class="form-group">
              <label class="small font-weight-bold text-muted text-uppercase">Nama Barang</label>
              <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Charger Samsung Type-C" required>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                      <label class="small font-weight-bold text-muted text-uppercase">Kategori</label>
                      <input type="text" name="kategori" class="form-control" placeholder="Contoh: Aksesoris" required>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                      <label class="small font-weight-bold text-muted text-uppercase">Satuan</label>
                      <select name="satuan" class="form-control" required>
                        <option value="Pcs">Pcs</option>
                        <option value="Unit">Unit</option>
                        <option value="Pack">Pack</option>
                      </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
              <label class="small font-weight-bold text-muted text-uppercase">Lokasi Penyimpanan (Rak)</label>
              <select name="lokasi_rak" class="form-control" required>
                <option value="Etalase Depan">Etalase Depan</option>
                <option value="Rak Aksesoris A">Rak Aksesoris A</option>
                <option value="Rak Aksesoris B">Rak Aksesoris B</option>
                <option value="Gudang Belakang">Gudang Belakang</option>
              </select>
            </div>
            
            <?php if ($user_role == 'admin') : ?>
            <div class="form-group">
              <label class="small font-weight-bold text-muted text-uppercase">Cabang Toko</label>
              <select name="cabang_toko" class="form-control" required>
                <option value="" disabled selected>-- Pilih Cabang --</option>
                <option value="Kuningan">Kuningan</option>
                <option value="Jakarta">Jakarta Selatan</option>
                <option value="Jakarta Pusat">Jakarta Pusat</option>
                <option value="Bandung">Bandung</option>
                <option value="Cirebon">Cirebon</option>
                <option value="Semarang">Semarang</option>
                <option value="Yogyakarta">Yogyakarta</option>
                <option value="Surabaya">Surabaya</option>
                <option value="Medan">Medan</option>
                <option value="Makassar">Makassar</option>
                <option value="Bali">Bali</option>
              </select>
            </div>
            <?php else : ?>
              <input type="hidden" name="cabang_toko" value="<?php echo $cabang_user; ?>">
            <?php endif; ?>

            <div class="form-group">
              <label class="small font-weight-bold text-muted text-uppercase">Stok Awal</label>
              <input type="number" name="stok" class="form-control" min="0" value="0" required>
            </div>
          </div>
          <div class="modal-footer border-0 bg-light py-2">
            <button type="submit" class="btn btn-primary font-weight-bold px-4" style="border-radius: 6px;">Simpan Ke Database</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer bg-white border-0 text-sm">
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