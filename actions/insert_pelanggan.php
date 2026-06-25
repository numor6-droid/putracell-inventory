<?php
include "../config/koneksi.php"; // Jembatan ke db_putracell

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Tangkap data dari form registrasi
    $user   = mysqli_real_escape_string($conn, $_POST['username']);
    $pass   = mysqli_real_escape_string($conn, $_POST['password']);
    $nama   = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $no_hp  = mysqli_real_escape_string($conn, $_POST['nomor_hp']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']); // Tambahan: Tangkap cabang
    $role   = 'karyawan'; // Tambahan: Set otomatis jadi karyawan

    // Query masukkan data dengan kolom tambahan
    $query = "INSERT INTO pelanggan_cell (username, password, nama_lengkap, nomor_hp, cabang, role) 
              VALUES ('$user', '$pass', '$nama', '$no_hp', '$cabang', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Registrasi berhasil! Akun Karyawan siap digunakan.');
                window.location.href='../pages/login.php'; 
              </script>";
        exit();
    } else {
        echo "Gagal daftar akun, bosku: " . mysqli_error($conn);
    }

} else {
    header("Location: ../pages/registrasi.php");
    exit();
}
?>