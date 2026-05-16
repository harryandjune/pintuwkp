<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}

$user_id = $_SESSION['id_user'];
date_default_timezone_set('Asia/Makassar'); // Sesuaikan zona waktu Anda
$now = date('Y-m-d H:i:s');

// 1. Ambil Bokingan Gedung (Pending ATAU Disetujui yang belum lewat jamnya)
$q_gedung = mysqli_query($koneksi, "SELECT r.*, rm.nama_ruangan 
    FROM reservasi r 
    LEFT JOIN ruangan rm ON r.ruangan_id = rm.id 
    WHERE r.user_id = '$user_id' 
    AND (r.status = 'pending' OR (r.status = 'disetujui' AND CONCAT(r.tgl_selesai, ' ', r.jam_selesai) > '$now'))
    ORDER BY r.id DESC");

// 2. Ambil Bokingan Kendaraan (Pending ATAU Disetujui yang belum lewat jamnya)
$q_mobil = mysqli_query($koneksi, "SELECT r.*, k.merk, k.model 
    FROM reservasi_kendaraan r 
    LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
    WHERE r.user_id = '$user_id' 
    AND (r.status = 'pending' OR (r.status = 'disetujui' AND r.tgl_selesai > '$now'))
    ORDER BY r.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boking Aktif - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: 10px; }
        .active-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 6px solid #0d6efd; }
        .status-badge { font-size: 9px; padding: 4px 10px; border-radius: 8px; font-weight: 700; text-transform: uppercase; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center text-start">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Boking Aktif</h4>
        </div>
    </div>

    <div class="container mt-4">
        <p class="small text-muted px-2 text-start">Daftar pengajuan yang sedang diproses atau akan datang.</p>

        <!-- LOOP GEDUNG -->
        <?php while($g = mysqli_fetch_array($q_gedung)) { ?>
            <div class="card active-card shadow-sm">
                <div class="card-body p-3 text-start">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 9px;">GEDUNG / GH</span>
                            <h6 class="fw-bold mb-0"><?php echo ($g['ruangan_id'] ? $g['nama_ruangan'] : "Minta: ".strtoupper($g['tipe_permintaan'])); ?></h6>
                        </div>
                        <span class="status-badge <?php echo ($g['status'] == 'pending' ? 'bg-warning text-dark' : 'bg-success text-white'); ?>">
                            <?php echo $g['status']; ?>
                        </span>
                    </div>
                    <div class="row border-top pt-2">
                        <div class="col-8">
                            <small class="text-muted d-block" style="font-size: 9px;">Waktu:</small>
                            <small class="fw-bold"><?php echo date('d M Y', strtotime($g['tgl_pinjam'])); ?></small>
                        </div>
                        <div class="col-4 text-end">
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelGedung<?php echo $g['id']; ?>">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Cancel Gedung -->
            <div class="modal fade" id="cancelGedung<?php echo $g['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="cancel_aksi.php" method="POST" class="modal-content" style="border-radius:25px;">
                        <div class="modal-body p-4 text-start">
                            <h6 class="fw-bold">Batalkan Pesanan?</h6>
                            <p class="small text-muted">Berikan alasan singkat pembatalan:</p>
                            <input type="hidden" name="id" value="<?php echo $g['id']; ?>">
                            <input type="hidden" name="type" value="gedung">
                            <textarea name="alasan" class="form-control mb-3" rows="3" placeholder="Alasan..." required style="border-radius:12px;"></textarea>
                            <button type="submit" class="btn btn-danger w-100 fw-bold py-2" style="border-radius:12px;">Konfirmasi Pembatalan</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <!-- LOOP KENDARAAN -->
        <?php while($k = mysqli_fetch_array($q_mobil)) { ?>
            <div class="card active-card shadow-sm" style="border-left-color: #f59e0b;">
                <div class="card-body p-3 text-start">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-warning-subtle text-warning mb-1" style="font-size: 9px;">KENDARAAN</span>
                            <h6 class="fw-bold mb-0"><?php echo ($k['kendaraan_id'] ? $k['merk'].' '.$k['model'] : "Minta: ".$k['jenis_permintaan']); ?></h6>
                        </div>
                        <span class="status-badge <?php echo ($k['status'] == 'pending' ? 'bg-warning text-dark' : 'bg-success text-white'); ?>">
                            <?php echo $k['status']; ?>
                        </span>
                    </div>
                    <div class="row border-top pt-2">
                        <div class="col-8">
                            <small class="text-muted d-block" style="font-size: 9px;">Tujuan:</small>
                            <small class="fw-bold text-truncate d-block"><?php echo $k['tujuan']; ?></small>
                        </div>
                        <div class="col-4 text-end">
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cancelMobil<?php echo $k['id']; ?>">Batal</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Cancel Mobil -->
            <div class="modal fade" id="cancelMobil<?php echo $k['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="cancel_aksi.php" method="POST" class="modal-content" style="border-radius:25px;">
                        <div class="modal-body p-4 text-start">
                            <h6 class="fw-bold">Batalkan Peminjaman Mobil?</h6>
                            <p class="small text-muted">Berikan alasan singkat pembatalan:</p>
                            <input type="hidden" name="id" value="<?php echo $k['id']; ?>">
                            <input type="hidden" name="type" value="mobil">
                            <textarea name="alasan" class="form-control mb-3" rows="3" placeholder="Alasan..." required style="border-radius:12px;"></textarea>
                            <button type="submit" class="btn btn-danger w-100 fw-bold py-2" style="border-radius:12px;">Konfirmasi Pembatalan</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <?php if(mysqli_num_rows($q_gedung) == 0 && mysqli_num_rows($q_mobil) == 0) { ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-check2-circle fs-1"></i>
                <p class="small mt-2">Tidak ada bokingan aktif.</p>
            </div>
        <?php } ?>
    </div>

    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>