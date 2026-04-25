<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// Ambil jumlah pending untuk lencana menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Ruangan - <?php echo $sett['nama_sistem']; ?></title>
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
            margin-bottom: -30px;
        }

        /* Form Card Styling */
        .setup-card {
            border: none;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-left: 5px;
        }

        .form-control,
        .form-select {
            border-radius: 15px;
            padding: 12px 18px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #fff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .btn-save {
            border-radius: 15px;
            padding: 15px;
            font-weight: 600;
            font-size: 16px;
            background: #38bdf8;
            border: none;
            transition: all 0.3s;
        }

        .btn-save:hover {
            background: #0ea5e9;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="ruangan.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Tambah Ruangan</h4>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center px-2">
            <div class="col-12 col-md-8 col-lg-6">

                <!-- Card Form -->
                <div class="card setup-card">
                    <div class="card-body p-4">
                        <form action="ruangan_aksi.php?aksi=tambah" method="post">

                            <div class="mb-3">
                                <label class="form-label">Nama Ruangan / Kamar</label>
                                <input type="text" name="nama_ruangan" class="form-control" placeholder="Contoh: Kamar 101 atau Meeting Room A" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tipe Ruangan</label>
                                <select name="tipe" class="form-select" required>
                                    <option value="guest_house">Guest House (Per Hari)</option>
                                    <option value="meeting_room">Meeting Room (Per Jam)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kapasitas (Orang)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0" style="border-radius: 15px 0 0 15px;">
                                        <i class="bi bi-people"></i>
                                    </span>
                                    <input type="number" name="kapasitas" class="form-control" placeholder="0" required style="border-radius: 0 15px 15px 0;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Fasilitas</label>
                                <textarea name="fasilitas" class="form-control" rows="3" placeholder="Contoh: AC, TV, Proyektor, WiFi..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-save w-100 text-white shadow">
                                Simpan Ruangan Baru <i class="bi bi-check-circle ms-2"></i>
                            </button>

                            <a href="ruangan.php" class="btn btn-link w-100 mt-3 text-decoration-none text-muted small">
                                Batal & Kembali
                            </a>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-5 text-muted" style="font-size: 10px;">
                    &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
                </div>

            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>