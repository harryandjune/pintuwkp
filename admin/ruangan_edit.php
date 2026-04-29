<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "admin") { header("location:../login.php"); exit(); }

$id = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM ruangan WHERE id='$id'");
$d = mysqli_fetch_array($query);

if (!$d) { echo "Data tidak ditemukan"; exit(); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ruangan - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 50px; }
        .header-section { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .form-card { border: none; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; }
        .form-control, .form-select { border-radius: 12px; padding: 12px; background: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
    </style>
</head>
<body>

    <div class="header-section shadow d-flex align-items-center">
        <div class="container text-start">
            <a href="ruangan.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 d-inline">Edit Ruangan</h4>
        </div>
    </div>

    <div class="container mt-5">
        <div class="card form-card p-4">
            <form action="ruangan_aksi.php?aksi=edit" method="post">
                <input type="hidden" name="id" value="<?php echo $d['id']; ?>">

                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Ruangan / Kamar</label>
                    <input type="text" name="nama_ruangan" class="form-control" value="<?php echo $d['nama_ruangan']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Tipe Ruangan</label>
                    <select name="tipe" class="form-select" required>
                        <option value="guest_house" <?php if($d['tipe'] == 'guest_house') echo 'selected'; ?>>Guest House</option>
                        <option value="meeting_room" <?php if($d['tipe'] == 'meeting_room') echo 'selected'; ?>>Meeting Room</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold">Kapasitas (Orang)</label>
                    <input type="number" name="kapasitas" class="form-control" value="<?php echo $d['kapasitas']; ?>" required>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Fasilitas</label>
                    <textarea name="fasilitas" class="form-control" rows="3"><?php echo $d['fasilitas']; ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 15px; background: #38bdf8; border:none;">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
</body>
</html>