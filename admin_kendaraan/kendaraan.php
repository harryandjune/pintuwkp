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
    <title>Kelola Armada - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .car-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden; }
        .btn-add { border-radius: 15px; background: #f59e0b; border: none; color: #fff; font-weight: 600; padding: 10px 20px; }
        .status-badge { font-size: 10px; padding: 4px 10px; border-radius: 8px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Data Armada</h4>
            </div>
            <a href="kendaraan_tambah.php" class="btn btn-add shadow-sm"><i class="bi bi-plus-lg"></i></a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-4">
            <h6 class="fw-bold mb-0 text-dark">Daftar Kendaraan Yayasan</h6>
            <small class="text-muted">Kelola status dan informasi mobil</small>
        </div>

        <div class="row g-3">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM kendaraan ORDER BY id_kendaraan DESC");
            if(mysqli_num_rows($data) == 0){
                echo '<div class="text-center py-5 text-muted">Belum ada kendaraan terdaftar.</div>';
            }
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="col-12 col-md-6">
                <div class="card car-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-light rounded-3 p-3 me-3 text-primary">
                                <i class="bi bi-car-front-fill fs-2"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0"><?php echo $d['merk'] . " " . $d['model']; ?></h6>
                                <span class="text-primary small fw-bold"><?php echo $d['nomor_plat']; ?></span>
                            </div>
                            <div class="text-end">
                                <?php if($d['status_kendaraan'] == 'tersedia') { ?>
                                    <span class="status-badge bg-success-subtle text-success">Tersedia</span>
                                <?php } else { ?>
                                    <span class="status-badge bg-danger-subtle text-danger">Perbaikan</span>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="row text-center border-top pt-3">
                            <div class="col-4 border-end">
                                <small class="text-muted d-block" style="font-size: 10px;">Jenis</small>
                                <small class="fw-bold"><?php echo $d['jenis_kendaraan']; ?></small>
                            </div>
                            <div class="col-4 border-end">
                                <small class="text-muted d-block" style="font-size: 10px;">Kapasitas</small>
                                <small class="fw-bold"><?php echo $d['kapasitas']; ?> Seat</small>
                            </div>
                            <div class="col-4">
                                <a href="kendaraan_aksi.php?id=<?php echo $d['id_kendaraan']; ?>&aksi=hapus" 
                                   class="text-danger" onclick="return confirm('Hapus kendaraan ini?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>