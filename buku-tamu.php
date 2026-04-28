<?php
include 'config/koneksi.php'; // Mengambil data pengaturan ($sett)

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
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --mustard: #e1ad01;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
            --placeholder-color: #a0a0a0;
        }

        body {
            background-color: var(--dark-bg);
            color: #ffffff;
            font-family: 'Poppins', sans-serif;
            padding: 40px 0;
            display: flex;
            align-items: center;
            min-height: 100vh;
        }

        .form-container {
            max-width: 500px;
            margin: auto;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 30px;
            border-top: 8px solid var(--mustard);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }

        .logo-wrapper {
            text-align: center;
            margin-bottom: 15px;
        }

        .guest-icon {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .guest-icon img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        .guest-icon i {
            font-size: 3.5rem;
            color: var(--mustard);
        }

        .header-title {
            color: var(--mustard);
            font-weight: 600;
            text-align: center;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 1.4rem;
        }

        .header-subtitle {
            text-align: center;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 30px;
        }

        .form-label {
            color: var(--mustard);
            font-size: 0.8rem;
            font-weight: 600;
            margin-left: 5px;
            text-transform: uppercase;
        }

        .form-control {
            background-color: #2a2a2a;
            border: 1px solid #333;
            color: #fff;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        .form-control::placeholder {
            color: var(--placeholder-color) !important;
            font-size: 0.9rem;
        }

        .form-control:focus {
            background-color: #2a2a2a;
            color: #fff;
            border-color: var(--mustard);
            box-shadow: 0 0 0 0.25rem rgba(225, 173, 1, 0.25);
        }

        .btn-submit {
            background-color: var(--mustard);
            color: #000;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 15px;
            font-weight: 700;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #c49601;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(225, 173, 1, 0.2);
        }

        .btn-success-back {
            background-color: transparent;
            border: 2px solid var(--mustard);
            color: var(--mustard);
            width: 100%;
            padding: 12px;
            border-radius: 15px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: 0.3s;
        }

        .btn-success-back:hover {
            background: var(--mustard);
            color: #000;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .copyright {
            text-align: center;
            font-size: 0.7rem;
            color: #555;
            margin-top: 25px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        
        <?php if ($show_success): ?>
            <!-- Tampilan Berhasil -->
            <div class="text-center py-4">
                <div class="guest-icon mb-4">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h2 class="header-title">Data Tersimpan</h2>
                <p class="text-secondary small px-3">Terima kasih. Data kunjungan Anda di <b><?php echo htmlspecialchars($sett['nama_sistem']); ?></b> telah berhasil kami catat.</p>
                <a href="index.php" class="btn btn-success-back">Selesai / Kembali</a>
            </div>
        <?php else: ?>
            <!-- Form Utama -->
            <div class="logo-wrapper">
                <div class="guest-icon">
                    <?php if (!empty($sett['logo']) && file_exists('../assets/img/'.$sett['logo'])): ?>
                        <img src="../assets/img/<?php echo $sett['logo']; ?>" alt="Logo">
                    <?php else: ?>
                        <i class="bi bi-person-rolodex"></i>
                    <?php endif; ?>
                </div>
            </div>

            <h2 class="header-title"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h2>
            <p class="header-subtitle text-uppercase small">Buku Tamu Digital Yayasan Ponpes Hidayatullah Balikpapan</p>

            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama sesuai identitas" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Asal Instansi</label>
                    <input type="text" name="instansi" class="form-control" placeholder="Contoh: Dinas / PT. Berkah" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Maksud & Tujuan</label>
                    <textarea name="tujuan" class="form-control" rows="2" placeholder="Tujuan kunjungan Anda..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Masukkan alamat singkat" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="number" name="no_wa" class="form-control" placeholder="Contoh: 0812xxxxxxxx" required>
                </div>

                <button type="submit" name="simpan_tamu" class="btn btn-submit shadow">Kirim Data Kedatangan</button>
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