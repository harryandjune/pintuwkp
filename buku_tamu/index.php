<?php
include '../config/koneksi.php'; // Mengambil variabel $sett dari sini

$show_success = false;
if (isset($_POST['simpan_tamu'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $instansi = mysqli_real_escape_string($koneksi, $_POST['instansi']);
    $tujuan   = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_wa    = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $tgl      = date('Y-m-d');

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
    
    <!-- CDN Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --mustard: #E1AD01;
            --dark-gray: #2C2C2C;
            --light-gray: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: var(--dark-gray);
        }

        /* Header Styling */
        .guest-header {
            background: linear-gradient(135deg, var(--dark-gray), #444);
            color: white;
            padding: 60px 20px 80px;
            border-radius: 0 0 50px 50px;
            text-align: center;
            border-bottom: 5px solid var(--mustard);
        }

        .logo-container {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 20px;
            margin: 0 auto 15px;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }

        /* Form Card Styling */
        .form-card {
            border: none;
            border-radius: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-top: -60px;
            background: #fff;
            overflow: hidden;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--dark-gray);
            margin-left: 5px;
        }

        .form-control {
            border-radius: 15px;
            padding: 12px 18px;
            background: var(--light-gray);
            border: 1px solid #eee;
            font-size: 14px;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--mustard);
            box-shadow: 0 0 0 4px rgba(225, 173, 1, 0.1);
        }

        .btn-mustard {
            background-color: var(--mustard);
            color: var(--dark-gray);
            border: none;
            border-radius: 15px;
            padding: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
        }

        .btn-mustard:hover {
            background-color: #c99601;
            transform: translateY(-2px);
            color: #000;
            box-shadow: 0 5px 15px rgba(225, 173, 1, 0.3);
        }

        .success-icon {
            font-size: 80px;
            color: var(--mustard);
        }
    </style>
</head>
<body>

    <?php if ($show_success): ?>
        <!-- Tampilan Sukses -->
        <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="card form-card p-5 text-center w-100" style="max-width: 450px; margin-top:0;">
                <div class="success-icon mb-3">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <h3 class="fw-bold">Terima Kasih</h3>
                <p class="text-muted">Data kunjungan Anda di <b><?php echo htmlspecialchars($sett['nama_sistem']); ?></b> telah kami simpan. Selamat beraktivitas!</p>
                <a href="index.php" class="btn btn-mustard w-100 mt-3 shadow">Kembali</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Header Section -->
        <div class="guest-header shadow">
            <div class="logo-container">
                <?php if (!empty($sett['logo']) && file_exists('../assets/img/'.$sett['logo'])): ?>
                    <img src="../assets/img/<?php echo $sett['logo']; ?>" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                <?php else: ?>
                    <i class="bi bi-journal-text fs-1 text-dark"></i>
                <?php endif; ?>
            </div>
            <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h3>
            <p class="small opacity-75 mb-0">Buku Tamu Digital Kantor WKP</p>
        </div>

        <!-- Form Section -->
        <div class="container mb-5">
            <div class="row justify-content-center">
                <div class="col-11 col-md-8 col-lg-5">
                    <div class="card form-card p-4">
                        <h5 class="fw-bold mb-4 text-center">Registrasi Kedatangan</h5>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama Anda" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Instansi / Asal</label>
                                <input type="text" name="instansi" class="form-control" placeholder="Contoh: Unit Sekolah / PT. Maju Jaya" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tujuan Kunjungan</label>
                                <textarea name="tujuan" class="form-control" rows="2" placeholder="Keperluan kedatangan Anda..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat / Domisili</label>
                                <input type="text" name="alamat" class="form-control" placeholder="Asal kota/daerah">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Nomor WhatsApp</label>
                                <input type="number" name="no_wa" class="form-control" placeholder="0812..." required>
                            </div>
                            <button type="submit" name="simpan_tamu" class="btn btn-mustard w-100 shadow">
                                Simpan Kedatangan <i class="bi bi-chevron-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Footer Copyright Dinamis -->
                    <div class="text-center mt-4 text-muted" style="font-size: 11px;">
                        &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo htmlspecialchars($sett['copyright']); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>