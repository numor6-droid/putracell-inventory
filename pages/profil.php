<?php
session_start();
include "../config/koneksi.php";

// --- PROTEKSI HALAMAN ---
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$user_login = $_SESSION['user_email'] ?? 'Viewer';

// --- LOGIKA UPDATE FOTO DARI HASIL CROP (BASE64) ---
if (isset($_POST['btn_update'])) {
    $diupdate = false;

    // 1. Logika Update Foto Profil
    if (!empty($_POST['foto_baru_base64'])) {
        $base64_image = $_POST['foto_baru_base64'];
        if (mysqli_query($conn, "UPDATE pelanggan_cell SET foto = '$base64_image' WHERE nama_lengkap = '$user_login'")) {
            $_SESSION['foto_user'] = $base64_image;
            $diupdate = true;
        }
    }

    // 2. Logika Update Foto Background (Cover)
    if (!empty($_POST['foto_bg_baru_base64'])) {
        $base64_bg = $_POST['foto_bg_baru_base64'];
        if (mysqli_query($conn, "UPDATE pelanggan_cell SET foto_background = '$base64_bg' WHERE nama_lengkap = '$user_login'")) {
            $_SESSION['foto_background'] = $base64_bg;
            $diupdate = true;
        }
    }

    // Jika berhasil
    if ($diupdate) {
        $user_edit = $_SESSION['username'] ?? 'Karyawan';
        mysqli_query($conn, "INSERT INTO log_aktivitas (username, aktivitas) VALUES ('$user_edit', 'Memperbarui tampilan foto profil dengan fitur skala/crop.')");
        
        echo "<script>alert('Mantap! Tampilan profil berhasil disesuaikan.'); window.location.href='profil.php';</script>";
    }
}

