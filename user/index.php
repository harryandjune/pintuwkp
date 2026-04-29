<?php
session_start();
include '../config/koneksi.php';

// Proteksi User
if (!isset($_SESSION['role']) || $_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: 10px; }
        
        /* Menu Card Styling */
        .cat-card { border: none; border-radius: 25px; transition: 0.3s; background: #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.05); text-align: center; padding: 25px 15px; text-decoration: none; color: inherit; display: block; height: 100%; border: 1px solid rgba(0,0,0,0.02); }
        .cat-card:active { transform: scale(0.95); background-color: #f1f3f5; }
        
        .icon-box { width: 65px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 30px; }
        
        /* Warna Ikon */
        .bg-gh { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
        .bg-mr { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-car { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .bg-guest { background: rgba(25, 135, 84, 0.1); color: #198754; }
        
        .card-label { font-size: 13px; font-weight: 700; color: #2d3436; }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header-section shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-0 opacity-75">Selamat Datang,</h6>
                <h4 class="fw-bold"><?php echo $_SESSION['nama']; ?> 👋</h4>
            </div>
            <a href="profil.php" class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px; text-decoration: none;">
                <i class="bi bi-person-fill fs-4"></i>
            </a>
        </div>
    </div>

    <!-- Main Menu Layout 2x2 -->
    <div class="container mt-4">
        <h6 class="fw-bold mb-3 px-1 text-muted">Pilih Kategori Layanan</h6>
        <div class="row g-3">
            
            <!-- 1. Guest House -->
            <div class="col-6">
                <a href="booking.php?type=guest_house" class="cat-card">
                    <div class="icon-box bg-gh"><i class="bi bi-houses-fill"></i></div>
                    <span class="card-label">Guest House</span>
                </a>
            </div>

            <!-- 2. Meeting Room -->
            <div class="col-6">
                <a href="booking.php?type=meeting_room" class="cat-card">
                    <div class="icon-box bg-mr"><i class="bi bi-person-workspace"></i></div>
                    <span class="card-label">Meeting Room</span>
                </a>
            </div>

            <!-- 3. Peminjaman Kendaraan -->
            <div class="col-6">
                <a href="booking_kendaraan.php" class="cat-card">
                    <div class="icon-box bg-car"><i class="bi bi-car-front-fill"></i></div>
                    <span class="card-label">Kendaraan</span>
                </a>
            </div>

            <!-- 4. Buku Tamu (Link ke Root) -->
            <div class="col-6">
                <a href="../buku-tamu.php" target="_blank" class="cat-card">
                    <div class="icon-box bg-guest"><i class="bi bi-journal-bookmark-fill"></i></div>
                    <span class="card-label">Buku Tamu</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Info Box -->
    <div class="container mt-4">
        <div class="alert alert-primary border-0 rounded-4 shadow-sm" style="background-color: #e7f1ff;">
            <div class="d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                <small class="text-dark" style="font-size: 11px;">
                    <b>Informasi:</b> Admin akan memverifikasi permintaan Anda dan menentukan unit atau armada yang tersedia secara otomatis.
                </small>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>