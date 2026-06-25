<?php
session_start();
include "../config/koneksi.php";

// Proteksi: Pastikan cuma admin yang bisa buka halaman ini
if (!isset($_SESSION['is_logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: barang.php");
    exit();
}

$id = $_GET['id'];
$data = mysqli_query($conn, "SELECT * FROM barang WHERE id='$id'");
$row = mysqli_fetch_assoc($data);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang | Putra Cell</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,600,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
    </style>
</head>
<body class="p-4 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card shadow-lg" style="width: 100%; max-width: 600px; border-radius: 12px; border: none;">
        <div class="card-header bg-warning text-dark" style="border-radius: 12px 12px 0 0;">
            <h3 class="card-title font-weight-bold"><i class="fas fa-edit mr-2"></i> Edit Data Barang</h3>
        </div>
        <form action="../actions/edit_aksi.php" method="POST">
            <div class="card-body">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Kode Barang</label>
                            <input type="text" name="kode_barang" class="form-control" value="<?php echo $row['kode_barang']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Nama Barang</label>
                            <input type="text" name="nama_barang" class="form-control" value="<?php echo $row['nama_barang']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Kategori</label>
                            <input type="text" name="kategori" class="form-control" value="<?php echo $row['kategori']; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Satuan</label>
                            <select name="satuan" class="form-control" required>
                                <option value="Pcs" <?php echo ($row['satuan'] == 'Pcs') ? 'selected' : ''; ?>>Pcs</option>
                                <option value="Unit" <?php echo ($row['satuan'] == 'Unit') ? 'selected' : ''; ?>>Unit</option>
                                <option value="Pack" <?php echo ($row['satuan'] == 'Pack') ? 'selected' : ''; ?>>Pack</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Lokasi Rak</label>
                            <select name="lokasi_rak" class="form-control" required>
                                <option value="Etalase Depan" <?php echo ($row['lokasi_rak'] == 'Etalase Depan') ? 'selected' : ''; ?>>Etalase Depan</option>
                                <option value="Rak Aksesoris A" <?php echo ($row['lokasi_rak'] == 'Rak Aksesoris A') ? 'selected' : ''; ?>>Rak Aksesoris A</option>
                                <option value="Rak Aksesoris B" <?php echo ($row['lokasi_rak'] == 'Rak Aksesoris B') ? 'selected' : ''; ?>>Rak Aksesoris B</option>
                                <option value="Gudang Belakang" <?php echo ($row['lokasi_rak'] == 'Gudang Belakang') ? 'selected' : ''; ?>>Gudang Belakang</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small text-muted text-uppercase">Cabang Toko</label>
                            <select name="cabang_toko" class="form-control" required>
                                <option value="Kuningan" <?php echo ($row['cabang_toko'] == 'Kuningan') ? 'selected' : ''; ?>>Kuningan</option>
                                <option value="Jakarta" <?php echo ($row['cabang_toko'] == 'Jakarta') ? 'selected' : ''; ?>>Jakarta</option>
                                <option value="Bandung" <?php echo ($row['cabang_toko'] == 'Bandung') ? 'selected' : ''; ?>>Bandung</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="small text-muted text-uppercase">Stok Saat Ini</label>
                    <input type="number" name="stok" class="form-control font-weight-bold text-primary" value="<?php echo $row['stok']; ?>" required>
                </div>

            </div>
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end">
                <a href="../pages/barang.php" class="btn btn-light border shadow-sm mr-2 font-weight-bold">Batal</a>
                <button type="submit" class="btn btn-warning shadow-sm font-weight-bold"><i class="fas fa-save mr-1"></i> Update Data</button>
            </div>
        </form>
    </div>
</body>
</html>