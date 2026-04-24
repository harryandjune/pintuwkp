<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
}

// Hitung jumlah pending untuk lencana menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ruangan - PINTU WKP</title>
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
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        /* Room Card Styling */
        .manage-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .icon-box-room {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .btn-add {
            border-radius: 15px;
            padding: 10px 20px;
            font-weight: 600;
            background: #38bdf8;
            border: none;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
        }

        .btn-delete {
            border-radius: 10px;
            padding: 5px 12px;
            font-size: 12px;
        }

    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Data Ruangan</h4>
            </div>
            <a href="ruangan_tambah.php" class="btn btn-add text-white">
                <i class="bi bi-plus-lg"></i> Tambah
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="px-2 mb-4">
            <h6 class="fw-bold mb-0 text-dark">Daftar Kamar & Ruangan</h6>
            <small class="text-muted small">Total: <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan")); ?> Aset Terdaftar</small>
        </div>

        <div class="row g-3">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM ruangan ORDER BY tipe DESC");
            if(mysqli_num_rows($data) == 0){
                echo '<div class="text-center py-5 text-muted">Belum ada ruangan. Klik tombol tambah.</div>';
            }
            while($d = mysqli_fetch_array($data)){
            ?>
            
            <div class="col-12 col-md-6">
                <div class="card manage-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <!-- Icon berdasarkan tipe -->
                            <div class="icon-box-room me-3 <?php echo ($d['tipe'] == 'guest_house' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning'); ?>">
                                <i class="bi <?php echo ($d['tipe'] == 'guest_house' ? 'bi-door-closed-fill' : 'bi-person-video3'); ?>"></i>
                            </div>
                            
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0"><?php echo $d['nama_ruangan']; ?></h6>
                                <span class="badge bg-light text-dark small" style="font-size: 10px;">
                                    <?php echo ($d['tipe'] == 'guest_house' ? 'Guest House' : 'Meeting Room'); ?>
                                </span>
                            </div>

                            <div class="text-end">
                                <a href="ruangan_aksi.php?id=<?php echo $d['id']; ?>&aksi=hapus" class="btn btn-outline-danger btn-delete" onclick="return confirm('Hapus ruangan ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>

                        <hr class="my-3 opacity-25">

                        <div class="row text-center">
                            <div class="col-4 border-end">
                                <small class="text-muted d-block" style="font-size: 10px;">Kapasitas</small>
                                <small class="fw-bold text-dark"><?php echo $d['kapasitas']; ?> Org</small>
                            </div>
                            <div class="col-8 text-start px-3">
                                <small class="text-muted d-block" style="font-size: 10px;">Fasilitas</small>
                                <small class="text-dark text-truncate d-block" style="font-size: 11px; max-width: 150px;"><?php echo $d['fasilitas']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php } ?>
        </div>

        <div class="text-center mt-5">
            <p class="text-muted" style="font-size: 10px;">&copy; 2026 YPPH - Kantor WKP Management</p>
        </div>
    </div>

   <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>