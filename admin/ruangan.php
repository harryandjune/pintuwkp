<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// Hitung jumlah pending untuk lencana menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ruangan - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 100px;
        }

        /* Header Styling */
        .header-section {
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: 10px;
        }

        /* Room Card Styling */
        .manage-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            transition: 0.3s;
        }

        .icon-box-room {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .btn-add {
            border-radius: 15px;
            padding: 10px 18px;
            font-weight: 600;
            background: #0d6efd;
            border: none;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .btn-action-sm {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 14px;
            padding: 0;
        }

        .room-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 2px;
            color: #1e293b;
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center text-start">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Data Ruangan</h4>
            </div>
            <a href="ruangan_tambah.php" class="btn btn-add btn-sm text-white">
                <i class="bi bi-plus-lg"></i>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="px-2 mb-4 d-flex justify-content-between align-items-center text-start">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Daftar Kamar & Ruangan</h6>
                <small class="text-muted small">Total: <?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan")); ?> Aset</small>
            </div>
        </div>

        <div class="row g-3">
            <?php
            $data = mysqli_query($koneksi, "SELECT * FROM ruangan ORDER BY tipe DESC");
            if (mysqli_num_rows($data) == 0) {
                echo '<div class="text-center py-5 text-muted"><i class="bi bi-inbox fs-1 d-block mb-2"></i>Belum ada ruangan.</div>';
            }
            while ($d = mysqli_fetch_array($data)) {
            ?>

                <div class="col-12 col-md-6">
                    <div class="card manage-card">
                        <div class="card-body p-3">
                            <!-- Bagian Atas Card -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center text-start">
                                    <!-- Icon -->
                                    <div class="icon-box-room me-3 <?php echo ($d['tipe'] == 'guest_house' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning'); ?>">
                                        <i class="bi <?php echo ($d['tipe'] == 'guest_house' ? 'bi-houses-fill' : 'bi-person-video3'); ?>"></i>
                                    </div>

                                    <!-- Info Nama & Tipe -->
                                    <div>
                                        <h6 class="room-title mb-0"><?php echo $d['nama_ruangan']; ?></h6>
                                        <span class="badge bg-light text-dark fw-normal" style="font-size: 9px; letter-spacing: 0.5px;">
                                            <?php echo ($d['tipe'] == 'guest_house' ? 'GUEST HOUSE' : 'MEETING ROOM'); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Grup Tombol Aksi -->
                                <div class="d-flex gap-2">
                                    <a href="ruangan_edit.php?id=<?php echo $d['id']; ?>" class="btn btn-outline-primary btn-action-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="ruangan_aksi.php?id=<?php echo $d['id']; ?>&aksi=hapus" class="btn btn-outline-danger btn-action-sm" onclick="return confirm('Hapus ruangan ini?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <hr class="my-2 opacity-25">

                            <!-- Bagian Bawah Card (Fasilitas & Kapasitas) -->
                            <div class="row text-start g-0">
                                <div class="col-4 border-end pe-2">
                                    <small class="text-muted d-block" style="font-size: 9px; font-weight: 600;">KAPASITAS</small>
                                    <small class="fw-bold text-dark" style="font-size: 12px;"><?php echo $d['kapasitas']; ?> Orang</small>
                                </div>
                                <div class="col-8 ps-3">
                                    <small class="text-muted d-block" style="font-size: 9px; font-weight: 600;">FASILITAS</small>
                                    <small class="text-dark d-block text-truncate" style="font-size: 11px;"><?php echo $d['fasilitas']; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>
        </div>

        <div class="text-center mt-5 text-muted" style="font-size: 10px;">
            &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>