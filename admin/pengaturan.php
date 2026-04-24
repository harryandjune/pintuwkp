<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location:../login.php');
    exit();
}

$success = isset($_GET['pesan']) && $_GET['pesan'] === 'sukses';
// Hitung jumlah pending untuk lencana menu bawah
$count_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
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

        /* Card Styling */
        .setup-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            margin-left: 5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 10px 15px;
            font-size: 14px;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.1);
        }

        .current-image {
            width: 50px;
            height: 50px;
            object-fit: contain;
            border-radius: 10px;
            background: #f1f5f9;
            padding: 5px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Pengaturan</h4>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        
        <?php if ($success): ?>
            <div class="alert alert-success border-0 shadow-sm rounded-4 small mb-4 text-center">
                <i class="bi bi-check-circle-fill me-2"></i> Pengaturan berhasil diperbarui!
            </div>
        <?php endif; ?>

        <form action="pengaturan_aksi.php" method="post" enctype="multipart/form-data">
            
            <!-- Identitas Card -->
            <div class="card setup-card p-4">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-display me-2"></i>Identitas Sistem</h6>
                <div class="mb-3">
                    <label class="form-label">Nama Sistem</label>
                    <input type="text" name="nama_sistem" class="form-control" value="<?php echo htmlspecialchars($sett['nama_sistem']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="2"><?php echo htmlspecialchars($sett['deskripsi']); ?></textarea>
                </div>
                <div class="row">
                    <div class="col-7 mb-3">
                        <label class="form-label">Copyright</label>
                        <input type="text" name="copyright" class="form-control" value="<?php echo htmlspecialchars($sett['copyright']); ?>">
                    </div>
                    <div class="col-5 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="text" name="tahun_sistem" class="form-control" value="<?php echo htmlspecialchars($sett['tahun_sistem']); ?>">
                    </div>
                </div>
            </div>

            <!-- Kontak Card -->
            <div class="card setup-card p-4">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-telephone me-2"></i>Kontak & Alamat</h6>
                <div class="mb-3">
                    <label class="form-label">WhatsApp Admin</label>
                    <input type="text" name="kontak_admin" class="form-control" value="<?php echo htmlspecialchars($sett['kontak_admin']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat Kantor</label>
                    <textarea name="alamat_kantor" class="form-control" rows="2"><?php echo htmlspecialchars($sett['alamat_kantor']); ?></textarea>
                </div>
            </div>

            <!-- Visual Card -->
            <div class="card setup-card p-4">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-image me-2"></i>Visual Aset</h6>
                <div class="row align-items-center mb-3">
                    <div class="col-3">
                        <?php if (!empty($sett['logo'])): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($sett['logo']); ?>" alt="Logo" class="current-image">
                        <?php endif; ?>
                    </div>
                    <div class="col-9">
                        <label class="form-label">Logo Sistem</label>
                        <input type="file" name="logo" class="form-control">
                    </div>
                </div>
                <div class="row align-items-center mb-4">
                    <div class="col-3">
                        <?php if (!empty($sett['favicon'])): ?>
                            <img src="../assets/img/<?php echo htmlspecialchars($sett['favicon']); ?>" alt="Fav" class="current-image">
                        <?php endif; ?>
                    </div>
                    <div class="col-9">
                        <label class="form-label">Favicon</label>
                        <input type="file" name="favicon" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-bold py-3 shadow" style="border-radius: 15px;">
                    Simpan Perubahan <i class="bi bi-save ms-2"></i>
                </button>
            </div>

        </form>

        <div class="text-center mt-2">
            <p class="text-muted" style="font-size: 10px;">&copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?></p>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>