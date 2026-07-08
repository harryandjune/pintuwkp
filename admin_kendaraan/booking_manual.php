<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "admin_kendaraan") { header("location:../login.php"); exit(); }

$query_mobil = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status_kendaraan='tersedia'");

// Ambil data unik Institusi untuk saran
$q_inst = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi_kendaraan WHERE institusi_peminjam != ''");
// Ambil data unik Sopir untuk saran
$q_sopir = mysqli_query($koneksi, "SELECT DISTINCT nama_sopir_alt FROM reservasi_kendaraan WHERE nama_sopir_alt != ''");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Manual - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: #0f172a; color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: #fff; }
        .form-label { font-size: 12px; font-weight: 600; color: #475569; margin-left: 5px; }
        .form-control, .form-select { border-radius: 12px; padding: 10px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
        
        /* Style Suggestion Box */
        .sug-box {
            display: none;
            position: absolute;
            width: 100%;
            background: white;
            z-index: 1001;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-height: 180px;
            overflow-y: auto;
            border: 1px solid #eee;
            top: 100%;
            margin-top: 5px;
        }
        .sug-item { cursor: pointer; padding: 10px 15px; font-size: 13px; }
        .sug-item:hover { background-color: #f1f5f9; color: #0d6efd; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container">
            <a href="index.php" class="text-white me-3 fs-4 text-decoration-none"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 d-inline text-white">Input Reservasi Internal</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="booking_manual_aksi.php" method="post">
                
                <div class="mb-3">
                    <label class="form-label">Pilih Armada</label>
                    <select name="kendaraan_id" class="form-select" required>
                        <option value="">-- Pilih Mobil --</option>
                        <?php while($m = mysqli_fetch_array($query_mobil)) { ?>
                            <option value="<?php echo $m['id_kendaraan']; ?>"><?php echo $m['merk'].' '.$m['model'].' ('.$m['nomor_plat'].')'; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <!-- AUTOCOMPLETE INSTANSI -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="institusi_peminjam" id="input_instansi" class="form-control" placeholder="Ketik nama..." autocomplete="off" required>
                    <div id="box_instansi" class="sug-box">
                        <div class="list-group list-group-flush">
                            <?php while($i = mysqli_fetch_array($q_inst)) { 
                                echo '<div class="list-group-item sug-item border-0">'.$i['institusi_peminjam'].'</div>'; 
                            } ?>
                        </div>
                    </div>
                </div>

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
                    <label class="form-label">Tujuan</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Dinas Luar Kota" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="2" placeholder="Alasan peminjaman..." required></textarea>
                </div>

                <!-- AUTOCOMPLETE SOPIR -->
                <div class="mb-4 position-relative">
                    <label class="form-label">Sopir</label>
                    <input type="text" name="sopir" id="input_sopir" class="form-control" placeholder="Nama sopir..." autocomplete="off" required>
                    <div id="box_sopir" class="sug-box">
                        <div class="list-group list-group-flush">
                            <?php while($s = mysqli_fetch_array($q_sopir)) { 
                                echo '<div class="list-group-item sug-item border-0">'.$s['nama_sopir_alt'].'</div>'; 
                            } ?>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-dark shadow-sm" style="border-radius: 15px;">SIMPAN & JADWALKAN</button>
            </form>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Fungsi Universal untuk Autocomplete
            function setupAutocomplete(inputId, boxId) {
                var input = $(inputId);
                var box = $(boxId);
                
                input.on("keyup focus", function() {
                    var val = $(this).val().toLowerCase();
                    var items = box.find(".sug-item");
                    var matchCount = 0;

                    if (val.length === 0) {
                        box.fadeOut(100);
                        return;
                    }

                    items.each(function() {
                        var text = $(this).text().toLowerCase();
                        if (text.indexOf(val) > -1) {
                            $(this).show();
                            matchCount++;
                        } else {
                            $(this).hide();
                        }
                    });

                    if (matchCount > 0) box.fadeIn(100); else box.fadeOut(100);
                });

                $(document).on("click", boxId + " .sug-item", function() {
                    input.val($(this).text());
                    box.fadeOut(100);
                });
            }

            // Jalankan untuk kedua kolom
            setupAutocomplete("#input_instansi", "#box_instansi");
            setupAutocomplete("#input_sopir", "#box_sopir");

            // Tutup box jika klik di luar
            $(document).on("click", function(e) {
                if (!$(e.target).closest(".position-relative").length) {
                    $(".sug-box").fadeOut(100);
                }
            });
        });
    </script>
</body>
</html>