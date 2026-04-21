<?php
session_start();
if($_SESSION['role'] != "admin") { header("location:../login.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Ruangan - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">Tambah Ruangan Baru</div>
                    <div class="card-body">
                        <form action="ruangan_aksi.php?aksi=tambah" method="post">
                            <div class="mb-3">
                                <label>Nama Ruangan / Kamar</label>
                                <input type="text" name="nama_ruangan" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Tipe Ruangan</label>
                                <select name="tipe" class="form-control" required>
                                    <option value="guest_house">Guest House</option>
                                    <option value="meeting_room">Meeting Room</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Kapasitas (Orang)</label>
                                <input type="number" name="kapasitas" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Fasilitas</label>
                                <textarea name="fasilitas" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan Ruangan</button>
                            <a href="ruangan.php" class="btn btn-light">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>