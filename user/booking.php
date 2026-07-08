<?php
session_start();
include '../config/koneksi.php';

// Proteksi User
if (!isset($_SESSION['role']) || $_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}

// 1. Ambil Kategori dari URL (Menggunakan 'type', bukan 'id')
$type = isset($_GET['type']) ? mysqli_real_escape_string($koneksi, $_GET['type']) : '';

// Jika type kosong, kembalikan ke dashboard
if (empty($type)) {
    header("location:index.php");
    exit();
}

// Tentukan Judul Tampilan
$display_title = ($type == 'guest_house') ? "Guest House" : "Meeting Room";

// 2. Ambil daftar institusi unik untuk autocomplete
$query_institusi = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi WHERE institusi_peminjam IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking <?php echo $display_title; ?> - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; }
        .form-label { font-size: 13px; font-weight: 600; color: #495057; margin-left: 5px; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
        .form-control:focus { background: #fff; border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
        
        /* Suggestion Box Autocomplete */
        #suggestion-box { display: none; position: absolute; width: 100%; background: white; z-index: 1001; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-height: 200px; overflow-y: auto; border: 1px solid #eee; top: 100%; margin-top: 5px; }
        .suggestion-item { cursor: pointer; padding: 12px 15px; font-size: 13px; transition: 0.2s; }
        .suggestion-item:hover { background-color: #f1f5f9; color: #0d6efd; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex align-items-center text-start">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h4 class="fw-bold mb-0">Booking <?php echo $display_title; ?></h4>
                <small class="opacity-75">Admin akan menentukan unit terbaik untuk Anda</small>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="booking_aksi.php" method="post">
                <!-- Kirim Tipe Permintaan ke Aksi -->
                <input type="hidden" name="tipe_permintaan" value="<?php echo $type; ?>">

                <!-- Institusi Peminjam (Autocomplete) -->
                <div class="mb-3 position-relative">
                    <label class="form-label">Instansi atau Unit Peminjam</label>
                    <input type="text" name="institusi_peminjam" id="institusi" class="form-control" 
                           placeholder="Ketik nama instansi..." 
                           value="<?php echo $_SESSION['unit'] ?? ''; ?>" 
                           autocomplete="off" required>
                    <div id="suggestion-box">
                        <div class="list-group list-group-flush text-start">
                            <?php 
                            while ($inst = mysqli_fetch_array($query_institusi)) { 
                                echo '<div class="list-group-item suggestion-item border-0">'.$inst['institusi_peminjam'].'</div>'; 
                            } 
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tgl_pinjam" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <!-- Jika Meeting Room, Tampilkan Input Jam -->
                <?php if($type == 'meeting_room') { ?>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
                <?php } ?>

                <div class="mb-3">
                    <label class="form-label">Keperluan / Acara</label>
                    <textarea name="keperluan" class="form-control" rows="2" placeholder="Contoh: Rapat Koordinasi Tahunan" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Estimasi Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" class="form-control" placeholder="0" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 15px;">
                    Kirim Pengajuan <i class="bi bi-send ms-2"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Script Autocomplete -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
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
                if (matchCount > 0) box.fadeIn(100); else box.fadeOut(100);
            });

            $(document).on("click", ".suggestion-item", function() {
                input.val($(this).text()); box.fadeOut(100);
            });

            $(document).on("click", function(e) {
                if (!$(e.target).closest(".position-relative").length) { box.fadeOut(100); }
            });
        });
    </script>

    <?php include 'navbar.php'; ?>
</body>
</html>