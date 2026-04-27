<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Admin Kendaraan
if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

// --- LOGIKA UPDATE DATA PERSONAL ---
if (isset($_POST['update_profile'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $unit  = mysqli_real_escape_string($koneksi, $_POST['unit']);
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);

    $query_update = "UPDATE users SET nama_lengkap='$nama', unit='$unit', no_wa='$no_wa' WHERE id='$user_id'";
    
    if (mysqli_query($koneksi, $query_update)) {
        $_SESSION['nama'] = $nama; // Update nama di session agar dashboard berubah
        $msg = "<div class='alert alert-success border-0 shadow-sm small rounded-3 text-center'>Profil berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 shadow-sm small rounded-3 text-center'>Gagal memperbarui profil.</div>";
    }
}

// --- LOGIKA UPDATE PASSWORD ---
if (isset($_POST['update_password'])) {
    $password_baru = md5($_POST['password_baru']);
    $update_pass = mysqli_query($koneksi, "UPDATE users SET password = '$password_baru' WHERE id = '$user_id'");
    if ($update_pass) {
        $msg = "<div class='alert alert-success border-0 shadow-sm small rounded-3 text-center'>Password berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 shadow-sm small rounded-3 text-center'>Gagal memperbarui password.</div>";
    }
}

// Ambil data terbaru untuk ditampilkan di form
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$u = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin Transport - <?php echo $sett['nama_sistem']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        
        .header-section { 
            background: linear-gradient(135deg, #0f172a, #1e293b); 
            color: white; 
            padding: 40px 20px 80px; 
            border-radius: 0 0 40px 40px; 
            text-align: center; 
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: #f59e0b;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -50px auto 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            font-size: 45px;
            border: 5px solid #f4f7f6;
        }

        .setup-card { 
            border: none; 
            border-radius: 25px; 
            background: #fff; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
        }

        .form-label { font-size: 12px; font-weight: 600; color: #64748b; margin-left: 5px; }
        .form-control { border-radius: 12px; padding: 10px 15px; font-size: 14px; background: #f8f9fa; border: 1px solid #e2e8f0; }
        .form-control:focus { background-color: #fff; border-color: #f59e0b; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1); }
        
        .btn-update { border-radius: 15px; padding: 12px; font-weight: 600; background: #f59e0b; border: none; color: #000; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <h4 class="fw-bold mb-0">Profil Pengelola</h4>
        <p class="small opacity-75">Manajemen Transportasi Yayasan</p>
    </div>

    <div class="container">
        <div class="profile-avatar shadow">
            <i class="bi bi-person-badge"></i>
        </div>
        
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($u['nama_lengkap']); ?></h5>
            <span class="badge bg-dark-subtle text-dark rounded-pill" style="font-size: 10px;">ADMIN KENDARAAN</span>
        </div>

        <?php if(isset($msg)) echo $msg; ?>

        <!-- Form Data Personal -->
        <div class="card setup-card p-4 mb-4">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-person-lines-fill me-2 text-warning"></i>Data Personal</h6>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($u['nama_lengkap']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Departemen</label>
                    <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($u['unit']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Nomor WhatsApp (Untuk Notifikasi)</label>
                    <input type="number" name="no_wa" class="form-control" value="<?php echo htmlspecialchars($u['no_wa']); ?>" placeholder="0812..." required>
                    <small class="text-muted" style="font-size: 10px;">*Nomor ini digunakan untuk menerima pesan booking dari user</small>
                </div>
                <button type="submit" name="update_profile" class="btn btn-update w-100 shadow-sm">
                    Simpan Perubahan Data
                </button>
            </form>
        </div>

        <!-- Form Ganti Password -->
        <div class="card setup-card p-4 mb-4">
            <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-shield-lock-fill me-2 text-danger"></i>Keamanan Akun</h6>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-outline-danger w-100 fw-bold" style="border-radius: 15px; padding: 12px;">
                    Ganti Password Keamanan
                </button>
            </form>
        </div>

        <div class="text-center mt-2 text-muted" style="font-size: 10px;">
            &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>