<?php
session_start();
include '../config/koneksi.php';

// Proteksi Admin Kendaraan
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
        .info-box { background: #f8f9fa; border-radius: 12px; padding: 12px; font-size: 12px; }
        .driver-alt-badge { background: #fff8e6; color: #9a6700; border: 1px dashed #f59e0b; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container text-start">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0 text-white">Persetujuan Mobil</h4>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-2 mb-4 d-flex justify-content-between align-items-end">
            <div>
                <h6 class="fw-bold mb-0">Daftar Pengajuan</h6>
                <small class="text-muted">Kelola izin penggunaan armada</small>
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

        if(mysqli_num_rows($data) == 0){
            echo '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p>Tidak ada data pengajuan.</p></div>';
        }

        while($d = mysqli_fetch_array($data)){
            // Format WhatsApp User
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if(substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
            elseif(substr($phone, 0, 1) === '8') $phone = '62' . $phone;
        ?>
        <div class="card approval-card shadow-sm">
            <div class="card-body p-3 text-start">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 9px;"><?php echo htmlspecialchars($d['institusi_peminjam']); ?></span>
                        <h6 class="fw-bold mb-0"><?php echo $d['merk'].' '.$d['model']; ?></h6>
                        <small class="text-muted">PIC: <?php echo $d['nama_lengkap']; ?> 
                            <?php if(!empty($phone)){ ?>
                                <a href="https://wa.me/<?php echo $phone; ?>" target="_blank" class="text-success ms-1"><i class="bi bi-whatsapp"></i></a>
                            <?php } ?>
                        </small>
                    </div>
                    <?php 
                    if($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                    elseif($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                    else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                    ?>
                </div>

                <div class="info-box mb-3">
                    <div class="row mb-2">
                        <div class="col-6 border-end">
                            <small class="text-muted d-block">Waktu Pinjam:</small>
                            <small class="fw-bold"><?php echo date('d M, H:i', strtotime($d['tgl_mulai'])); ?></small>
                        </div>
                        <div class="col-6 ps-3">
                            <small class="text-muted d-block">Kebutuhan Sopir:</small>
                            <?php if($d['pakai_sopir'] == 'ya'){ ?>
                                <small class="fw-bold text-success text-uppercase">SOPIR YAYASAN</small>
                            <?php } else { ?>
                                <div class="driver-alt-badge mt-1">
                                    <i class="bi bi-person-badge"></i> ALT: <?php echo htmlspecialchars($d['nama_sopir_alt']); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="mt-2 border-top pt-2 text-start">
                        <small class="text-muted d-block">Tujuan & Keperluan:</small>
                        <small class="fw-bold d-block text-primary"><?php echo htmlspecialchars($d['tujuan']); ?></small>
                        <small class="text-secondary"><?php echo htmlspecialchars($d['keperluan']); ?></small>
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
        
        <div class="text-center mt-4">
            <p class="text-muted" style="font-size: 10px;">&copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?></p>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>