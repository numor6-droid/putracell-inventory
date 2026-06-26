<?php
session_start();
include "../config/koneksi.php";

$pesan_error = "";
$login_sukses = false; // PENGAMAN 1: Inisialisasi variabel di awal
$remembered_user = isset($_COOKIE['remember_user']) ? $_COOKIE['remember_user'] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $sandi = $_POST['password'];
    $remember = isset($_POST['remember']) ? $_POST['remember'] : "";

  // CEK DATABASE
        $sql = "SELECT * FROM pelanggan_cell WHERE username = '$user'";
        $query = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            if ($sandi == $data['password']) {
                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_email']   = $data['nama_lengkap'];
                $_SESSION['role']         = $data['role'];    // Ambil dari DB
                $_SESSION['cabang']       = $data['cabang'];  // Ambil dari DB
                $_SESSION['foto_user']    = $data['foto'];
                $_SESSION['username']     = $user; 
                $login_sukses = true;
            } else {
                $login_sukses = false;
                $pesan_error = "Password salah!";
            }
        } else {
            $login_sukses = false;
            $pesan_error = "Username tidak terdaftar!";
        }
    }

    // PENGAMAN 2: Cek pakai isset() biar bebas error "Undefined variable"
    if (isset($login_sukses) && $login_sukses === true) {
        if ($remember == "on") {
            setcookie('remember_user', $user, time() + (86400 * 30), "/"); // 30 hari
            setcookie('remember_role', $data['role'], time() + (86400 * 30), "/"); // 30 hari
        }
        header("Location: ../index.php");
        exit(); 
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Putra Cell | Log in</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="../dist/css/adminlte.min.css">
  <style>
    body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-card-body { border-radius: 15px; padding: 30px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
    .brand-text-login { font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 20px; text-align: center; }
    .text-blue { color: #3b82f6; }
    .toggle-password { cursor: pointer; }
    /* Hilangkan ikon mata bawaan browser (untuk Chrome/Edge) */
    input::-ms-reveal,
    input::-ms-clear {
      display: none;
    }

    input[type="password"]::-webkit-contacts-auto-fill-button {
      visibility: hidden;
      display: none !important;
      pointer-events: none;
      position: absolute;
      right: 0;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="text-center mb-4">
  <img src="../dist/img/poto_logo_putracell.png" alt="Logo Putra Cell" class="img-circle elevation-3" style="width: 90px; height: 90px; margin-bottom: 15px; background: white; padding: 5px;">
  <div class="brand-text-login" style="margin-bottom: 0;">PUTRA <span class="text-blue">CELL</span></div>
  </div>
  <div class="card shadow-lg" style="border-radius: 15px; border: none;">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Silakan masuk ke sistem</p>

      <?php if ($pesan_error != ""): ?>
        <div class="alert alert-danger p-2 small text-center"><?php echo $pesan_error; ?></div>
      <?php endif; ?>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="Username" value="<?php echo $remembered_user; ?>" required>
          <div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>
        </div>
        
        <div class="input-group mb-3">
          <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
             <div class="input-group-text toggle-password" id="tombolMata">
              <span class="fas fa-eye" id="ikonMata"></span>
            </div>
          </div>
        </div>
        
        <div class="row align-items-center">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember" name="remember" <?php echo $remembered_user ? "checked" : ""; ?>>
              <label for="remember" class="small text-muted font-weight-normal"> Ingat Saya</label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
        </div>
      </form>

      
      <div class="text-center mt-4">
        <p class="mb-0 small text-muted">
          Belum punya akun? <a href="registrasi.php" class="text-primary font-weight-bold">Daftar Akun Baru</a>
        </p>
      </div>

      <p class="mt-4 mb-0 text-center small text-muted">
        &copy; 2026 <b>Putra Cell</b> Inventory
      </p>

    </div>
  </div>
</div>

<script src="../plugins/jquery/jquery.min.js"></script>
<script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
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