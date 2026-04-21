<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "user") { header("location:../login.php"); }

$id_ruangan = $_GET['id'];
$ruangan = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE id='$id_ruangan'");
$r = mysqli_fetch_array($ruangan);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Booking - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">Form Reservasi: <?php echo $r['nama_ruangan']; ?></div>
                    <div class="card-body">
                        <form action="booking_aksi.php" method="post">
                            <input type="hidden" name="ruangan_id" value="<?php echo $r['id']; ?>">
                            <input type="hidden" name="tipe" value="<?php echo $r['tipe']; ?>">

                            <div class="row mb-3">
                                <div class="col">
                                    <label>Tanggal Mulai / Check-in</label>
                                    <input type="date" name="tgl_pinjam" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label>Tanggal Selesai / Check-out</label>
                                    <input type="date" name="tgl_selesai" class="form-control" required>
                                </div>
                            </div>

                            <?php if($r['tipe'] == 'meeting_room') { ?>
                            <div class="row mb-3">
                                <div class="col">
                                    <label>Jam Mulai</label>
                                    <input type="time" name="jam_mulai" class="form-control" required>
                                </div>
                                <div class="col">
                                    <label>Jam Selesai</label>
                                    <input type="time" name="jam_selesai" class="form-control" required>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="mb-3">
                                <label>Keperluan / Nama Tamu</label>
                                <textarea name="keperluan" class="form-control" rows="3" placeholder="Contoh: Rapat Koordinasi Kurikulum atau Nama Tamu Guest House" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label>Jumlah Orang</label>
                                <input type="number" name="jumlah_orang" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Kirim Pengajuan Booking</button>
                            <a href="index.php" class="btn btn-light w-100 mt-2">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>