<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "admin") { header("location:../login.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kelola Ruangan - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <h3>Daftar Ruangan & Kamar</h3>
            <div>
                <a href="index.php" class="btn btn-secondary">Dashboard</a>
                <a href="ruangan_tambah.php" class="btn btn-primary">+ Tambah Ruangan</a>
            </div>
        </div>

        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Ruangan</th>
                    <th>Tipe</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $data = mysqli_query($koneksi, "SELECT * FROM ruangan");
                while($d = mysqli_fetch_array($data)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $d['nama_ruangan']; ?></td>
                        <td><?php echo ($d['tipe'] == 'guest_house') ? 'Guest House' : 'Meeting Room'; ?></td>
                        <td><?php echo $d['kapasitas']; ?> orang</td>
                        <td><?php echo $d['fasilitas']; ?></td>
                        <td>
                            <a href="ruangan_aksi.php?id=<?php echo $d['id']; ?>&aksi=hapus" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php 
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>