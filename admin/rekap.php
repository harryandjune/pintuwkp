<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
    exit(); 
}

// 1. LOGIKA HITUNG STATISTIK TAMU
$tgl_sekarang = date('Y-m-d');
$bulan_ini    = date('m');
$tahun_ini    = date('Y');

// A. Tamu Hari Ini
$tamu_hari_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu WHERE tanggal = '$tgl_sekarang'"));

// B. Tamu Minggu Ini (Senin - Minggu)
$tamu_minggu_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu WHERE YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)"));

// C. Tamu Bulan Ini
$tamu_bulan_ini = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu WHERE MONTH(tanggal) = '$bulan_ini' AND YEAR(tanggal) = '$tahun_ini'"));

// D. Total Semua Tamu
$total_tamu = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu"));

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Tamu - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 40px 20px 70px; border-radius: 0 0 40px 40px; }

        /* Stat Card Styling */
        .stat-card-tamu {
            border: none;
            border-radius: 25px;
            background: #fff;
            padding: 20px 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .stat-card-tamu:active { transform: scale(0.95); }

        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 10px;
        }

        /* Warna Ikon Berbeda agar menarik */
        .bg-blue   { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
        .bg-cyan   { background: rgba(56, 189, 248, 0.1); color: #0ea5e9; }
        .bg-indigo { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        .bg-slate  { background: rgba(30, 41, 59, 0.1); color: #1e293b; }

        .count-number { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 0px; }
        .stat-label { font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        .btn-view-all {
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 15px;
            padding: 14px;
            font-weight: 600;
            font-size: 13px;
            width: 100%;
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
        }
    </style>
</head>
<body>

    <div class="header-section shadow text-center">
        <div class="container">
            <h6 class="opacity-75 mb-1">Manajemen Buku Tamu</h6>
            <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h4>
        </div>
    </div>

    <div class="container mt-n4" style="margin-top: -35px;">
        <!-- Grid Statistik 2x2 Simetris -->
        <div class="row g-3 px-2">
            <!-- Tamu Hari Ini -->
            <div class="col-6">
                <div class="stat-card-tamu shadow-sm">
                    <div class="icon-circle bg-blue">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="count-number"><?php echo $tamu_hari_ini; ?></div>
                    <div class="stat-label">Hari Ini</div>
                </div>
            </div>
            <!-- Tamu Minggu Ini -->
            <div class="col-6">
                <div class="stat-card-tamu shadow-sm">
                    <div class="icon-circle bg-cyan">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="count-number"><?php echo $tamu_minggu_ini; ?></div>
                    <div class="stat-label">Minggu Ini</div>
                </div>
            </div>
            <!-- Tamu Bulan Ini -->
            <div class="col-6">
                <div class="stat-card-tamu shadow-sm">
                    <div class="icon-circle bg-indigo">
                        <i class="bi bi-calendar-range"></i>
                    </div>
                    <div class="count-number"><?php echo $tamu_bulan_ini; ?></div>
                    <div class="stat-label">Bulan Ini</div>
                </div>
            </div>
            <!-- Total Semua Tamu -->
            <div class="col-6">
                <div class="stat-card-tamu shadow-sm">
                    <div class="icon-circle bg-slate">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="count-number"><?php echo $total_tamu; ?></div>
                    <div class="stat-label">Total Tamu</div>
                </div>
            </div>
        </div>

        <!-- Menu Aksi -->
        <div class="mt-5 px-2">
            
            
            <a href="tamu_list.php" class="btn btn-view-all mb-4 shadow-sm text-white text-decoration-none d-block text-center">
                <i class="bi bi-journal-richtext me-2"></i> LIHAT LOG PENGUNJUNG
            </a>
            
        </div>

        <div class="text-center mt-5 text-muted" style="font-size: 10px;">
            &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo htmlspecialchars($sett['copyright']); ?>
        </div>
    </div>

    <!-- Navigasi Bawah Admin -->
    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>