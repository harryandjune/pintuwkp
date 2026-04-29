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
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        .approval-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .status-badge {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 12px;
            font-size: 12px;
        }

        .btn-action {
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            padding: 10px;
        }
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
        <div class="px-2 mb-4 d-flex justify-content-between align-items-end">
            <div class="text-start">
                <h6 class="fw-bold mb-0">Daftar Pengajuan</h6>
                <small class="text-muted">Tentukan unit armada untuk user</small>
            </div>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3"><?php echo $count_pending; ?> Baru</span>
        </div>

        <?php
        // Ambil data pengajuan (JOIN dengan users untuk nama PIC)
        $query = "SELECT r.*, u.nama_lengkap, u.no_wa 
                  FROM reservasi_kendaraan r 
                  JOIN users u ON r.user_id = u.id 
                  ORDER BY (status = 'pending') DESC, r.id DESC";

        $data = mysqli_query($koneksi, $query);

        while ($d = mysqli_fetch_array($data)) {
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
        ?>
            <div class="card approval-card shadow-sm text-start">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 9px;"><?php echo htmlspecialchars($d['institusi_peminjam']); ?></span>
                            <h6 class="fw-bold mb-0">Minta Jenis: <?php echo $d['jenis_permintaan']; ?></h6>
                            <small class="text-muted">PIC: <?php echo $d['nama_lengkap']; ?>
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
                                <!-- TOMBOL TRIGGER MODAL -->
                                <button class="btn btn-success btn-action w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSetujui<?php echo $d['id']; ?>">
                                    <i class="bi bi-check-circle me-1"></i> Pilih Unit & Setujui
                                </button>
                            </div>
                            <div class="col-12">
                                <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-outline-danger btn-action w-100 border-0" onclick="return confirm('Tolak pengajuan ini?')">Tolak</a>
                            </div>
                        </div>

                        <!-- MODAL PILIH MOBIL -->
                        <div class="modal fade" id="modalSetujui<?php echo $d['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <form action="persetujuan_aksi.php" method="GET" class="modal-content" style="border-radius: 25px;">
                                    <div class="modal-header border-0">
                                        <h6 class="fw-bold mb-0">Alokasikan Unit Mobil</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body pt-0">
                                        <input type="hidden" name="id" value="<?php echo $d['id']; ?>">
                                        <input type="hidden" name="status" value="disetujui">

                                        <p class="small text-muted mb-3">User meminta jenis <b><?php echo $d['jenis_permintaan']; ?></b>. Pilih mobil yang tersedia:</p>

                                        <label class="small fw-bold mb-1">Daftar Mobil Tersedia:</label>
                                        <select name="kendaraan_id" class="form-select mb-3" style="border-radius: 12px;" required>
                                            <option value="">-- Pilih Armada --</option>
                                            <?php
                                            $jenis = $d['jenis_permintaan']; // Diambil dari data reservasi

                                            // Ambil mobil yang JENIS-nya sesuai permintaan DAN STATUS-nya 'tersedia'
                                            $q_mobil = mysqli_query($koneksi, "SELECT * FROM kendaraan 
                                       WHERE jenis_kendaraan = '$jenis' 
                                       AND status_kendaraan = 'tersedia' 
                                       ORDER BY merk ASC");

                                            if (mysqli_num_rows($q_mobil) > 0) {
                                                while ($m = mysqli_fetch_array($q_mobil)) {
                                                    echo "<option value='" . $m['id_kendaraan'] . "'>" . $m['merk'] . " " . $m['model'] . " (" . $m['nomor_plat'] . ")</option>";
                                                }
                                            } else {
                                                // Jika jenis yang diminta habis/tidak ada, tampilkan pesan peringatan
                                                echo "<option value='' disabled>Maaf, jenis $jenis saat ini tidak tersedia</option>";
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
    </div>

    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>