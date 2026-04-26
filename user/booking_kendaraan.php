<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "user") { header("location:../login.php"); exit(); }

$id_v = $_GET['id'];
$mobil = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id_kendaraan='$id_v'");
$m = mysqli_fetch_array($mobil);

// Autocomplete Institusi
$query_institusi = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM reservasi_kendaraan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Mobil - <?php echo $m['model']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #0d6efd, #0049b8); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header-section">
        <div class="container d-flex align-items-center">
            <a href="kendaraan.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h4 class="fw-bold mb-0">Form Booking Mobil</h4>
                <small class="opacity-75"><?php echo $m['merk'] . " " . $m['model'] . " (" . $m['nomor_plat'] . ")"; ?></small>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="booking_kendaraan_aksi.php" method="post">
                <input type="hidden" name="kendaraan_id" value="<?php echo $m['id_kendaraan']; ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold">Institusi Peminjam</label>
                    <input type="text" name="institusi_peminjam" class="form-control" value="<?php echo $_SESSION['unit'] ?? ''; ?>" list="list_inst" required>
                    <datalist id="list_inst">
                        <?php while($inst = mysqli_fetch_array($query_institusi)) { echo "<option value='".$inst['institusi_peminjam']."'>"; } ?>
                    </datalist>
                </div>

                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Waktu Mulai</label>
                        <input type="datetime-local" name="tgl_mulai" class="form-control" required>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="form-label small fw-bold">Waktu Selesai</label>
                        <input type="datetime-local" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Tujuan Perjalanan</label>
                    <input type="text" name="tujuan" class="form-control" placeholder="Contoh: Bandara Soetta / Kantor Pusat" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Keperluan</label>
                    <textarea name="keperluan" class="form-control" rows="2" placeholder="Alasan peminjaman..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Gunakan Sopir?</label>
                    <select name="pakai_sopir" class="form-select">
                        <option value="ya">Ya, Butuh Sopir Yayasan</option>
                        <option value="tidak">Tidak, Bawa Sopir Sendiri</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 15px;">Kirim Pengajuan</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <?php include 'navbar.php'; ?>
</body>
</html>