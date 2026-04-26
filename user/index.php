<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTU WKP - Dashboard</title>
    <!-- Favicon Dinamis -->
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        .room-card {
            border: none;
            border-radius: 20px;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .room-card:hover {
            transform: translateY(-5px);
        }

        .badge-tipe {
            border-radius: 10px;
            font-size: 11px;
            padding: 5px 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .btn-booking {
            border-radius: 12px;
            font-weight: 600;
            padding: 10px;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 opacity-75">Selamat Datang,</h6>
                    <h4 class="fw-bold"><?php echo $_SESSION['nama']; ?> 👋</h4>
                </div>
                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 45px; height: 45px;">
                    <i class="bi bi-person-fill fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row g-3">
            <div class="col-6">
                <a href="index.php" class="card p-3 text-center text-decoration-none shadow-sm rounded-4 border-0 bg-primary text-white">
                    <i class="bi bi-building fs-3 mb-2"></i>
                    <small class="fw-bold">Pinjam Ruangan</small>
                </a>
            </div>
            <div class="col-6">
                <a href="kendaraan.php" class="card p-3 text-center text-decoration-none shadow-sm rounded-4 border-0 bg-white text-dark">
                    <i class="bi bi-car-front-fill fs-3 mb-2 text-warning"></i>
                    <small class="fw-bold">Pinjam Mobil</small>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <h6 class="fw-bold mb-0">Pilih Ruangan</h6>
            <span class="badge bg-light text-dark rounded-pill shadow-sm">Total: <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan")); ?></span>
        </div>

        <div class="row">
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM ruangan");
            while ($d = mysqli_fetch_array($data)) {
            ?>
                <div class="col-12 col-md-6 mb-4">
                    <div class="card room-card shadow-sm h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge badge-tipe <?php echo ($d['tipe'] == 'guest_house') ? 'bg-info text-white' : 'bg-warning text-dark'; ?>">
                                    <i class="bi <?php echo ($d['tipe'] == 'guest_house') ? 'bi-houses' : 'bi-person-video3'; ?> me-1"></i>
                                    <?php echo ($d['tipe'] == 'guest_house') ? 'Guest House' : 'Meeting Room'; ?>
                                </span>
                                <div class="text-primary fw-bold">
                                    <i class="bi bi-people-fill me-1"></i> <?php echo $d['kapasitas']; ?>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo $d['nama_ruangan']; ?></h5>
                            <p class="text-muted small mb-3"><?php echo $d['fasilitas']; ?></p>
                            <a href="booking.php?id=<?php echo $d['id']; ?>" class="btn btn-primary btn-booking w-100 shadow-sm">Booking Sekarang</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- JS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PANGGIL NAVBAR DISINI -->
    <?php include 'navbar.php'; ?>

    <script>
        $(document).ready(function() {
            // Animasi masuk kartu satu per satu
            $('.room-card').each(function(i) {
                $(this).css({
                    'opacity': '0',
                    'margin-top': '20px'
                });
                setTimeout(() => {
                    $(this).animate({
                        'opacity': '1',
                        'margin-top': '0px'
                    }, 400);
                }, 150 * i);
            });
        });
    </script>
</body>

</html>