<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Putra Cell | Registrasi Baru</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  
  <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  
  <style>.toggle-password { cursor: pointer; width: 40px; justify-content: center; }</style>
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center"><a href="" class="h1"><b>PUTRA</b>CELL</a></div>
    <div class="card-body">
      <p class="login-box-msg">Pendaftaran Akun Baru</p>

      <form action="../actions/insert_pelanggan.php" method="POST">
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" required>
          <div class="input-group-append"><div class="input-group-text" style="width: 40px;"><span class="fas fa-user"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
          <div class="input-group-append"><div class="input-group-text toggle-password" id="tombolMata"><span class="fas fa-eye" id="ikonMata"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Lengkap" required>
          <div class="input-group-append"><div class="input-group-text" style="width: 40px;"><span class="fas fa-id-card"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <input type="text" name="nomor_hp" class="form-control" placeholder="Nomor HP" required>
          <div class="input-group-append"><div class="input-group-text" style="width: 40px;"><span class="fas fa-phone"></span></div></div>
        </div>

        <div class="input-group mb-3">
          <select name="cabang" class="form-control" required>
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
          <div class="input-group-append">
            <div class="input-group-text" style="width: 40px;"><span class="fas fa-store"></span></div>
          </div>
        </div>

        <div class="row mt-4">
          <div class="col-12"><button type="submit" class="btn btn-primary btn-block">Daftar Akun</button></div>
        </div>
      </form>

      <p class="mt-3 mb-0 text-center">
        <a href="login.php" class="text-center">Sudah punya akun? Kembali ke Login</a>
      </p>

    </div>
  </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script>
  $('#tombolMata').click(function() {
    var input = $('#inputPassword');
    var icon = $('#ikonMata');
    if (input.attr('type') === 'password') {
      input.attr('type', 'text');
      icon.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
      input.attr('type', 'password');
      icon.removeClass('fa-eye-slash').addClass('fa-eye');
    }
  });
</script>
</body>
</html>