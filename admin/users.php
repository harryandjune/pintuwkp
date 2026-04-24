<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
}

// Hitung statistik user
$count_user = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users WHERE role='user'"));
// Hitung jumlah pending untuk lencana menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - PINTU WKP</title>
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

        /* User Card Styling */
        .user-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .avatar-box {
            width: 45px;
            height: 45px;
            background: #e2e8f0;
            color: #475569;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
        }

        .unit-label {
            font-size: 10px;
            background: #dcfce7;
            color: #166534;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Manajemen Users</h4>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="px-2 mb-4">
            <h6 class="fw-bold mb-0 text-dark">Daftar Akun Unit</h6>
            <small class="text-muted small">Total: <?php echo $count_user; ?> Akun Terdaftar</small>
        </div>

        <div class="row g-2">
            <?php 
            $data = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, unit ASC");
            while($u = mysqli_fetch_array($data)){
                // Inisial untuk avatar
                $inisial = strtoupper(substr($u['nama_lengkap'], 0, 1));
            ?>
            
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <!-- Avatar -->
                            <div class="avatar-box me-3">
                                <?php echo $inisial; ?>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center">
                                    <h6 class="fw-bold mb-0 me-2"><?php echo $u['nama_lengkap']; ?></h6>
                                    <?php if($u['role'] == 'admin') { ?>
                                        <span class="badge bg-primary text-white" style="font-size: 8px;">ADMIN</span>
                                    <?php } else { ?>
                                        <span class="unit-label"><?php echo $u['unit']; ?></span>
                                    <?php } ?>
                                </div>
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-person me-1"></i> @<?php echo $u['username']; ?>
                                </small>
                            </div>

                            <!-- Aksi Hapus (Jangan hapus admin sendiri) -->
                            <?php if($u['role'] != 'admin') { ?>
                            <div class="text-end">
                                <a href="user_aksi.php?hapus=<?php echo $u['id']; ?>" class="btn btn-outline-danger border-0 btn-sm" onclick="return confirm('Hapus akun unit ini?')">
                                    <i class="bi bi-trash fs-5"></i>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php } ?>
        </div>

        <!-- Info tambahan -->
        <div class="alert alert-info mx-2 mt-4 border-0 shadow-sm" style="border-radius: 15px; font-size: 11px;">
            <i class="bi bi-info-circle-fill me-2"></i> Akun baru ditambahkan melalui halaman registrasi publik atau oleh sistem. Admin tidak dapat menghapus akun Admin lainnya melalui sini.
        </div>

        <div class="text-center mt-4">
            <p class="text-muted" style="font-size: 10px;">&copy; 2026 YPPH - Kantor WKP Management</p>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>