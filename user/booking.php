<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "user") {
    header("location:../login.php");
}

$id_ruangan = $_GET['id'];
$ruangan = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE id='$id_ruangan'");
$r = mysqli_fetch_array($ruangan);

// Jika ID tidak ditemukan
if (!$r) {
    echo "Ruangan tidak ditemukan";
    exit;
}
$query_institusi = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi WHERE institusi_peminjam IS NOT NULL");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - PINTU WKP</title>
    <!-- Favicon Dinamis -->
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 100px;
        }

        /* Header Styling */
        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        /* Form Card Styling */
        .booking-card {
            border: none;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-left: 5px;
        }

        .form-control {
            border-radius: 15px;
            padding: 12px 18px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .btn-confirm {
            border-radius: 15px;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        /* Floating Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            display: flex;
            justify-content: space-around;
            padding: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
        }

        .nav-item {
            text-align: center;
            color: #adb5bd;
            text-decoration: none;
            font-size: 11px;
            flex: 1;
        }

        .nav-item i {
            font-size: 22px;
            display: block;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <div>
                    <h4 class="fw-bold mb-0">Form Reservasi</h4>
                    <small class="opacity-75"><?php echo $r['nama_ruangan']; ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">

                <!-- Info Ruangan Singkat -->
                <div class="alert alert-primary border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 20px;">
                    <i class="bi bi-info-circle-fill fs-3 me-3"></i>
                    <div>
                        <small class="d-block fw-bold">Tipe: <?php echo ($r['tipe'] == 'guest_house' ? 'Guest House' : 'Meeting Room'); ?></small>
                        <small>Kapasitas Maks: <?php echo $r['kapasitas']; ?> Orang</small>
                    </div>
                </div>

                <!-- Card Form -->
                <div class="card booking-card">
                    <div class="card-body p-4">
                        <form action="booking_aksi.php" method="post">
                            <input type="hidden" name="ruangan_id" value="<?php echo $r['id']; ?>">
                            <input type="hidden" name="tipe" value="<?php echo $r['tipe']; ?>">

                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label">Tgl Mulai</label>
                                    <input type="date" name="tgl_pinjam" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label">Tgl Selesai</label>
                                    <input type="date" name="tgl_selesai" class="form-control" required>
                                </div>
                            </div>

                            <?php if ($r['tipe'] == 'meeting_room') { ?>
                                <div class="row mb-3 animated fadeIn">
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
                                <label class="form-label">Keperluan</label>
                                <textarea name="keperluan" class="form-control" rows="3" placeholder="Jelaskan tujuan peminjaman..." required></textarea>
                            </div>
                            <div class="mb-3 position-relative"> <!-- Tambahkan position-relative -->
                                <label class="form-label">Institusi / Unit Peminjam</label>
                                <input type="text" name="institusi_peminjam" id="institusi" class="form-control" placeholder="Ketik nama unit..." autocomplete="off" required>

                                <!-- Wadah Saran (Akan muncul saat mengetik) -->
                                <div id="suggestion-box" class="shadow-sm border-0 position-absolute w-100 bg-white" style="display:none; z-index:1001; border-radius:15px; max-height:200px; overflow-y:auto; top: 75px;">
                                    <ul class="list-group list-group-flush" id="suggestion-list">
                                        <?php
                                        // Ambil data institusi ke dalam array JS nanti
                                        $institusi_array = [];
                                        mysqli_data_seek($query_institusi, 0); // Reset pointer query
                                        while ($inst = mysqli_fetch_array($query_institusi)) {
                                            $institusi_array[] = $inst['institusi_peminjam'];
                                            echo '<li class="list-group-item list-group-item-action border-0 small py-3 suggestion-item" style="cursor:pointer;">' . $inst['institusi_peminjam'] . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <small class="text-muted" style="font-size: 10px;">*Pilih dari saran atau ketik unit baru</small>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Jumlah Orang</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light" style="border-radius: 15px 0 0 15px;"><i class="bi bi-people"></i></span>
                                    <input type="number" name="jumlah_orang" class="form-control" placeholder="0" required style="border-radius: 0 15px 15px 0;">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-confirm w-100 shadow">
                                Ajukan Reservasi Sekarang <i class="bi bi-send ms-2"></i>
                            </button>

                            <a href="index.php" class="btn btn-link w-100 mt-3 text-decoration-none text-muted small">Batal & Kembali</a>
                        </form>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <p class="text-muted" style="font-size: 11px;">
                        Pastikan data yang Anda masukkan sudah benar.<br>
                        Persetujuan akan diberikan oleh Admin Kantor WKP.
                    </p>
                </div>

            </div>
        </div>
    </div>

    <!-- Floating Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item">
            <i class="bi bi-grid-1x2"></i>
            <span>Beranda</span>
        </a>
        <a href="riwayat.php" class="nav-item">
            <i class="bi bi-calendar-event"></i>
            <span>Riwayat</span>
        </a>
        <a href="profil.php" class="nav-item">
            <i class="bi bi-person"></i>
            <span>Profil</span>
        </a>
        <a href="../logout.php" class="nav-item text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>
    </div>
    <!-- 1. MUAT JQUERY TERLEBIH DAHULU -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- 2. MUAT BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 3. KODE LOGIKA ANDA -->
    <script>
        $(document).ready(function() {
            var input = $("#institusi");
            var box = $("#suggestion-box");

            // Set posisi tepat di bawah input
            box.css({
                'top': '100%',
                'margin-top': '5px'
            });

            // Munculkan saran saat input di-fokus atau diketik
            input.on("keyup focus", function() {
                var val = $(this).val().toLowerCase();
                var box = $("#suggestion-box");
                var items = $(".suggestion-item");
                var matchCount = 0;

                // JIKA INPUT KOSONG, SEMBUNYIKAN BOX DAN BERHENTI
                if (val.length === 0) {
                    box.fadeOut(200);
                    return;
                }

                // JIKA ADA HURUF, FILTER DATA
                items.each(function() {
                    var text = $(this).text().toLowerCase();
                    if (text.indexOf(val) > -1) {
                        $(this).show();
                        matchCount++;
                    } else {
                        $(this).hide();
                    }
                });

                // MUNCULKAN BOX HANYA JIKA ADA DATA YANG COCOK
                if (matchCount > 0) {
                    box.fadeIn(200);
                } else {
                    box.fadeOut(200);
                }
            });
            // Saat saran diklik
            $(document).on("click", ".suggestion-item", function() {
                input.val($(this).text());
                box.fadeOut(200);
            });

            // Sembunyikan saran jika klik di luar
            $(document).on("click", function(e) {
                if (!$(e.target).closest(".position-relative").length) {
                    box.fadeOut(200);
                }
            });
        });
    </script>
</body>

</html>