// Logika nampilin foto
$foto_tampil = !empty($_SESSION['foto_user']) ? $_SESSION['foto_user'] : '../dist/img/default.jpg';
$bg_tampil = !empty($_SESSION['foto_background']) ? "url('".$_SESSION['foto_background']."') center/cover" : "linear-gradient(135deg, #1e293b, #3b82f6)";
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
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .main-sidebar { background-color: #0f172a !important; }
    .nav-link.active { background-color: #3b82f6 !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4) !important; }
    .user-panel img { width: 2.1rem; height: 2.1rem; object-fit: cover; }
    
    .card-profile { border-radius: 12px; border: none; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden; margin-top: 20px; }
    .profile-header-bg { height: 140px; position: relative; }
    .profile-img-wrap { margin-top: -60px; text-align: center; position: relative; z-index: 2; }
    .profile-user-img { width: 110px; height: 110px; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); object-fit: cover; background: #fff;}
    
    .btn-upload-cover { position: absolute; top: 10px; right: 10px; background: rgba(255,255,255,0.85); color: #1e293b; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .btn-upload-cover:hover { background: #fff; color: #3b82f6; }

    /* Fix buat Modal Cropper */
    .img-container img { max-width: 100%; }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0">
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars text-muted"></i></a></li>
      <li class="nav-item d-none d-sm-inline-block"><span class="nav-link font-weight-bold">Monitoring System</span></li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item"><a class="btn btn-light btn-sm text-danger font-weight-bold" href="../actions/logout.php"><i class="fas fa-power-off mr-1"></i> Logout</a></li>
    </ul>
  </nav>

  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="../index.php" class="brand-link border-0 py-3" style="padding-left: 1.2rem;">
        <img src="../dist/img/poto_logo_putracell.png" alt="Logo Putra Cell" class="brand-image img-circle elevation-2" style="opacity: 1; margin-top: -3px;">
        <span class="brand-text font-weight-bold" style="letter-spacing: 1px;">PUTRA CELL</span>
      </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-0">
        <div class="image"><img src="<?php echo $foto_tampil; ?>" class="img-circle elevation-2" alt="User"></div>
        <div class="info">
          <a href="profil.php" class="d-block font-weight-bold"><?php echo $user_login; ?></a>
          <small class="text-success"><i class="fas fa-circle text-xs mr-1"></i> Mode View</small>
        </div>
      </div>
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="../index.php" class="nav-link"><i class="nav-icon fas fa-th-large"></i><p>Ringkasan Global</p></a></li>
          <li class="nav-item"><a href="barang.php" class="nav-link"><i class="nav-icon fas fa-list-ul"></i><p>Katalog Barang</p></a></li>
          <li class="nav-item"><a href="laporan.php" class="nav-link"><i class="nav-icon fas fa-print"></i><p>Rekap Laporan</p></a></li>
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
              <form action="" method="POST">
                  
                  <input type="hidden" name="foto_baru_base64" id="foto_baru_base64">
                  <input type="hidden" name="foto_bg_baru_base64" id="foto_bg_baru_base64">

                  <div class="profile-header-bg" id="preview-bg" style="background: <?php echo $bg_tampil; ?>;">
                      <label for="upload-bg" class="btn-upload-cover">
                          <i class="fas fa-camera"></i> Edit Cover
                      </label>
                      <input type="file" id="upload-bg" style="display:none;" accept="image/*" onchange="bukaModalCrop(event, 'bg')">
                  </div>
                  
                  <div class="card-body pt-0">
                      <div class="profile-img-wrap">
                        <img class="profile-user-img img-circle" src="<?php echo $foto_tampil; ?>" alt="User profile" id="preview-foto">
                        <br>
                        <label for="upload-foto" style="cursor: pointer; color: #3b82f6; font-size: 14px; margin-top: 10px; font-weight: 600;">
                            <i class="fas fa-camera mr-1"></i> Ganti Profil
                        </label>
                        <input type="file" id="upload-foto" style="display:none;" accept="image/*" onchange="bukaModalCrop(event, 'profil')">
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
                  </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </section>
  </div>
</div>

<div class="modal fade" id="modalCrop" tabindex="-1" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white border-0">
        <h5 class="modal-title font-weight-bold"><i class="fas fa-crop mr-2"></i>Sesuaikan Skala Gambar</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-0 bg-light">
        <div class="img-container p-3" style="max-height: 60vh;">
          <img id="imageToCrop" src="" alt="Picture">
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary font-weight-bold" id="btnCrop"><i class="fas fa-check mr-1"></i> Terapkan</button>
      </div>
    </div>
  </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../dist/js/adminlte.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
  $(document).ready(function() {
    $('[data-widget="pushmenu"]').PushMenu('init');
  });

  let cropper;
  let tipeCropSaatIni = ''; // Nyimpen info lagi nge-crop 'profil' atau 'bg'

  // Fungsi saat user pilih file
  function bukaModalCrop(event, tipe) {
    let files = event.target.files;
    if (files && files.length > 0) {
      let reader = new FileReader();
      reader.onload = function(e) {
        $('#imageToCrop').attr('src', e.target.result);
        tipeCropSaatIni = tipe;
        $('#modalCrop').modal('show');
      };
      reader.readAsDataURL(files[0]);
    }
    // Kosongin value input biar bisa pilih file yg sama lagi kalau batal
    event.target.value = '';
  }

  // Setelan pas modal muncul (Inisialisasi Cropper)
  $('#modalCrop').on('shown.bs.modal', function() {
    let image = document.getElementById('imageToCrop');
    
    // Logika rasio bingkai: Kalau profil kotak 1:1, kalau Background panjang 21:9
    let rasio = (tipeCropSaatIni === 'profil') ? 1 / 1 : 21 / 9;

    cropper = new Cropper(image, {
      aspectRatio: rasio,
      viewMode: 1, // Kunci biar ga crop di luar area gambar
      dragMode: 'move', // Biar enak digeser-geser gambarnya
      background: false
    });
  }).on('hidden.bs.modal', function() {
    // Kalau ditutup, hapus settingan cropper
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }
  });

  // Tombol Terapkan diklik
  $('#btnCrop').click(function() {
    if (!cropper) return;

    // Resolusi hasil crop
    let canvasW = (tipeCropSaatIni === 'profil') ? 400 : 1000;
    let canvasH = (tipeCropSaatIni === 'profil') ? 400 : 428;

    let canvas = cropper.getCroppedCanvas({
      width: canvasW,
      height: canvasH
    });

    // Ubah jadi base64
    let base64hasil = canvas.toDataURL('image/jpeg', 0.8);

    if (tipeCropSaatIni === 'profil') {

      $('#preview-foto').attr('src', base64hasil);
      
      $('#foto_baru_base64').val(base64hasil);
    } else {
      //  cover belakang
      $('#preview-bg').css('background', "url('" + base64hasil + "') center/cover");
      $('#foto_bg_baru_base64').val(base64hasil);
    }

    $('#modalCrop').modal('hide');
  });
</script>
</body>
</html>