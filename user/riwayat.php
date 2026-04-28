<?php
session_start();
include '../config/koneksi.php';

if($_SESSION['role'] != "user") { 
    header("location:../login.php"); 
    exit();
}

$user_id = $_SESSION['id_user'];

// Query 1: Ambil Riwayat Ruangan
$q_ruangan = mysqli_query($koneksi, "SELECT r.*, rm.nama_ruangan, rm.tipe 
                                     FROM reservasi r 
                                     JOIN ruangan rm ON r.ruangan_id = rm.id 
                                     WHERE r.user_id = '$user_id' 
                                     ORDER BY r.id DESC");

// Query 2: Ambil Riwayat Kendaraan
$q_kendaraan = mysqli_query($koneksi, "SELECT r.*, k.merk, k.model, k.nomor_plat 
                                       FROM reservasi_kendaraan r 
                                       JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                                       WHERE r.user_id = '$user_id' 
                                       ORDER BY r.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: 10px; }
        .tab-nav-history { background: #fff; border-radius: 15px; padding: 5px; display: flex; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .tab-nav-history button { flex: 1; border: none; background: none; padding: 10px; border-radius: 12px; font-size: 12px; font-weight: 600; color: #6c757d; transition: 0.3s; }
        .tab-nav-history button.active { background: #0d6efd; color: #fff; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
        .history-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); border-left: 6px solid #ccc; }
        .history-card.pending { border-left-color: #ffc107; }
        .history-card.disetujui { border-left-color: #198754; }
        .history-card.ditolak { border-left-color: #dc3545; }
        .history-card.selesai { border-left-color: #0d6efd; }
        .status-badge { font-size: 9px; padding: 4px 10px; border-radius: 8px; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Riwayat Saya</h4>
        </div>
    </div>

    <div class="container mt-4">
        <div class="tab-nav-history">
            <button id="btn-h-ruangan" class="active">Ruangan</button>
            <button id="btn-h-kendaraan">Kendaraan</button>
        </div>

        <div id="section-h-ruangan">
            <?php if(mysqli_num_rows($q_ruangan) == 0) { ?>
                <div class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1"></i><p class="small">Belum ada riwayat ruangan.</p></div>
            <?php } ?>

            <?php while($d = mysqli_fetch_array($q_ruangan)){ ?>
                <div class="card history-card shadow-sm <?php echo $d['status']; ?>">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px;">ID #R-<?php echo $d['id']; ?></small>
                                <h6 class="fw-bold mb-0 text-start"><?php echo $d['nama_ruangan']; ?></h6>
                            </div>
                            <!-- PERBAIKAN WARNA BADGE RUANGAN -->
                            <span class="status-badge <?php 
                                if($d['status'] == 'pending') echo 'bg-warning text-dark';
                                elseif($d['status'] == 'disetujui') echo 'bg-success text-white';
                                elseif($d['status'] == 'ditolak') echo 'bg-danger text-white';
                                else echo 'bg-info text-white';
                            ?>">
                                <?php echo $d['status']; ?>
                            </span>
                        </div>
                        <div class="row mt-2 border-top pt-2">
                            <div class="col-6 border-end text-start">
                                <small class="text-muted d-block" style="font-size: 9px;">Waktu:</small>
                                <small class="fw-bold text-primary"><?php echo date('d M Y', strtotime($d['tgl_pinjam'])); ?></small>
                            </div>
                            <div class="col-6 ps-3 text-start">
                                <small class="text-muted d-block" style="font-size: 9px;">Institusi:</small>
                                <small class="fw-bold d-block text-truncate"><?php echo $d['institusi_peminjam']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div id="section-h-kendaraan" style="display: none;">
            <?php if(mysqli_num_rows($q_kendaraan) == 0) { ?>
                <div class="text-center py-5 text-muted"><i class="bi bi-car-front fs-1"></i><p class="small">Belum ada riwayat kendaraan.</p></div>
            <?php } ?>

            <?php while($k = mysqli_fetch_array($q_kendaraan)){ ?>
                <div class="card history-card shadow-sm <?php echo $k['status']; ?>">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-muted d-block" style="font-size: 10px;">ID #V-<?php echo $k['id']; ?></small>
                                <h6 class="fw-bold mb-0 text-start"><?php echo $k['merk'].' '.$k['model']; ?></h6>
                                <small class="badge bg-light text-dark border" style="font-size: 10px; font-family: monospace;"><?php echo $k['nomor_plat']; ?></small>
                            </div>
                            <!-- PERBAIKAN WARNA BADGE KENDARAAN -->
                            <span class="status-badge <?php 
                                if($k['status'] == 'pending') echo 'bg-warning text-dark';
                                elseif($k['status'] == 'disetujui') echo 'bg-success text-white';
                                elseif($k['status'] == 'ditolak') echo 'bg-danger text-white';
                                else echo 'bg-info text-white';
                            ?>">
                                <?php echo $k['status']; ?>
                            </span>
                        </div>
                        <div class="row mt-2 border-top pt-2">
                            <div class="col-6 border-end text-start">
                                <small class="text-muted d-block" style="font-size: 9px;">Tujuan:</small>
                                <small class="fw-bold text-primary d-block text-truncate"><?php echo $k['tujuan']; ?></small>
                            </div>
                            <div class="col-6 ps-3 text-start">
                                <small class="text-muted d-block" style="font-size: 9px;">Sopir:</small>
                                <small class="fw-bold"><?php echo ($k['pakai_sopir'] == 'ya' ? 'Sopir Yayasan' : 'Bawa Sendiri'); ?></small>
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
    <script>
        $(document).ready(function() {
            $('#btn-h-ruangan').click(function() {
                $(this).addClass('active');
                $('#btn-h-kendaraan').removeClass('active');
                $('#section-h-kendaraan').hide();
                $('#section-h-ruangan').fadeIn();
            });
            $('#btn-h-kendaraan').click(function() {
                $(this).addClass('active');
                $('#btn-h-ruangan').removeClass('active');
                $('#section-h-ruangan').hide();
                $('#section-h-kendaraan').fadeIn();
            });
        });
    </script>
</body>
</html>