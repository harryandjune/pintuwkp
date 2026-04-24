<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "user") { header("location:../login.php"); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PINTU WKP - Dashboard</title>
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
            padding-bottom: 100px; /* Ruang untuk menu bawah */
        }
        
        /* Header Styling */
        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        /* Card Styling */
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
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 1000;
            border: 1px solid rgba(255,255,255,0.3);
        }
        .nav-item {
            text-align: center;
            color: #adb5bd;
            text-decoration: none;
            font-size: 11px;
            transition: all 0.3s;
            flex: 1;
        }
        .nav-item i {
            font-size: 22px;
            display: block;
            margin-bottom: 2px;
        }
        .nav-item.active {
            color: #0d6efd;
        }
        .nav-item:active {
            transform: scale(0.9);
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

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3 px-1">
            <h6 class="fw-bold mb-0">Pilih Ruangan</h6>
            <span class="badge bg-light text-dark rounded-pill shadow-sm">Total: <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan")); ?></span>
        </div>

        <div class="row">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM ruangan");
            while($d = mysqli_fetch_array($data)){
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
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1"></i> <?php echo $d['fasilitas']; ?>
                        </p>
                        
                        <a href="booking.php?id=<?php echo $d['id']; ?>" class="btn btn-primary btn-booking w-100 shadow-sm">
                            Booking Sekarang <i class="bi bi-chevron-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Floating Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php" class="nav-item active">
            <i class="bi bi-grid-1x2-fill"></i>
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
        <a href="../logout.php" class="nav-item text-danger" id="logoutBtn">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>
    </div>

    <!-- JS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function(){
            // Efek klik pada logout
            $('#logoutBtn').on('click', function(e){
                if(!confirm('Apakah anda yakin ingin keluar?')){
                    e.preventDefault();
                }
            });

            // Animasi masuk kartu satu per satu
            $('.room-card').each(function(i) {
                $(this).css({'opacity': '0', 'margin-top': '20px'});
                setTimeout(() => {
                    $(this).animate({'opacity': '1', 'margin-top': '0px'}, 400);
                }, 150 * i);
            });
        });
    </script>
</body>
</html>