<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// Ambil jumlah pending untuk lencana di menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
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
    </style>
</head>

<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Persetujuan</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="px-1 mb-4 d-flex justify-content-between align-items-end">
            <div class="text-start">
                <h6 class="fw-bold mb-0 text-dark">Daftar Pengajuan</h6>
                <small class="text-muted small">Tentukan ruangan untuk unit</small>
            </div>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3"><?php echo $count_pending; ?> Pending</span>
        </div>

        <?php
        // QUERY: Menggunakan LEFT JOIN agar data dengan ruangan_id NULL tetap muncul
        $query = "SELECT r.*, u.nama_lengkap, u.unit, u.no_wa, rm.nama_ruangan 
                  FROM reservasi r 
                  JOIN users u ON r.user_id = u.id 
                  LEFT JOIN ruangan rm ON r.ruangan_id = rm.id 
                  ORDER BY (status = 'pending') DESC, r.id DESC";

        $data = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($data) == 0) {
            echo '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada data.</div>';
        }

        while ($d = mysqli_fetch_array($data)) {
            // Normalisasi WA
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (!empty($phone)) {
                if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                elseif (substr($phone, 0, 1) === '8') { $phone = '62' . $phone; }
            }

            $message = urlencode("Assalamualaikum, Akh " . $d['nama_lengkap'] . ", saya Admin " . $sett['nama_sistem'] . " ingin konfirmasi terkait pengajuan " . $d['tipe_permintaan']);
        ?>

            <div class="card approval-card shadow-sm text-start">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="unit-badge mb-1 d-inline-block text-primary">
                                <i class="bi bi-briefcase me-1"></i> <?php echo $d['institusi_peminjam']; ?>
                            </span>
                            <!-- Menampilkan Nama Ruangan jika sudah ada, atau Jenis yang diminta jika masih NULL -->
                            <h6 class="fw-bold mb-0">
                                <?php echo ($d['ruangan_id'] ? $d['nama_ruangan'] : "Minta: ".str_replace('_',' ', strtoupper($d['tipe_permintaan']))); ?>
                            </h6>
                            <small class="text-muted" style="font-size: 11px;">
                                PIC: <?php echo $d['nama_lengkap']; ?>
                                <?php if ($phone) { ?>
                                    <a href="https://wa.me/<?php echo $phone; ?>?text=<?php echo $message; ?>" target="_blank" class="btn-wa ms-1">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                <?php } ?>
                            </small>
                        </div>
                        <?php
                        if ($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                        elseif ($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                        else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                        ?>
                    </div>

                    <div class="bg-light p-2 rounded-3 mb-3" style="font-size: 12px;">
                        <div class="d-flex mb-1">
                            <i class="bi bi-calendar3 me-2 text-primary"></i>
                            <span>
                                <?php
                                echo date('d M Y', strtotime($d['tgl_pinjam']));
                                if ($d['tipe_permintaan'] == 'meeting_room') {
                                    echo " (" . substr($d['jam_mulai'], 0, 5) . "-" . substr($d['jam_selesai'], 0, 5) . ")";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="d-flex">
                            <i class="bi bi-chat-left-text me-2 text-primary"></i>
                            <span class="text-truncate"><?php echo $d['keperluan']; ?></span>
                        </div>
                    </div>

                    <?php if ($d['status'] == 'pending') { ?>
                        <div class="row g-2">
                            <div class="col-12">
                                <!-- TRIGGER MODAL -->
                                <button class="btn btn-success btn-action w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetujui<?php echo $d['id']; ?>">
                                    <i class="bi bi-check-circle me-1"></i> Pilih Ruang & Setujui
                                </button>
                            </div>
                            <div class="col-12">
                                <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-outline-danger btn-action w-100 border-0" onclick="return confirm('Tolak?')">Tolak</a>
                            </div>
                        </div>

                        <!-- MODAL PILIH RUANGAN -->
                        <div class="modal fade" id="modalSetujui<?php echo $d['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="persetujuan_aksi.php" method="GET" class="modal-content" style="border-radius: 25px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="fw-bold mb-0">Alokasikan Unit Ruangan</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-3">
                                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                        <input type="hidden" name="status" value="disetujui">
                                        
                                        <p class="small text-muted mb-3">User memerlukan: <b class="text-primary"><?php echo str_replace('_', ' ', strtoupper($d['tipe_permintaan'])); ?></b></p>
                                        
                                        <label class="small fw-bold mb-1">Daftar Ruangan Tersedia:</label>
                                        <select name="ruangan_id" class="form-select mb-3" style="border-radius: 12px;" required>
                                            <option value="">-- Pilih Ruangan --</option>
                                            <?php 
                                            $tipe_req = $d['tipe_permintaan'];
                                            $q_room = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE tipe='$tipe_req' ORDER BY nama_ruangan ASC");
                                            while($rm = mysqli_fetch_array($q_room)){
                                                echo "<option value='".$rm['id']."'>".$rm['nama_ruangan']." (Kapasitas: ".$rm['kapasitas'].")</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 12px;">Konfirmasi & Setujui</button>
                                    </div>
                                </form>
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