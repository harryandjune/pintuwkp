<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "user") { header("location:../login.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard User - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">PINTU WKP</a>
            <div class="navbar-nav">
                <a class="nav-link active" href="index.php">Daftar Ruangan</a>
                <a class="nav-link" href="riwayat.php">Riwayat Booking</a>
                <a class="nav-link text-danger" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h4>Selamat Datang, <?php echo $_SESSION['nama']; ?></h4>
        <p>Silakan pilih ruangan atau kamar di bawah ini untuk memulai reservasi.</p>
        <hr>

        <div class="row">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM ruangan");
            while($d = mysqli_fetch_array($data)){
            ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge <?php echo ($d['tipe'] == 'guest_house') ? 'bg-info' : 'bg-warning text-dark'; ?> mb-2">
                            <?php echo ($d['tipe'] == 'guest_house') ? 'Guest House' : 'Meeting Room'; ?>
                        </span>
                        <h5 class="card-title"><?php echo $d['nama_ruangan']; ?></h5>
                        <p class="card-text text-muted small"><?php echo $d['fasilitas']; ?></p>
                        <p><strong>Kapasitas:</strong> <?php echo $d['kapasitas']; ?> Orang</p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="booking.php?id=<?php echo $d['id']; ?>" class="btn btn-primary w-100">Booking Sekarang</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</body>
</html>