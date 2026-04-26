<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "user") { header("location:../login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa Kendaraan - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .car-card { border: none; border-radius: 20px; transition: 0.3s; overflow: hidden; }
        .badge-status { border-radius: 10px; font-size: 11px; padding: 5px 12px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Peminjaman Kendaraan</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row g-3">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status_kendaraan='tersedia'");
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="col-12 col-md-6">
                <div class="card car-card shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary-subtle text-primary rounded-3 p-3">
                                <i class="bi bi-car-front-fill fs-3"></i>
                            </div>
                            <span class="badge bg-success-subtle text-success badge-status">Tersedia</span>
                        </div>
                        <h5 class="fw-bold mb-1"><?php echo $d['merk'] . " " . $d['model']; ?></h5>
                        <p class="text-muted small mb-3"><i class="bi bi-tag me-1"></i> <?php echo $d['nomor_plat']; ?></p>
                        
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3 text-center">
                                    <small class="text-muted d-block">Kapasitas</small>
                                    <small class="fw-bold"><?php echo $d['kapasitas']; ?> Seat</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3 text-center">
                                    <small class="text-muted d-block">Jenis</small>
                                    <small class="fw-bold"><?php echo $d['jenis_kendaraan']; ?></small>
                                </div>
                            </div>
                        </div>

                        <a href="booking_kendaraan.php?id=<?php echo $d['id_kendaraan']; ?>" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 12px;">Booking Mobil</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
</body>
</html>