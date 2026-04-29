<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "super_admin") { header("location:../login.php"); exit(); }

// Statistik Global untuk Super Admin
$total_user = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users"));
$total_booking_gedung = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi"));
$total_booking_mobil = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan"));
$total_tamu = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #312e81, #4338ca); color: white; padding: 40px 20px 60px; border-radius: 0 0 40px 40px; }
        .stat-card { border: none; border-radius: 20px; padding: 20px; background: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.05); text-align: center; }
        .icon-box { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 24px; }
    </style>
</head>
<body>

    <div class="header-section shadow text-center">
        <h6 class="opacity-75 mb-1">Otoritas Tertinggi</h6>
        <h4 class="fw-bold">Super Admin Panel</h4>
        <div class="mt-3 badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm">
             <?php echo $_SESSION['nama']; ?>
        </div>
    </div>

    <div class="container mt-n4" style="margin-top: -30px;">
        <div class="row g-3 px-2">
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='manage_users.php'">
                    <div class="icon-box bg-primary text-white"><i class="bi bi-people"></i></div>
                    <h4 class="fw-bold mb-0"><?php echo $total_user; ?></h4>
                    <small class="text-muted">Total Akun</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='manage_tamu.php'">
                    <div class="icon-box bg-success text-white"><i class="bi bi-journal-text"></i></div>
                    <h4 class="fw-bold mb-0"><?php echo $total_tamu; ?></h4>
                    <small class="text-muted">Buku Tamu</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='manage_reservasi.php'">
                    <div class="icon-box bg-indigo text-white" style="background:#4338ca !important;"><i class="bi bi-building"></i></div>
                    <h4 class="fw-bold mb-0"><?php echo $total_booking_gedung; ?></h4>
                    <small class="text-muted">Booking Gedung</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='manage_reservasi.php?tab=mobil'">
                    <div class="icon-box bg-warning text-dark"><i class="bi bi-car-front"></i></div>
                    <h4 class="fw-bold mb-0"><?php echo $total_booking_mobil; ?></h4>
                    <small class="text-muted">Booking Mobil</small>
                </div>
            </div>
        </div>

        <div class="alert alert-danger mx-2 mt-4 border-0 shadow-sm" style="border-radius: 20px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-lock-fill fs-3 me-3"></i>
                <div>
                    <small class="fw-bold d-block">Area Berisiko Tinggi</small>
                    <small style="font-size: 11px;">Sebagai Super Admin, Anda memiliki wewenang untuk menghapus data secara permanen. Lakukan dengan hati-hati.</small>
                </div>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
</body>
</html>