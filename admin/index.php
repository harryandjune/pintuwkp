<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// Ambil statistik untuk dashboard
$count_pending   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
$count_approved  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='disetujui'"));
$count_ruangan   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan"));
$count_user      = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users WHERE role='user'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $sett['nama_sistem']; ?></title>
    <!-- Favicon Dinamis -->
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 100px;
        }

        /* Header Styling */
        .header-section {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 40px 20px 60px;
            border-radius: 0 0 40px 40px;
        }

        /* Stat Card Styling */
        .stat-card {
            border: none;
            border-radius: 20px;
            padding: 20px;
            background: #fff;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .stat-card:active {
            transform: scale(0.95);
        }

        .icon-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .copyright-text {
            font-size: 11px;
            color: #adb5bd;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container text-center">
            <h6 class="opacity-75 mb-1">Administrator Dashboard</h6>
            <h4 class="fw-bold"><?php echo $sett['nama_sistem']; ?></h4>

            <!-- Link ke users.php (Halaman Update Akun) -->
            <a href="users.php" class="text-decoration-none">
                <div class="mt-3 badge bg-primary px-3 py-2 rounded-pill shadow-sm border border-light border-opacity-25" style="transition: all 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="bi bi-person-circle me-2"></i>
                    <?php echo $_SESSION['nama']; ?>
                    <i class="bi bi-chevron-right ms-2 small opacity-50"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-3">
        <!-- Notifikasi Badge jika ada yang pending -->
        <?php if ($count_pending > 0) { ?>
            <div class="alert alert-warning border-0 shadow-sm mx-2 mb-4 d-flex align-items-center" style="border-radius: 15px; margin-top: -20px;">
                <i class="bi bi-bell-fill fs-4 me-3"></i>
                <small class="fw-bold" style="font-size: 12px;">Ada <?php echo $count_pending; ?> pengajuan menunggu persetujuan!</small>
            </div>
        <?php } ?>

        <!-- Grid Statistik -->
        <div class="row g-3 px-2">
            <!-- CARD PENDING -->
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='persetujuan.php'">
                    <div class="icon-box bg-warning text-white shadow-sm mx-auto">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_pending; ?></h4>
                    <small class="text-muted d-block">Pending</small>
                </div>
            </div>

            <!-- CARD DISETUJUI -->
            <div class="col-6">
                <div class="stat-card text-center">
                    <div class="icon-box bg-success text-white shadow-sm mx-auto">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_approved; ?></h4>
                    <small class="text-muted d-block">Disetujui</small>
                </div>
            </div>

            <!-- CARD RUANGAN -->
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='ruangan.php'">
                    <div class="icon-box bg-primary text-white shadow-sm mx-auto">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_ruangan; ?></h4>
                    <small class="text-muted d-block">Ruangan</small>
                </div>
            </div>

            <!-- CARD KALENDER -->
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='kalender.php'">
                    <div class="icon-box bg-danger text-white shadow-sm mx-auto">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_approved; ?></h4>
                    <small class="text-muted d-block">Kalender</small>
                </div>
            </div>
        </div>
        <!-- Tombol Booking Manual (Baris Baru) -->
        <div class="col-12 mt-2">
            <div class="stat-card" onclick="location.href='booking_manual.php'" style="background: #1e293b; color: #38bdf8; border: 1px solid #38bdf8;">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="bi bi-plus-circle-fill fs-4 me-2"></i>
                    <h6 class="fw-bold mb-0">Booking Ruangan Langsung</h6>
                </div>
            </div>
        </div>

        <!-- Kartu Identitas Admin -->
        <div class="card stat-card mx-2 mt-4 text-center">
            <h6 class="fw-bold mb-2">Manajemen <?php echo $sett['nama_sistem']; ?></h6>
            <p class="small text-muted px-3 mb-0"><?php echo $sett['deskripsi']; ?></p>
            <div class="copyright-text">
                &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
            </div>
        </div>
    </div>


    <!-- Floating Bottom Navigation (6 Items Optimized) -->
    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>