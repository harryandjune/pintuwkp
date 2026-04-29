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
        .car-card { border: none; border-radius: 25px; background: #fff; margin-bottom: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.03); overflow: hidden; transition: all 0.3s ease; }
        .btn-add { border-radius: 15px; background: #f59e0b; border: none; color: #fff; font-weight: 600; padding: 10px 20px; }
        .plat-nomor { background: #f8f9fa; color: #1e293b; font-weight: 600; font-size: 12px; padding: 4px 10px; border-radius: 10px; border: 1px solid #e2e8f0; letter-spacing: 0.5px; }
        .spec-item { background: #f8f9fa; padding: 8px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; color: #475569; display: flex; align-items: center; }
        .spec-item i { margin-right: 5px; font-size: 14px; }
        .btn-action-sm { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; transition: 0.2s; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center text-start">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Data Armada</h4>
            </div>
            <a href="kendaraan_tambah.php" class="btn btn-add shadow-sm text-white"><i class="bi bi-plus-lg"></i></a>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-4 text-start">
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
                <div class="card car-card text-start">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark"><?php echo $d['merk'] . " " . $d['model']; ?></h5>
                                <span class="plat-nomor"><i class="bi bi-card-heading me-1"></i> <?php echo $d['nomor_plat']; ?></span>
                            </div>
                            <span class="badge <?php echo ($d['status_kendaraan'] == 'tersedia' ? 'bg-success' : 'bg-danger'); ?> text-white" style="border-radius: 8px; font-size: 10px; padding: 6px 10px;">
                                <?php echo strtoupper($d['status_kendaraan']); ?>
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            <div class="spec-item"><i class="bi bi-people-fill text-warning"></i> <?php echo $d['kapasitas']; ?> Kursi</div>
                            <div class="spec-item"><i class="bi bi-fuel-pump-fill text-warning"></i> <?php echo $d['jenis_kendaraan']; ?></div>
                            <div class="spec-item"><i class="bi bi-calendar-check-fill text-warning"></i> Th <?php echo $d['tahun_produksi']; ?></div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <small class="text-muted" style="font-size: 11px;">ID: #0<?php echo $d['id_kendaraan']; ?></small>
                            <div class="d-flex gap-2">
                                <!-- Tombol Edit -->
                                <a href="kendaraan_edit.php?id=<?php echo $d['id_kendaraan']; ?>" class="btn btn-outline-primary btn-action-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <!-- Tombol Hapus -->
                                <a href="kendaraan_aksi.php?id=<?php echo $d['id_kendaraan']; ?>&aksi=hapus" 
                                   class="btn btn-outline-danger btn-action-sm" 
                                   onclick="return confirm('Hapus kendaraan ini?')">
                                    <i class="bi bi-trash-fill"></i>
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