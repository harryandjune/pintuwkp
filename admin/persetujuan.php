<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// -----------------------------------------------------------------------
// 1. LOGIKA AUTO-SELESAI GEDUNG (OTOMATIS)
// -----------------------------------------------------------------------
date_default_timezone_set('Asia/Makassar'); 
$now_dt = date('Y-m-d H:i:s');
$today  = date('Y-m-d');

// Update status ke 'selesai' jika waktu penggunaan sudah lewat
$sql_auto_done = "UPDATE reservasi SET status = 'selesai' 
                  WHERE status = 'disetujui' 
                  AND (
                    (tipe_permintaan = 'meeting_room' AND CONCAT(tgl_selesai, ' ', jam_selesai) < '$now_dt') 
                    OR 
                    (tipe_permintaan = 'guest_house' AND tgl_selesai < '$today')
                  )";
mysqli_query($koneksi, $sql_auto_done);
// -----------------------------------------------------------------------

// --- 2. LOGIKA PAGINASI ---
$limit = 10; 
$page  = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

$total_res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi");
$total_data = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_data / $limit);

$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .approval-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        .unit-badge { background: #f1f5f9; color: #475569; font-size: 10px; padding: 4px 10px; border-radius: 8px; font-weight: 600; }
        .status-badge { font-size: 10px; padding: 4px 10px; border-radius: 8px; font-weight: 600; }
        .btn-action { border-radius: 12px; font-size: 13px; font-weight: 600; padding: 10px; }
        .btn-wa { color: #25D366; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .btn-wa:hover { color: #128C7E; }
        .info-box { background: #f8f9fa; border-radius: 12px; padding: 12px; font-size: 12px; }
        .pagination .page-link { border-radius: 8px; margin: 0 3px; border: none; color: #1e293b; font-size: 13px; }
        .pagination .page-item.active .page-link { background-color: #0d6efd; color: #fff; }
    </style>
</head>

<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 text-white">Persetujuan</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-1 mb-4 d-flex justify-content-between align-items-end text-start">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Daftar Pengajuan</h6>
                <small class="text-muted small">Hal <?php echo $page; ?> dari <?php echo $total_pages; ?></small>
            </div>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3"><?php echo $count_pending; ?> Pending</span>
        </div>

        <?php
        $query = "SELECT r.*, u.nama_lengkap, u.unit, u.no_wa, rm.nama_ruangan 
                  FROM reservasi r 
                  JOIN users u ON r.user_id = u.id 
                  LEFT JOIN ruangan rm ON r.ruangan_id = rm.id 
                  ORDER BY (status = 'pending') DESC, r.id DESC 
                  LIMIT $offset, $limit";

        $data = mysqli_query($koneksi, $query);

        while ($d = mysqli_fetch_array($data)) {
            // Tentukan batas waktu selesai untuk tombol cancel
            $waktu_selesai_booking = ($d['tipe_permintaan'] == 'meeting_room') 
                                     ? $d['tgl_selesai'] . ' ' . $d['jam_selesai'] 
                                     : $d['tgl_selesai'] . ' 23:59:59';

            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
            elseif (substr($phone, 0, 1) === '8') $phone = '62' . $phone;
            
            $message = urlencode("Assalamualaikum, Akh " . $d['nama_lengkap'] . ", saya Admin " . $sett['nama_sistem'] . " ingin konfirmasi terkait pengajuan " . $d['tipe_permintaan']);
        ?>

            <div class="card approval-card shadow-sm text-start">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="unit-badge mb-1 d-inline-block text-primary">
                                <i class="bi bi-briefcase me-1"></i> <?php echo htmlspecialchars($d['institusi_peminjam']); ?>
                            </span>
                            <h6 class="fw-bold mb-0">
                                <?php echo ($d['ruangan_id'] ? htmlspecialchars($d['nama_ruangan']) : "Pinjam: ".str_replace('_',' ', strtoupper($d['tipe_permintaan']))); ?>
                            </h6>
                            <small class="text-muted">PIC: <?php echo htmlspecialchars($d['nama_lengkap']); ?>
                                <?php if ($phone) { ?>
                                    <a href="https://wa.me/<?php echo $phone; ?>?text=<?php echo $message; ?>" target="_blank" class="btn-wa ms-1"><i class="bi bi-whatsapp"></i></a>
                                <?php } ?>
                            </small>
                        </div>
                        <?php
                        if ($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                        elseif ($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                        elseif ($d['status'] == 'selesai') echo '<span class="status-badge bg-primary text-white">Selesai</span>';
                        elseif ($d['status'] == 'dibatalkan') echo '<span class="status-badge bg-dark text-white">Batal</span>';
                        else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                        ?>
                    </div>

                    <div class="info-box mb-3">
                        <div class="row mb-2">
                            <div class="col-7 border-end">
                                <small class="text-muted d-block">Waktu Penggunaan:</small>
                                <small class="fw-bold">
                                    <?php echo date('d M Y', strtotime($d['tgl_pinjam'])); ?>
                                    <?php if ($d['tipe_permintaan'] == 'meeting_room') echo "<br>(" . substr($d['jam_mulai'], 0, 5) . " - " . substr($d['jam_selesai'], 0, 5) . ")"; ?>
                                </small>
                            </div>
                            <div class="col-5 ps-3">
                                <small class="text-muted d-block">Kapasitas Diminta:</small>
                                <small class="fw-bold text-primary"><i class="bi bi-people-fill me-1"></i> <?php echo $d['jumlah_orang']; ?> Org</small>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 8px;">Keperluan:</small>
                            <span class="text-dark small"><?php echo htmlspecialchars($d['keperluan']); ?></span>
                        </div>
                    </div>

                    <?php if ($d['status'] == 'pending') { ?>
                        <div class="row g-2">
                            <div class="col-12">
                                <button class="btn btn-success btn-action w-100 shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalSetujui<?php echo $d['id']; ?>">
                                    <i class="bi bi-check-circle me-1"></i> Pilih Ruang & Setujui
                                </button>
                            </div>
                            <div class="col-12 text-center mt-2">
                                <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-link text-danger text-decoration-none small" onclick="return confirm('Tolak pengajuan ini?')">Tolak Pengajuan</a>
                            </div>
                        </div>

                        <!-- MODAL SETUJUI -->
                        <div class="modal fade" id="modalSetujui<?php echo $d['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="persetujuan_aksi.php" method="GET" class="modal-content" style="border-radius: 25px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="fw-bold mb-0">Alokasikan Unit Ruangan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3 text-start">
                                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                        <input type="hidden" name="status" value="disetujui">
                                        <label class="small fw-bold mb-1">Daftar Ruangan Tersedia:</label>
                                        <select name="ruangan_id" class="form-select mb-3" style="border-radius: 12px;" required>
                                            <option value="">-- Pilih Ruangan --</option>
                                            <?php 
                                            $tipe_req = $d['tipe_permintaan'];
                                            $start_req = $d['tgl_pinjam'];
                                            $end_req   = $d['tgl_selesai'];
                                            $q_busy = mysqli_query($koneksi, "SELECT ruangan_id FROM reservasi WHERE status = 'disetujui' AND ruangan_id IS NOT NULL AND (tgl_pinjam <= '$end_req' AND tgl_selesai >= '$start_req')");
                                            $busy_ids = [];
                                            while($busy = mysqli_fetch_assoc($q_busy)){ $busy_ids[] = $busy['ruangan_id']; }
                                            $q_room = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE tipe='$tipe_req' ORDER BY nama_ruangan ASC");
                                            while($rm = mysqli_fetch_array($q_room)){
                                                $is_busy = in_array($rm['id'], $busy_ids);
                                                $label_status = $is_busy ? " [⚠️ TERPAKAI]" : " [READY]";
                                                $color_style = $is_busy ? "style='color: red; font-weight:bold;'" : "";
                                                echo "<option value='".$rm['id']."' $color_style>".$rm['nama_ruangan']." (Kaps: ".$rm['kapasitas'].") $label_status</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-white shadow" style="border-radius: 12px;">Konfirmasi & Setujui</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php } elseif ($d['status'] == 'disetujui' && $now_dt < $waktu_selesai_booking) { ?>
                        <!-- TOMBOL CANCEL (Hanya muncul jika belum berakhir) -->
                        <div class="mt-2">
                            <button class="btn btn-outline-danger btn-action w-100" data-bs-toggle="modal" data-bs-target="#modalCancel<?php echo $d['id']; ?>">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Izin Ruangan
                            </button>
                        </div>

                        <!-- MODAL CANCEL -->
                        <div class="modal fade" id="modalCancel<?php echo $d['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="persetujuan_aksi.php" method="GET" class="modal-content" style="border-radius: 25px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="fw-bold mb-0 text-danger">Batalkan Peminjaman</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3 text-start">
                                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                        <input type="hidden" name="status" value="dibatalkan">
                                        <label class="small fw-bold mb-1">Alasan Pembatalan:</label>
                                        <textarea name="alasan" class="form-control mb-3" rows="3" placeholder="Sebutkan alasan pembatalan..." style="border-radius: 12px;" required></textarea>
                                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold text-white shadow" style="border-radius: 12px;">Konfirmasi Pembatalan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- PAGINASI -->
        <?php if ($total_pages > 1) { ?>
        <nav class="mt-4"><ul class="pagination justify-content-center">
            <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>"><a class="page-link shadow-sm" href="?p=<?php echo $page-1; ?>"><i class="bi bi-chevron-left"></i></a></li>
            <?php for($i=1; $i<=$total_pages; $i++) { ?>
                <li class="page-item <?php if($page == $i) echo 'active'; ?>"><a class="page-link shadow-sm" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php } ?>
            <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>"><a class="page-link shadow-sm" href="?p=<?php echo $page+1; ?>"><i class="bi bi-chevron-right"></i></a></li>
        </ul></nav>
        <?php } ?>
    </div>

    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>