<?php
session_start();
include 'config/koneksi.php';

// Jika sudah login, tidak perlu daftar lagi, lempar ke dashboard
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    if ($_SESSION['role'] == "admin") {
        header("location:admin/index.php");
    } else {
        header("location:user/index.php");
    }
    exit();
}

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $unit     = mysqli_real_escape_string($koneksi, $_POST['unit']);
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");

    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, unit, no_wa, role) 
          VALUES ('$username', '$password', '$nama', '$unit', '$no_wa', 'user')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>

    <!-- Favicon Dinamis -->
    <link rel="icon" type="image/x-icon" href="assets/img/<?php echo $sett['favicon']; ?>">

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px 0;
        }

        .register-card {
            border: none;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #198754, #146c43);
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 15px;
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.2);
        }

        .form-control {
            border-radius: 15px;
            padding: 10px 18px;
            background-color: #f8f9fa;
            border: 1px solid #eee;
            font-size: 14px;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #198754;
            box-shadow: 0 0 0 4px rgba(25, 135, 84, 0.1);
        }

        .btn-register {
            border-radius: 15px;
            padding: 12px;
            font-weight: 600;
            background: linear-gradient(135deg, #198754, #146c43);
            border: none;
            transition: all 0.3s;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
        }

        .copyright-text {
            font-size: 11px;
            color: #adb5bd;
            margin-top: 20px;
            border-top: 1px solid #f1f1f1;
            padding-top: 15px;
        }

        hr {
            opacity: 0.1;
            margin: 20px 0;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-9 col-md-6 col-lg-4">
                <div class="card register-card p-2 p-md-3">
                    <div class="card-body">
                        <!-- Icon Logo -->
                        <div class="brand-logo text-center mx-auto">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>

                        <h3 class="text-center fw-bold mb-1">Daftar Akun</h3>
                        <p class="text-center text-muted small mb-4"><?php echo htmlspecialchars($sett['nama_sistem']); ?></p>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger py-2 text-center small border-0" role="alert" style="border-radius: 12px;">
                                <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary ms-1">Username</label>
                                <input type="text"
                                    name="username"
                                    class="form-control"
                                    placeholder="Masukkan username"
                                    required
                                    autocapitalize="none"
                                    autocorrect="off"
                                    spellcheck="false">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary ms-1">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="" required>
                            </div>

                            <hr>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary ms-1">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" placeholder="" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary ms-1"> Asal Unit / Instansi</label>
                                <input type="text" name="unit" class="form-control" placeholder="" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-secondary ms-1">Nomor WhatsApp</label>
                                <input type="number" name="no_wa" class="form-control" placeholder="" required>
                            </div>

                            <button type="submit" name="register" class="btn btn-success btn-register w-100 mb-3 text-white">
                                Daftar Sekarang
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="small text-muted mb-0">Sudah punya akses? <a href="login.php" class="text-decoration-none fw-bold text-success">Login</a></p>
                        </div>

                        <!-- Copyright Dinamis dari Database -->
                        <div class="text-center copyright-text">
                            &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo htmlspecialchars($sett['copyright']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>