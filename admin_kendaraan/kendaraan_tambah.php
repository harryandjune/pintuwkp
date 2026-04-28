<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Armada - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 50px;
        }

        .header-section {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        .form-card {
            border: none;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="kendaraan.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Tambah Armada</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="kendaraan_aksi.php?aksi=tambah" method="post">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nomor Plat</label>
                    <input type="text" name="nomor_plat" class="form-control" placeholder="Contoh: B 1234 WKP" required>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Merk</label>
                        <input type="text" name="merk" class="form-control" placeholder="Toyota" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Model</label>
                        <input type="text" name="model" class="form-control" placeholder="Innova" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Tahun Produksi</label>
                        <input type="number" name="tahun_produksi" class="form-control" placeholder="2023" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" class="form-control" placeholder="7" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label small fw-bold">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" class="form-control" required>
                        <option value="MPV">MPV</option>
                        <option value="Minibus">Minibus</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Truck">Truck</option>
                        <option value="Pickup">Pickup</option>
                        <option value="Motor">Motor/Roda 2</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 text-dark shadow-sm" style="border-radius: 15px;">Simpan Kendaraan</button>
            </form>
        </div>
    </div>

</body>

</html>