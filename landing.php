<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Putra Cell | Beranda</title>

  <!-- Google Font: Inter -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700,800&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style AdminLTE -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">

  <style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    
    /* Navbar Custom */
    .navbar-light { background-color: #ffffff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .brand-text { letter-spacing: 1px; color: #0f172a; }
    
    /* Hero Section Custom */
    .hero-section {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 100px 0;
        position: relative;
        overflow: hidden;
    }
    .hero-section::after {
        content: ''; position: absolute; right: -10%; top: -20%;
        width: 400px; height: 400px; background: rgba(59, 130, 246, 0.2);
        filter: blur(80px); border-radius: 50%;
    }
    .hero-title { font-size: 3.5rem; font-weight: 800; color: #ffffff; line-height: 1.2; margin-bottom: 20px;}
    .hero-subtitle { font-size: 1.1rem; color: #94a3b8; font-weight: 400; margin-bottom: 40px; }
    
    /* Feature Cards */
    .feature-card {
        border-radius: 16px; border: none; transition: all 0.3s ease;
        background: #ffffff; box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        height: 100%;
    }
    .feature-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
    .icon-box {
        width: 70px; height: 70px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 30px; margin-bottom: 20px;
    }
    .bg-light-blue { background: #e0f2fe; color: #0284c7; }
    .bg-light-warning { background: #fef3c7; color: #d97706; }
    .bg-light-success { background: #dcfce7; color: #15803d; }
    
    .btn-custom { padding: 12px 30px; font-weight: 600; font-size: 16px; border-radius: 50px; }
  </style>
</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  <!-- NAVBAR -->
  <nav class="main-header navbar navbar-expand-md navbar-light">
    <div class="container">
      <a href="#" class="navbar-brand d-flex align-items-center">
        <!-- Panggil Logo yang udah lu buat -->
        <img src="dist/img/poto_logo_putracell.png" alt="Putra Cell Logo" class="brand-image img-circle elevation-2" style="opacity: 1; width: 40px; height: 40px; background: white; padding: 2px;">
        <span class="brand-text font-weight-bold ml-2">PUTRA <span class="text-primary">CELL</span></span>
      </a>

      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse order-3" id="navbarCollapse">
        <ul class="navbar-nav ml-auto align-items-center">
          <li class="nav-item">
            <a href="#" class="nav-link font-weight-bold text-dark">Beranda</a>
          </li>
          <li class="nav-item">
            <a href="#tentang" class="nav-link font-weight-bold text-dark">Tentang Kami</a>
          </li>
          <li class="nav-item ml-md-3 mt-2 mt-md-0">
            <!-- TOMBOL LOGIN ARAHIN KE LOGIN.PHP -->
            <a href="pages/login.php" class="btn btn-primary btn-custom shadow-sm">
              <i class="fas fa-sign-in-alt mr-1"></i> Login Sistem
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- CONTENT WRAPPER -->
  <div class="content-wrapper bg-white">
    
    <!-- HERO SECTION (BAGIAN ATAS) -->
    <div class="hero-section text-left">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-7 col-md-12 text-center text-lg-left" style="z-index: 2;">
            <div class="badge badge-primary px-3 py-2 rounded-pill mb-3 text-uppercase" style="letter-spacing: 1px;">Sistem Informasi Gudang</div>
            <h1 class="hero-title">Manajemen Stok <br>Lebih <span class="text-primary">Mudah</span> & <span class="text-primary">Akurat</span>.</h1>
            <p class="hero-subtitle">Putra Cell Inventory adalah sistem berbasis web untuk memantau ketersediaan handphone dan aksesoris di seluruh cabang toko secara Real-Time.</p>
            
            <a href="pages/login.php" class="btn btn-primary btn-custom shadow-lg mr-2 mb-2">
              Masuk ke Dashboard <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>
          <div class="col-lg-5 d-none d-lg-block text-center" style="z-index: 2;">
            <!-- Gambar ilustrasi box/gudang (pake icon gede biar simple) -->
            <i class="fas fa-boxes text-white" style="font-size: 14rem; opacity: 0.9; text-shadow: 0 20px 30px rgba(0,0,0,0.5);"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- FITUR SECTION (OVERVIEW) -->
    <div class="content py-5 bg-light" id="tentang">
      <div class="container py-4">
        <div class="text-center mb-5">
          <h6 class="text-primary font-weight-bold text-uppercase" style="letter-spacing: 1.5px;">Kenapa Sistem Ini Dibuat?</h6>
          <h2 class="font-weight-bold text-dark">Overview Fitur Utama</h2>
        </div>

        <div class="row">
          <!-- Card 1 -->
          <div class="col-md-4 mb-4">
            <div class="card feature-card p-4">
              <div class="icon-box bg-light-blue">
                <i class="fas fa-mobile-alt"></i>
              </div>
              <h5 class="font-weight-bold text-dark">Katalog Terpusat</h5>
              <p class="text-muted small mb-0">Semua data barang dari berbagai kategori (HP, Charger, LCD, dll) tersimpan rapi dalam satu database yang mudah diakses.</p>
            </div>
          </div>
          
          <!-- Card 2 -->
          <div class="col-md-4 mb-4">
            <div class="card feature-card p-4">
              <div class="icon-box bg-light-warning">
                <i class="fas fa-bell"></i>
              </div>
              <h5 class="font-weight-bold text-dark">Notifikasi Cerdas</h5>
              <p class="text-muted small mb-0">Sistem otomatis mendeteksi dan memberi peringatan jika ada stok barang yang menipis atau kosong di gudang maupun cabang.</p>
            </div>
          </div>

          <!-- Card 3 -->
          <div class="col-md-4 mb-4">
            <div class="card feature-card p-4">
              <div class="icon-box bg-light-success">
                <i class="fas fa-chart-line"></i>
              </div>
              <h5 class="font-weight-bold text-dark">Laporan Akurat</h5>
              <p class="text-muted small mb-0">Aktivitas penambahan barang dan laporan stok kosong terekam dengan jelas untuk memudahkan owner melakukan pengecekan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- FOOTER -->
  <footer class="main-footer text-center border-0 py-4 bg-white">
    <strong>Copyright &copy; 2026 Putra Cell Inventory System.</strong> All rights reserved. <br>
    <small class="text-muted">Developed by Group 4.</small>
  </footer>

</div>

<!-- Scripts -->
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>