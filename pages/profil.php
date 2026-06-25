<?php
session_start();
include "../config/koneksi.php"; // Panggil koneksi buat update foto ke DB

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

$user_login = $_SESSION['user_email'] ?? 'Viewer';

// --- LOGIKA UPDATE FOTO ---
if (isset($_POST['btn_update'])) {
    if (isset($_FILES['foto_baru']) && $_FILES['foto_baru']['error'] == 0) {
        $file_tmp = $_FILES['foto_baru']['tmp_name'];
        $file_type = $_FILES['foto_baru']['type'];
        
        // Cek tipe file
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        if (in_array($file_type, $allowed_types)) {
            
            // Konversi jadi Base64
            $file_data = file_get_contents($file_tmp);
            $base64_image = 'data:' . $file_type . ';base64,' . base64_encode($file_data);

            // Update ke DB
            $query_update = "UPDATE pelanggan_cell SET foto = '$base64_image' WHERE nama_lengkap = '$user_login'";
            
            if (mysqli_query($conn, $query_update)) {
                // Update session biar foto langsung berubah
                $_SESSION['foto_user'] = $base64_image;
                // ================================================================
                // LOG AKTIVITAS!
                $user_edit = $_SESSION['username'] ?? 'Karyawan';
                mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_edit', 'Memperbarui foto profil akun.')");
                // ================================================================
                echo "<script>alert('Mantap! Foto profil berhasil diganti.');</script>";
            } else {
                echo "<script>alert('Gagal update database!');</script>";
            }
        } else {
            echo "<script>alert('Format file harus JPG atau PNG!');</script>";
        }
    }
}

// Logika nampilin foto: kalau ada di session pakai itu, kalau kosong pakai default
$foto_tampil = !empty($_SESSION['foto_user']) ? $_SESSION['foto_user'] : '../dist/img/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventory Info | Profil User</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">

  <style>
    /* CSS DASAR - SAMAKAN DENGAN STARTER */
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .main-sidebar { background-color: #0f172a !important; }
    .nav-link.active { background-color: #3b82f6 !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important; }
    .user-panel img { width: 2.1rem; height: 2.1rem; object-fit: cover; }
    
    /* CSS KHUSUS CARD PROFIL DI TENGAH */
    .card-profile { border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; margin-top: 20px; }
    .profile-header-bg { background: linear-gradient(135deg, #1e293b, #3b82f6); height: 120px; }
    .profile-img-wrap { margin-top: -60px; text-align: center; }
    .profile-user-img { width: 110px; height: 110px; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); object-fit: cover; background: #fff;}
    .main-footer { background-color: #fff; border-top: 1px solid #f1f5f9; color: #64748b; }
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
          <img src="<?php echo $foto_tampil; ?>" class="img-circle elevation-2" alt="User">
        </div>
        <div class="info">
          <a href="profil.php" class="d-block font-weight-bold"><?php echo $user_login; ?></a>
          <small class="text-success"><i class="fas fa-circle text-xs mr-1"></i> Mode View</small>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
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
        <h4 class="m-0 font-weight-bold text-dark text-center">Detail Akun</h4>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-6 col-lg-5">
            
            <div class="card card-profile">
              <div class="profile-header-bg"></div>
              <div class="card-body pt-0">
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="profile-img-wrap">
                      <img class="profile-user-img img-circle" src="<?php echo $foto_tampil; ?>" alt="User profile" id="preview-foto">
                      <br>
                      <label for="upload-foto" style="cursor: pointer; color: #3b82f6; font-size: 14px; margin-top: 10px; font-weight: 600;">
                          <i class="fas fa-camera mr-1"></i> Ganti Foto
                      </label>
                      <input type="file" name="foto_baru" id="upload-foto" style="display:none;" accept="image/*" onchange="previewImage(event)">
                    </div>
                    
                    <div class="text-center mt-3">
                        <h3 class="font-weight-bold mb-0 text-dark"><?php echo strtoupper($user_login); ?></h3>
                        <p class="text-muted small">System Information Access</p>
                    </div>
                    
                    <div class="mt-4 px-3">
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted small font-weight-bold uppercase">Username</span>
                            <span class="text-dark font-weight-bold"><?php echo $user_login; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small font-weight-bold uppercase">Status</span>
                            <span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 px-3 pb-3">
                        <button type="submit" name="btn_update" class="btn btn-success btn-block rounded-pill font-weight-bold shadow-sm mb-2">Simpan Perubahan</button>
                        <a href="../index.php" class="btn btn-primary btn-block rounded-pill font-weight-bold shadow-sm">Kembali</a>
                    </div>
                </form>

              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer text-sm">
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

  // Script nampilin foto pas dipilih
  function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
      var output = document.getElementById('preview-foto');
      output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
  }
</script>
</body>
</html>