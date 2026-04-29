<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "admin_kendaraan") { header("location:../login.php"); exit(); }

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id_kendaraan='$id'");
$d = mysqli_fetch_array($query);

if (!$d) { echo "Data tidak ditemukan"; exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Armada - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 50px; }
        .header-section { background: #0f172a; color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container text-start">
            <a href="kendaraan.php" class="text-white me-3 fs-4 text-decoration-none"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 d-inline">Edit Armada</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="kendaraan_aksi.php?aksi=edit" method="post">
                <input type="hidden" name="id_kendaraan" value="<?php echo $d['id_kendaraan']; ?>">
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-start d-block">Nomor Plat</label>
                    <input type="text" name="nomor_plat" class="form-control" value="<?php echo $d['nomor_plat']; ?>" required>
                </div>
                <div class="row text-start">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Merk</label>
                        <input type="text" name="merk" class="form-control" value="<?php echo $d['merk']; ?>" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Model</label>
                        <input type="text" name="model" class="form-control" value="<?php echo $d['model']; ?>" required>
                    </div>
                </div>
                <div class="row text-start">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Tahun Produksi</label>
                        <input type="number" name="tahun_produksi" class="form-control" value="<?php echo $d['tahun_produksi']; ?>" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Kapasitas (Seat)</label>
                        <input type="number" name="kapasitas" class="form-control" value="<?php echo $d['kapasitas']; ?>" required>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" class="form-select" required>
                        <option value="MPV" <?php if($d['jenis_kendaraan'] == 'MPV') echo 'selected'; ?>>MPV</option>
                        <option value="Minibus" <?php if($d['jenis_kendaraan'] == 'Minibus') echo 'selected'; ?>>Minibus</option>
                        <option value="Sedan" <?php if($d['jenis_kendaraan'] == 'Sedan') echo 'selected'; ?>>Sedan</option>
                        <option value="Pikap" <?php if($d['jenis_kendaraan'] == 'Pikap') echo 'selected'; ?>>Pikap</option>
                        <option value="Motor" <?php if($d['jenis_kendaraan'] == 'Motor') echo 'selected'; ?>>Motor/Roda 2</option>
                    </select>
                </div>
                <div class="mb-4 text-start">
                    <label class="form-label small fw-bold">Status Kendaraan</label>
                    <select name="status_kendaraan" class="form-select" required>
                        <option value="tersedia" <?php if($d['status_kendaraan'] == 'tersedia') echo 'selected'; ?>>Tersedia / Siap Jalan</option>
                        <option value="perbaikan" <?php if($d['status_kendaraan'] == 'perbaikan') echo 'selected'; ?>>Dalam Perbaikan (Bengkel)</option>
                        <option value="nonaktif" <?php if($d['status_kendaraan'] == 'nonaktif') echo 'selected'; ?>>Nonaktif / Dihapus</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 text-dark shadow-sm" style="border-radius: 15px;">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
</body>
</html>