<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Transport - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .approval-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .status-badge { font-size: 10px; padding: 4px 10px; border-radius: 8px; font-weight: 600; text-transform: uppercase; }
        .info-box { background: #f8f9fa; border-radius: 12px; padding: 10px; font-size: 12px; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Persetujuan Mobil</h4>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-4 d-flex justify-content-between align-items-end">
            <div>
                <h6 class="fw-bold mb-0">Daftar Pengajuan</h6>
                <small class="text-muted">Kelola izin armada</small>
            </div>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3"><?php echo $count_pending; ?> Baru</span>
        </div>

        <?php 
        $query = "SELECT r.*, u.nama_lengkap, u.no_wa, k.merk, k.model, k.nomor_plat 
                  FROM reservasi_kendaraan r 
                  JOIN users u ON r.user_id = u.id 
                  JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                  ORDER BY (status = 'pending') DESC, r.id DESC";
        
        $data = mysqli_query($koneksi, $query);

        while($d = mysqli_fetch_array($data)){
            // Format WhatsApp User
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if(substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
        ?>
        <div class="card approval-card shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 9px;"><?php echo $d['institusi_peminjam']; ?></span>
                        <h6 class="fw-bold mb-0"><?php echo $d['merk'].' '.$d['model']; ?></h6>
                        <small class="text-muted">PIC: <?php echo $d['nama_lengkap']; ?> 
                            <a href="https://wa.me/<?php echo $phone; ?>" target="_blank" class="text-success ms-1"><i class="bi bi-whatsapp"></i></a>
                        </small>
                    </div>
                    <?php 
                    if($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                    elseif($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                    else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                    ?>
                </div>

                <div class="info-box mb-3">
                    <div class="row">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Waktu:</small>
                            <small class="fw-bold"><?php echo date('d M, H:i', strtotime($d['tgl_mulai'])); ?></small>
                        </div>
                        <div class="col-6 ps-3">
                            <small class="text-muted d-block">Sopir:</small>
                            <small class="fw-bold text-uppercase"><?php echo $d['pakai_sopir']; ?></small>
                        </div>
                    </div>
                    <div class="mt-2 border-top pt-2">
                        <small class="text-muted d-block">Tujuan & Keperluan:</small>
                        <small class="fw-bold d-block text-primary"><?php echo $d['tujuan']; ?></small>
                        <small class="text-secondary"><?php echo $d['keperluan']; ?></small>
                    </div>
                </div>

                <?php if($d['status'] == 'pending') { ?>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=disetujui" class="btn btn-success w-100 fw-bold py-2 shadow-sm" style="border-radius: 12px;" onclick="return confirm('Setujui peminjaman ini?')">Setujui</a>
                    </div>
                    <div class="col-6">
                        <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-outline-danger w-100 fw-bold py-2" style="border-radius: 12px;" onclick="return confirm('Tolak peminjaman ini?')">Tolak</a>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>

    <?php include 'navbar.php'; ?>
</body>
</html>