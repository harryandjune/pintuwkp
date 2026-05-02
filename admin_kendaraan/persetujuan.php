<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

// --- 1. LOGIKA PAGINASI ---
$limit = 10; // Batasi 10 data per halaman
$page  = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Hitung total data untuk menentukan jumlah halaman
$total_res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi_kendaraan");
$total_data = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil jumlah pending untuk lencana di menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Transport - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .approval-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); }
        .status-badge { font-size: 10px; padding: 4px 10px; border-radius: 8px; font-weight: 700; text-transform: uppercase; }
        .info-box { background: #f8f9fa; border-radius: 12px; padding: 12px; font-size: 12px; }
        .btn-action { border-radius: 12px; font-size: 13px; font-weight: 600; padding: 10px; }
        .btn-wa { color: #25D366; text-decoration: none; font-size: 14px; transition: 0.3s; }
        
        /* Style Paginasi */
        .pagination .page-link { border-radius: 8px; margin: 0 3px; border: none; color: #1e293b; font-size: 13px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .pagination .page-item.active .page-link { background-color: #f59e0b; color: #000; font-weight: bold; }
    </style>
</head>

<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container">
            <div class="d-flex align-items-center text-start">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Persetujuan Mobil</h4>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-1 mb-4 d-flex justify-content-between align-items-end">
            <div class="text-start">
                <h6 class="fw-bold mb-0">Daftar Pengajuan</h6>
                <small class="text-muted small">Hal <?php echo $page; ?> dari <?php echo $total_pages; ?></small>
            </div>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3"><?php echo $count_pending; ?> Baru</span>
        </div>

        <?php
        // QUERY DENGAN LIMIT & OFFSET UNTUK PAGINASI
        $query = "SELECT r.*, u.nama_lengkap, u.no_wa 
                  FROM reservasi_kendaraan r 
                  JOIN users u ON r.user_id = u.id 
                  ORDER BY (status = 'pending') DESC, r.id DESC 
                  LIMIT $offset, $limit";

        $data = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($data) == 0) {
            echo '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data.</div>';
        }

        while ($d = mysqli_fetch_array($data)) {
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
            elseif (substr($phone, 0, 1) === '8') $phone = '62' . $phone;
        ?>
            <div class="card approval-card shadow-sm text-start">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 9px;"><?php echo htmlspecialchars($d['institusi_peminjam']); ?></span>
                            <h6 class="fw-bold mb-0">Minta Jenis: <?php echo $d['jenis_permintaan']; ?></h6>
                            <small class="text-muted">PIC: <?php echo htmlspecialchars($d['nama_lengkap']); ?>
                                <a href="https://wa.me/<?php echo $phone; ?>" target="_blank" class="text-success ms-1"><i class="bi bi-whatsapp"></i></a>
                            </small>
                        </div>
                        <?php
                        if ($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                        elseif ($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                        else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                        ?>
                    </div>

                    <div class="info-box mb-3">
                        <div class="row mb-2">
                            <div class="col-6 border-end">
                                <small class="text-muted d-block">Waktu:</small>
                                <small class="fw-bold"><?php echo date('d M, H:i', strtotime($d['tgl_mulai'])); ?></small>
                            </div>
                            <div class="col-6 ps-3">
                                <small class="text-muted d-block">Sopir:</small>
                                <small class="fw-bold text-uppercase"><?php echo ($d['pakai_sopir'] == 'ya' ? 'SOPIR YAYASAN' : 'ALT: ' . $d['nama_sopir_alt']); ?></small>
                            </div>
                        </div>
                        <div class="mt-1">
                            <small class="text-muted d-block">Tujuan:</small>
                            <small class="fw-bold text-primary"><?php echo htmlspecialchars($d['tujuan']); ?></small>
                        </div>
                    </div>

                    <?php if ($d['status'] == 'pending') { ?>
                        <div class="row g-2">
                            <div class="col-12">
                                <button class="btn btn-success btn-action w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetujui<?php echo $d['id']; ?>">
                                    <i class="bi bi-check-circle me-1"></i> Pilih Unit & Setujui
                                </button>
                            </div>
                            <div class="col-12 text-center">
                                <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-link text-danger text-decoration-none small" onclick="return confirm('Tolak pengajuan ini?')">Tolak Pengajuan</a>
                            </div>
                        </div>

                        <!-- MODAL PILIH MOBIL -->
                        <div class="modal fade" id="modalSetujui<?php echo $d['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="persetujuan_aksi.php" method="GET" class="modal-content" style="border-radius: 25px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="fw-bold mb-0">Alokasikan Unit Mobil</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3 text-start">
                                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                        <input type="hidden" name="status" value="disetujui">

                                        <p class="small text-muted mb-3">User meminta jenis <b><?php echo $d['jenis_permintaan']; ?></b>. Pilih mobil yang tersedia:</p>

                                        <label class="small fw-bold mb-1">Daftar Mobil Tersedia:</label>
                                        <select name="kendaraan_id" class="form-select mb-3" style="border-radius: 12px;" required>
                                            <option value="">-- Pilih Armada --</option>
                                            <?php
                                            $jenis = $d['jenis_permintaan'];
                                            $start_req = $d['tgl_mulai'];
                                            $end_req   = $d['tgl_selesai'];

                                            $q_busy = mysqli_query($koneksi, "SELECT kendaraan_id FROM reservasi_kendaraan 
                                                                              WHERE status = 'disetujui' 
                                                                              AND kendaraan_id IS NOT NULL 
                                                                              AND (tgl_mulai < '$end_req' AND tgl_selesai > '$start_req')");
                                            $busy_ids = [];
                                            while ($busy = mysqli_fetch_assoc($q_busy)) { $busy_ids[] = $busy['kendaraan_id']; }

                                            $q_mobil = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE jenis_kendaraan = '$jenis' AND status_kendaraan = 'tersedia' ORDER BY merk ASC");

                                            while ($m = mysqli_fetch_array($q_mobil)) {
                                                $is_busy = in_array($m['id_kendaraan'], $busy_ids);
                                                $label_status = $is_busy ? " [⚠️ SEDANG DIPAKAI]" : " [READY]";
                                                $color_style = $is_busy ? "style='color: red; font-weight:bold;'" : "";
                                                echo "<option value='" . $m['id_kendaraan'] . "' $color_style>" . $m['merk'] . " " . $m['model'] . " (" . $m['nomor_plat'] . ")" . $label_status . "</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold text-white shadow" style="border-radius: 12px;">Konfirmasi & Setujui</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- NAVIGASI PAGINASI -->
        <?php if ($total_pages > 1) { ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page-1; ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for($i=1; $i<=$total_pages; $i++) { ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link shadow-sm" href="?p=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page+1; ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
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