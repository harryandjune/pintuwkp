<?php
session_start();
include '../config/koneksi.php';

// Proteksi User
if (!isset($_SESSION['role']) || $_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}

// Ambil daftar institusi unik untuk autocomplete
$query_institusi = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi_kendaraan WHERE institusi_peminjam IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Kendaraan - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); background: #fff; }
        .form-label { font-size: 13px; font-weight: 600; color: #495057; margin-left: 5px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
        .form-control:focus { background: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        
        #suggestion-box { display: none; position: absolute; width: 100%; background: white; z-index: 1001; border-radius: 15px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); max-height: 200px; overflow-y: auto; border: 1px solid #eee; top: 100%; margin-top: 5px; }
        .suggestion-item { cursor: pointer; padding: 12px 15px; font-size: 13px; }
        .suggestion-item:hover { background-color: #f1f5f9; color: #0d6efd; }

        /* Div Sopir dibuat selalu tampil dengan warna peringatan halus */
        #div_sopir_alt {
            display: block; /* Selalu tampil */
            background: #fff8e6;
            padding: 15px;
            border-radius: 15px;
            border: 1px dashed #f59e0b;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h4 class="fw-bold mb-0">Booking Kendaraan</h4>
                <small class="opacity-75">Yayasan Ponpes Hidayatullah</small>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="booking_kendaraan_aksi.php" method="post">

                <!-- 1. Institusi Peminjam -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Instansi atau Unit Peminjam</label>
                    <input type="text" name="institusi_peminjam" id="institusi" class="form-control" placeholder="Ketik nama instansi..." value="<?php echo $_SESSION['unit'] ?? ''; ?>" autocomplete="off" required>
                    <div id="suggestion-box">
                        <div class="list-group list-group-flush">
                            <?php while ($inst = mysqli_fetch_array($query_institusi)) {
                                echo '<div class="list-group-item suggestion-item border-0">' . $inst['institusi_peminjam'] . '</div>';
                            } ?>
                        </div>
                    </div>
                </div>

                <!-- 2. Kategori -->
                <div class="mb-3">
                    <label class="form-label">Jenis Kendaraan</label>
                    <select name="jenis_permintaan" class="form-select" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="MPV">MPV (Avanza/Innova)</option>
                        <option value="Minibus">Minibus (Hiace/Elf)</option>
                        <option value="Pikap">Pikap</option>
                        <option value="Motor">Motor</option>
                    </select>
                </div>

                <!-- 3. Waktu -->
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Waktu Mulai</label>
                        <input type="datetime-local" name="tgl_mulai" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tujuan Perjalanan</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Ke mana tujuannya?" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="2" placeholder="Alasan peminjaman..." required></textarea>
                </div>

                <!-- 5. Opsi Sopir & Sopir Alternatif (Wajib Isi) -->
                <div class="mb-4">
                    <label class="form-label">Gunakan Sopir Yayasan?</label>
                    <select name="pakai_sopir" id="sopir_select" class="form-select">
                        <option value="ya">Ya, Butuh Sopir</option>
                        <option value="tidak">Tidak, Bawa Sopir Sendiri</option>
                    </select>

                    <div id="div_sopir_alt">
                        <label class="form-label text-warning"><i class="bi bi-person-badge"></i> Nama Sopir Alternatif</label>
                        <input type="text" name="nama_sopir_alt" id="nama_sopir_alt" class="form-control" placeholder="Nama sopir yang akan membawa" required>
                        <small class="text-muted d-block mt-1" style="font-size: 10px;">*Wajib diisi</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 15px;">
                    Kirim Permintaan <i class="bi bi-send ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Logika Autocomplete Institusi
            var input = $("#institusi");
            var box = $("#suggestion-box");
            var items = $(".suggestion-item");

            input.on("keyup focus", function() {
                var val = $(this).val().toLowerCase();
                if (val.length === 0) { box.fadeOut(100); return; }
                var matchCount = 0;
                items.each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(val) > -1) { $(this).show(); matchCount++; } else { $(this).hide(); }
                });
                if (matchCount > 0) box.fadeIn(100);
                else box.fadeOut(100);
            });

            $(document).on("click", ".suggestion-item", function() {
                input.val($(this).text());
                box.fadeOut(100);
            });

            $(document).on("click", function(e) {
                if (!$(e.target).closest(".position-relative").length) {
                    box.fadeOut(100);
                }
            });
        });
    </script>

    <?php include 'navbar.php'; ?>
</body>
</html>