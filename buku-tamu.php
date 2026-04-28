<?php
include 'config/koneksi.php'; // Path disesuaikan karena file ada di root

$show_success = false;
if (isset($_POST['simpan_tamu'])) {
    $tgl      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $instansi = mysqli_real_escape_string($koneksi, $_POST['instansi']);
    $tujuan   = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_wa    = mysqli_real_escape_string($koneksi, $_POST['no_wa']);

    $query = "INSERT INTO buku_tamu (tanggal, nama, instansi, maksud_tujuan, alamat, no_wa) 
              VALUES ('$tgl', '$nama', '$instansi', '$tujuan', '$alamat', '$no_wa')";
    
    if (mysqli_query($koneksi, $query)) {
        $show_success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <!-- Favicon Dinamis -->
    <link rel="icon" type="image/x-icon" href="assets/img/<?php echo $sett['favicon']; ?>">
    
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #f8f9fa;
            --text-dark: #2d3436;
            --text-muted: #636e72;
        }

        body {
            background-color: #f0f2f5;
            color: var(--text-dark);
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            min-height: 100vh;
            padding: 20px 0;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 25px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .logo-wrapper {
            text-align: center;
            margin-bottom: 20px;
        }

        .guest-icon {
            width: 80px;
            height: 80px;
            background: rgba(13, 110, 253, 0.05);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .guest-icon img {
            width: 65%;
            height: 65%;
            object-fit: contain;
        }

        .guest-icon i {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .header-title {
            color: var(--text-dark);
            font-weight: 700;
            text-align: center;
            margin-bottom: 5px;
            font-size: 1.5rem;
        }

        .header-subtitle {
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 30px;
            line-height: 1.4;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-left: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            background-color: #fdfdfd;
            border: 1.5px solid #edf2f7;
            color: var(--text-dark);
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control::placeholder {
            color: #b1b1b1;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: #fff;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 15px;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(13, 110, 253, 0.2);
            color: #fff;
        }

        .btn-success-back {
            background-color: var(--secondary-color);
            color: var(--text-dark);
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            text-align: center;
        }

        .copyright {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 30px;
        }

        /* Styling Kalender pada Input Date agar senada */
        input[type="date"] {
            color: var(--text-dark);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        
        <?php if ($show_success): ?>
            <!-- Tampilan Jika Berhasil Simpan -->
            <div class="text-center py-4">
                <div class="guest-icon mb-4" style="background: rgba(40, 167, 69, 0.1);">
                    <i class="bi bi-check-lg text-success"></i>
                </div>
                <h2 class="header-title">Selamat Datang!</h2>
                <p class="header-subtitle px-2">Terima kasih atas kunjungannya.</p>
                <a href="buku-tamu.php" class="btn btn-success-back">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Awal
                </a>
            </div>
        <?php else: ?>
            <!-- Tampilan Form Utama -->
            <div class="logo-wrapper">
                <div class="guest-icon">
                    <?php if (!empty($sett['logo']) && file_exists('assets/img/'.$sett['logo'])): ?>
                        <img src="assets/img/<?php echo $sett['logo']; ?>" alt="Logo">
                    <?php else: ?>
                        <i class="bi bi-person-badge"></i>
                    <?php endif; ?>
                </div>
            </div>

            <h2 class="header-title"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h2>
            <p class="header-subtitle text-uppercase font-monospace" style="font-size: 0.7rem; letter-spacing: 1px;">Digital Guest Book System</p>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Tuliskan nama Anda" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asal Instansi / Unit</label>
                    <input type="text" name="instansi" class="form-control" placeholder="Contoh: Unit Sekolah / PT. Maju Jaya" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maksud dan Tujuan</label>
                    <textarea name="tujuan" class="form-control" rows="2" placeholder="Keperluan kedatangan Anda..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat / Domisili</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Contoh: Balikpapan / Samarinda" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="number" name="no_wa" class="form-control" placeholder="Contoh: 0812xxxxxxxx" required>
                </div>

                <button type="submit" name="simpan_tamu" class="btn btn-submit">
                    <i class="bi bi-pencil-square me-2"></i> Kirim Kehadiran
                </button>
            </form>
        <?php endif; ?>

        <div class="copyright">
            &copy; <?php echo $sett['tahun_sistem']; ?> - <?php echo htmlspecialchars($sett['copyright']); ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>