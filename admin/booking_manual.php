<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

$query_ruangan = mysqli_query($koneksi, "SELECT * FROM ruangan ORDER BY tipe DESC");
$query_inst = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Manual - <?php echo $sett['nama_sistem']; ?></title>
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
            background: #1e293b;
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        .form-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background: #fff;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }

        #sug-box {
            display: none;
            position: absolute;
            width: 100%;
            background: white;
            z-index: 1001;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #eee;
            top: 100%;
            margin-top: 5px;
        }

        .sug-item {
            cursor: pointer;
            padding: 10px 15px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container text-start">
            <a href="index.php" class="text-white me-3 fs-4 text-decoration-none"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 d-inline">Booking Langsung</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="booking_manual_aksi.php" method="post">

                <div class="mb-3">
                    <label class="small fw-bold mb-1">Pilih Ruangan/Kamar</label>
                    <select name="ruangan_id" id="ruangan_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <?php while ($r = mysqli_fetch_array($query_ruangan)) { ?>
                            <option value="<?php echo $r['id']; ?>" data-tipe="<?php echo $r['tipe']; ?>">
                                <?php echo strtoupper($r['tipe']) . " - " . $r['nama_ruangan']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3 position-relative">
                    <label class="small fw-bold mb-1">Instansi</label>
                    <input type="text" name="institusi_peminjam" id="institusi" class="form-control" placeholder="Ketik nama..." autocomplete="off" required>
                    <div id="sug-box">
                        <div class="list-group list-group-flush">
                            <?php while ($i = mysqli_fetch_array($query_inst)) {
                                echo '<div class="list-group-item sug-item">' . $i['institusi_peminjam'] . '</div>';
                            } ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small fw-bold mb-1">Tgl Mulai</label>
                        <input type="date" name="tgl_pinjam" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small fw-bold mb-1">Tgl Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <!-- Input Jam (Tampil jika Meeting Room) -->
                <div id="jam_section" style="display:none;" class="row mb-3">
                    <div class="col-6">
                        <label class="small fw-bold mb-1">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="small fw-bold mb-1">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold mb-1">Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="2" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="small fw-bold mb-1">Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" class="form-control" value="1" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius:15px;">JADWALKAN SEKARANG</button>
            </form>
        </div>
    </div>
    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Logika Jam muncul hanya jika Meeting Room
            $('#ruangan_id').change(function() {
                var tipe = $(this).find(':selected').data('tipe');
                if (tipe == 'meeting_room') $('#jam_section').slideDown();
                else $('#jam_section').slideUp();
            });

            // Autocomplete Institusi
            var input = $("#institusi");
            var box = $("#sug-box");
            input.on("keyup focus", function() {
                var val = $(this).val().toLowerCase();
                if (val.length === 0) {
                    box.hide();
                    return;
                }
                var match = 0;
                $(".sug-item").each(function() {
                    if ($(this).text().toLowerCase().indexOf(val) > -1) {
                        $(this).show();
                        match++;
                    } else {
                        $(this).hide();
                    }
                });
                if (match > 0) box.show();
                else box.hide();
            });
            $(document).on("click", ".sug-item", function() {
                input.val($(this).text());
                box.hide();
            });
        });
    </script>
</body>

</html>