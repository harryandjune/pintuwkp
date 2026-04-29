<?php
session_start();
include 'config/koneksi.php';

// --- 1. CEK JIKA SUDAH LOGIN (Gunakan $_SESSION) ---
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    $role = $_SESSION['role'];
    
    if ($role == "super_admin") {
        header("location:super_admin/index.php");
    } elseif ($role == "admin") {
        header("location:admin/index.php");
    } elseif ($role == "admin_kendaraan") {
        header("location:admin_kendaraan/index.php");
    } else {
        header("location:user/index.php");
    }
    exit(); // PENTING: Hentikan eksekusi script agar tidak loop
}

// --- 2. PROSES LOGIN DITEKAN ---
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);

        // Simpan data ke dalam Session
        $_SESSION['id_user'] = $data['id'];
        $_SESSION['nama']    = $data['nama_lengkap'];
        $_SESSION['role']    = $data['role'];
        $_SESSION['status']  = "login";

        // Arahkan ke dashboard yang sesuai
        if ($data['role'] == "super_admin") {
            header("location:super_admin/index.php");
        } elseif ($data['role'] == "admin") {
            header("location:admin/index.php");
        } elseif ($data['role'] == "admin_kendaraan") {
            header("location:admin_kendaraan/index.php");
        } else {
            header("location:user/index.php");
        }
        exit(); // PENTING
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f0f2f5; height: 100vh; display: flex; align-items: center; }
        .login-card { border: none; border-radius: 25px; box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1); overflow: hidden; }
        .brand-logo { width: 80px; height: 80px; background: linear-gradient(135deg, #5eb4df, #072c64); color: white; border-radius: 22px; display: flex; align-items: center; justify-content: center; font-size: 35px; margin: 0 auto 15px; box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); overflow: hidden; }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
        .form-control { border-radius: 12px; padding: 12px 18px; background-color: #f8f9fa; border: 1px solid #eee; font-size: 14px; }
        .btn-login { border-radius: 15px; padding: 12px; font-weight: 600; background: linear-gradient(135deg, #0d6efd, #0049b8); border: none; transition: all 0.3s; color: white; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3); }
        .copyright-text { font-size: 11px; color: #adb5bd; margin-top: 25px; border-top: 1px solid #f1f1f1; padding-top: 15px; }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-5 col-lg-4">
                <div class="card login-card p-3 p-md-4">
                    <div class="card-body text-center">
                        <div class="brand-logo">
                            <?php if (!empty($sett['logo']) && file_exists('assets/img/' . $sett['logo'])): ?>
                                <img src="assets/img/<?php echo $sett['logo']; ?>" alt="Logo">
                            <?php else: ?>
                                <i class="bi bi-door-open-fill"></i>
                            <?php endif; ?>
                        </div>
                        <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h3>
                        <p class="text-muted small mb-4"><?php echo htmlspecialchars($sett['deskripsi']); ?></p>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger py-2 text-center small border-0 mb-3" role="alert" style="border-radius: 12px;">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="text-start">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary ms-1">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocapitalize="none" autocorrect="off" spellcheck="false">
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary ms-1">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-login w-100 mb-3">Masuk Ke Sistem</button>
                        </form>
                        <div class="text-center">
                            <p class="small text-muted mb-0">Belum punya akses? <a href="register.php" class="text-decoration-none fw-bold">Daftar</a></p>
                        </div>
                        <div class="text-center copyright-text">
                            &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo htmlspecialchars($sett['copyright']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>