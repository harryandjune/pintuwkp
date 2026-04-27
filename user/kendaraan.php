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
    <title>Pilih Armada - <?php echo $sett['nama_sistem']; ?></title>
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        /* CARD STYLE COMPACT PREMIUM */
        .car-item-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.04);
            transition: transform 0.2s ease;
            margin-bottom: 15px;
            position: relative;
            border-left: 5px solid #0d6efd; /* Memberi aksen warna di samping */
        }

        .car-item-card:active {
            transform: scale(0.98);
        }

        .car-info {
            padding: 20px;
        }

        .status-pill {
            background: #dcfce7;
            color: #15803d;
            font-size: 10px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 10px;
            text-transform: uppercase;
        }

        .plate-badge {
            font-family: monospace;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #e2e8f0;
        }

        .spec-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .spec-box {
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
        }

        .spec-box i {
            font-size: 16px;
            color: #0d6efd;
            margin-right: 8px;
        }

        .spec-box span {
            font-size: 11px;
            color: #475569;
            font-weight: 600;
        }

        .btn-book-now {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            border: none;
            border-radius: 12px;
            padding: 10px;
            font-weight: 600;
            color: white;
            width: 100%;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-section shadow">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-0">Pilih Armada</h4>
                    <small class="opacity-75">Transportasi Yayasan PH</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status_kendaraan='tersedia' ORDER BY merk ASC");
            if(mysqli_num_rows($data) == 0){
                echo '<div class="text-center py-5"><i class="bi bi-car-front text-muted fs-1"></i><p class="text-muted">Tidak ada armada tersedia saat ini.</p></div>';
            }
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="col-12 col-md-6">
                <div class="card car-item-card">
                    <div class="car-info">
                        <!-- Top Row: Plate & Status -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="plate-badge"><?php echo $d['nomor_plat']; ?></span>
                            <span class="status-pill">Tersedia</span>
                        </div>

                        <!-- Title -->
                        <h5 class="fw-bold text-dark mb-0"><?php echo $d['merk'] . " " . $d['model']; ?></h5>
                        <small class="text-muted">Produksi Th <?php echo $d['tahun_produksi']; ?></small>

                        <!-- Specifications -->
                        <div class="spec-grid">
                            <div class="spec-box">
                                <i class="bi bi-people-fill"></i>
                                <span><?php echo $d['kapasitas']; ?> Kursi</span>
                            </div>
                            <div class="spec-box">
                                <i class="bi bi-gear-wide-connected"></i>
                                <span><?php echo $d['jenis_kendaraan']; ?></span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <a href="booking_kendaraan.php?id=<?php echo $d['id_kendaraan']; ?>" class="btn btn-book-now">
                            Pesan Mobil Ini <i class="bi bi-arrow-right-short ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="text-center mt-3 mb-5 px-4">
            <p class="text-muted small">Armada hanya digunakan untuk keperluan operasional resmi Yayasan.</p>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <!-- Script jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>