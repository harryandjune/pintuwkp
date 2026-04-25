<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman
if ($_SESSION['role'] != "user") {
    header("location:../login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

// --- LOGIKA UPDATE PROFIL ---
if (isset($_POST['update_profile'])) {
    $nama  = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $unit  = mysqli_real_escape_string($koneksi, $_POST['unit']);
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);

    $query_update = "UPDATE users SET nama_lengkap='$nama', unit='$unit', no_wa='$no_wa' WHERE id='$user_id'";

    if (mysqli_query($koneksi, $query_update)) {
        $_SESSION['nama'] = $nama; // Update nama di session agar header ikut berubah
        $msg = "<div class='alert alert-success border-0 shadow-sm small rounded-3'>Profil berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 shadow-sm small rounded-3'>Gagal memperbarui profil.</div>";
    }
}

// --- LOGIKA GANTI PASSWORD ---
if (isset($_POST['update_password'])) {
    $password_baru = md5($_POST['password_baru']);
    $update_pass = mysqli_query($koneksi, "UPDATE users SET password = '$password_baru' WHERE id = '$user_id'");
    if ($update_pass) {
        $msg = "<div class='alert alert-success border-0 shadow-sm small rounded-3'>Password berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 shadow-sm small rounded-3'>Gagal memperbarui password.</div>";
    }
}

// Ambil data user terbaru untuk ditampilkan di form
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$u = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - <?php echo $sett['nama_sistem']; ?></title>
    <!-- Favicon Dinamis -->
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
            background-color: #f0f2f5;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 40px 20px 80px;
            border-radius: 0 0 40px 40px;
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -50px auto 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            color: #0d6efd;
            font-size: 50px;
            border: 5px solid #f0f2f5;
        }

        .info-card {
            border: none;
            border-radius: 25px;
            background: #fff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            margin-left: 5px;
        }

        .form-control {
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #eee;
            padding: 10px 15px;
            font-size: 14px;
        }

        .form-control:focus {
            background: #fff;
            border-color: #0d6efd;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .btn-custom {
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
        }

        .copyright-text {
            font-size: 11px;
            color: #adb5bd;
            margin-top: 20px;
            border-top: 1px solid #f1f1f1;
            padding-top: 15px;
        }
    </style>
</head>

<body>

    <div class="header-section">
        <h4 class="fw-bold mb-0">Profil Pengguna</h4>
        <p class="small opacity-75"><?php echo $sett['nama_sistem']; ?></p>
    </div>

    <div class="container">
        <div class="profile-avatar shadow">
            <i class="bi bi-person-circle"></i>
        </div>
        <div class="text-center mb-4 px-3">
            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($u['nama_lengkap']); ?></h5>
            <p class="text-muted small">@<?php echo htmlspecialchars($u['username']); ?></p>
        </div>

        <?php if (isset($msg)) echo $msg; ?>

        <!-- Form Edit Profil -->
        <div class="card info-card p-4 mb-4">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Data Personal</h6>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" value="<?php echo htmlspecialchars($u['nama_lengkap']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Unit Kerja / Yayasan</label>
                    <input type="text" name="unit" class="form-control" value="<?php echo htmlspecialchars($u['unit']); ?>" required>
                </div>
                <div class="mb-4">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="number" name="no_wa" class="form-control" value="<?php echo htmlspecialchars($u['no_wa']); ?>" placeholder="Contoh: 08123456789" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary btn-custom w-100">
                    Simpan Perubahan Profil
                </button>
            </form>
        </div>

        <!-- Form Ganti Password -->
        <div class="card info-card p-4 mb-4">
            <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-key-fill me-2"></i>Keamanan Akun</h6>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-outline-danger btn-custom w-100">
                    Ganti Password
                </button>
            </form>

            <div class="text-center copyright-text">
                &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
            </div>
        </div>
    </div>

<?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>