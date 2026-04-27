<?php
session_start();
include '../config/koneksi.php';
if ($_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}

$query_ruangan = mysqli_query($koneksi, "SELECT * FROM ruangan ORDER BY tipe DESC");
$query_kendaraan = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE status_kendaraan='tersedia' ORDER BY merk ASC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan - <?php echo $sett['nama_sistem']; ?></title>
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
            margin-bottom: 10px;
        }

        /* Tab Navigation Styling */
        .tab-wrapper {
            background: #fff;
            border-radius: 20px;
            padding: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .tab-btn {
            border: none;
            border-radius: 15px;
            padding: 12px 5px;
            transition: all 0.3s ease;
            background: transparent;
            color: #6c757d;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .tab-btn i {
            font-size: 24px;
            margin-bottom: 4px;
            color: #0d6efd;
            transition: 0.3s;
        }

        .tab-btn span {
            font-size: 12px;
            font-weight: 600;
        }

        /* State Active */
        .tab-btn.active {
            background-color: #0d6efd;
            color: #fff !important;
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
        }

        .tab-btn.active i {
            color: #fff !important;
        }

        /* Card Content Style */
        .item-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
            margin-bottom: 15px;
        }

        .plate-badge {
            font-family: monospace;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #e2e8f0;
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

    <!-- Tab Navigation -->
    <div class="container mt-4">
        <div class="tab-wrapper">
            <div class="row g-0">
                <div class="col-6">
                    <button id="btn-ruangan" class="tab-btn active">
                        <i class="bi bi-building"></i>
                        <span>Meeting Room & GH</span>
                    </button>
                </div>
                <div class="col-6">
                    <button id="btn-kendaraan" class="tab-btn">
                        <i class="bi bi-car-front-fill"></i>
                        <span>Kendaraan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section: RUANGAN -->
    <div id="content-ruangan" class="container mt-5 animate-fade">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <h6 class="fw-bold mb-0">Daftar Ruangan</h6>
            <span class="badge bg-light text-dark rounded-pill shadow-sm">Total: <?php echo mysqli_num_rows($query_ruangan); ?></span>
        </div>

        <div class="row">
            <?php while ($d = mysqli_fetch_array($query_ruangan)) { ?>
                <div class="col-12 col-md-6">
                    <div class="card item-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <span class="badge rounded-pill <?php echo ($d['tipe'] == 'guest_house') ? 'bg-info text-white' : 'bg-warning text-dark'; ?> py-2 px-3 small">
                                    <?php echo str_replace('_', ' ', strtoupper($d['tipe'])); ?>
                                </span>
                                <div class="text-primary fw-bold">
                                    <i class="bi bi-people-fill me-1"></i> <?php echo $d['kapasitas']; ?>
                                </div>
                            </div>
                            <h5 class="fw-bold mb-2"><?php echo $d['nama_ruangan']; ?></h5>
                            <p class="text-muted small mb-3"><?php echo $d['fasilitas']; ?></p>
                            <a href="booking.php?id=<?php echo $d['id']; ?>" class="btn btn-primary w-100 py-2 fw-bold shadow-sm" style="border-radius:12px;">Booking Sekarang</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Content Section: KENDARAAN -->
    <div id="content-kendaraan" class="container mt-5 animate-fade" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <h6 class="fw-bold mb-0">Daftar Armada</h6>
            <span class="badge bg-light text-dark rounded-pill shadow-sm">Total: <?php echo mysqli_num_rows($query_kendaraan); ?></span>
        </div>

        <div class="row">
            <?php while ($k = mysqli_fetch_array($query_kendaraan)) { ?>
                <div class="col-12 col-md-6">
                    <div class="card item-card h-100" style="border-left: 5px solid #f59e0b;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="plate-badge"><?php echo $k['nomor_plat']; ?></span>
                                <span class="badge bg-success-subtle text-success small">Tersedia</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-0"><?php echo $k['merk'] . " " . $k['model']; ?></h5>
                            <small class="text-muted">Kapasitas: <?php echo $k['kapasitas']; ?> Kursi</small>
                            <hr class="my-3 opacity-25">
                            <a href="booking_kendaraan.php?id=<?php echo $k['id_kendaraan']; ?>" class="btn btn-warning w-100 py-2 fw-bold text-dark shadow-sm" style="border-radius:12px;">Booking Mobil</a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php include 'navbar.php'; ?>

    <script>
        $(document).ready(function() {
            // Fungsi Ganti Tab
            $('#btn-ruangan').click(function() {
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');

                $('#content-kendaraan').hide();
                $('#content-ruangan').fadeIn(400);
            });

            $('#btn-kendaraan').click(function() {
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');

                $('#content-ruangan').hide();
                $('#content-kendaraan').fadeIn(400);
            });
        });
    </script>
</body>

</html>