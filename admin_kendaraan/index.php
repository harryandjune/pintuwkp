<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Admin Kendaraan yang boleh masuk
if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

// Ambil statistik khusus kendaraan
$count_pending   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
$count_approved  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='disetujui'"));
$count_mobil     = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_kendaraan FROM kendaraan"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Transport - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 40px 20px 60px; border-radius: 0 0 40px 40px; }
        .stat-card { border: none; border-radius: 20px; padding: 20px; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: 0.3s; }
        .icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 10px; }
    </style>
</head>
<body>

    <div class="header-section shadow text-center">
        <h6 class="opacity-75 mb-1">Manajemen Transportasi</h6>
        <h4 class="fw-bold">PINTU WKP</h4>
        <div class="mt-3 badge bg-warning text-dark px-3 py-2 rounded-pill">
            <i class="bi bi-person-badge-fill me-2"></i> <?php echo $_SESSION['nama']; ?>
        </div>
    </div>

    <div class="container mt-4">
        <?php if($count_pending > 0) { ?>
        <div class="alert alert-warning border-0 shadow-sm mx-2 mb-4 d-flex align-items-center" style="border-radius: 15px;">
            <i class="bi bi-bell-fill fs-4 me-3"></i>
            <small class="fw-bold">Ada <?php echo $count_pending; ?> pengajuan kendaraan menunggu konfirmasi!</small>
        </div>
        <?php } ?>

        <div class="row g-3 px-2">
            <div class="col-6">
                <div class="stat-card" onclick="location.href='persetujuan.php'">
                    <div class="icon-box bg-warning text-white shadow-sm">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?php echo $count_pending; ?></h4>
                    <small class="text-muted">Pending</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card">
                    <div class="icon-box bg-success text-white shadow-sm">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?php echo $count_approved; ?></h4>
                    <small class="text-muted">Aktif</small>
                </div>
            </div>
            <div class="col-12">
                <div class="stat-card" onclick="location.href='kendaraan.php'">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary text-white shadow-sm mb-0 me-3">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-0"><?php echo $count_mobil; ?> Armada</h4>
                            <small class="text-muted">Kelola Data Kendaraan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>