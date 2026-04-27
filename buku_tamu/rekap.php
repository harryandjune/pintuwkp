<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "admin") { header("location:../login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Tamu - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --mustard: #E1AD01; --dark-gray: #2C2C2C; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: var(--dark-gray); color: white; padding: 30px 20px 50px; border-bottom: 4px solid var(--mustard); border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .guest-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border-right: 5px solid var(--mustard); }
        .avatar-box { width: 45px; height: 45px; background: var(--soft-gray); color: var(--dark-gray); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid #eee; }
        .btn-chat { background: var(--mustard); color: var(--dark-gray); border: none; font-weight: 600; font-size: 10px; border-radius: 10px; padding: 5px 12px; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="../admin/index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h4 class="fw-bold mb-0 text-white">Log Pengunjung</h4>
                <small class="opacity-75">Manajemen Buku Tamu</small>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">Riwayat Tamu</h6>
            <span class="badge bg-dark text-white px-3 py-2 rounded-pill" style="font-size: 10px;">
                Total: <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu")); ?>
            </span>
        </div>

        <?php 
        $res = mysqli_query($koneksi, "SELECT * FROM buku_tamu ORDER BY id DESC");
        while($t = mysqli_fetch_array($res)){
            $inisial = strtoupper(substr($t['nama'], 0, 1));
            $phone = preg_replace('/[^0-9]/', '', $t['no_wa'] ?? '');
            if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
        ?>
        <div class="card guest-card">
            <div class="card-body p-3">
                <div class="d-flex align-items-start mb-2">
                    <div class="avatar-box me-3"><?php echo $inisial; ?></div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($t['nama']); ?></h6>
                        <small class="text-muted" style="font-size: 10px;"><?php echo htmlspecialchars($t['instansi']); ?></small>
                    </div>
                    <small class="text-muted" style="font-size: 9px;"><?php echo date('d/m, H:i', strtotime($t['created_at'])); ?></small>
                </div>
                
                <div class="p-2 rounded-3 mb-3" style="font-size: 12px; background: #fcf8e3; border: 1px solid #faebcc;">
                    <i class="bi bi-info-circle-fill me-2 text-warning"></i> <?php echo htmlspecialchars($t['maksud_tujuan']); ?>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted" style="font-size: 10px;"><i class="bi bi-geo-alt-fill me-1"></i> <?php echo htmlspecialchars($t['alamat']); ?></small>
                    <a href="https://wa.me/<?php echo $phone; ?>" target="_blank" class="btn btn-chat shadow-sm">
                        <i class="bi bi-whatsapp me-1"></i> HUBUNGI
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php include '../admin/navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>