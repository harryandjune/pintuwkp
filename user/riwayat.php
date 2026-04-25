<?php
session_start();
include '../config/koneksi.php';

// Pastikan yang akses adalah user
if($_SESSION['role'] != "user") { 
    header("location:../login.php"); 
}

$user_id = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - PINTU WKP</title>
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
            background-color: #f8f9fa;
            padding-bottom: 100px;
        }

        /* Header Styling */
        .header-section {
            background: linear-gradient(135deg, #0d6efd, #0049b8);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: -30px;
        }

        /* Card Riwayat Styling */
        .history-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            border-left: 6px solid #ccc; /* Default border */
        }
        
        .history-card.pending { border-left-color: #ffc107; }   /* Kuning */
        .history-card.disetujui { border-left-color: #198754; } /* Hijau */
        .history-card.ditolak { border-left-color: #dc3545; }   /* Merah */

        .status-badge {
            font-size: 10px;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
        }

        
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container">
            <div class="d-flex align-items-center">
                <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
                <h4 class="fw-bold mb-0">Riwayat Booking</h4>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="px-1 mb-4">
            <p class="text-muted small">Pantau status pengajuan ruangan Anda di bawah ini.</p>
        </div>

        <?php 
        $query = "SELECT reservasi.*, ruangan.nama_ruangan, ruangan.tipe 
                  FROM reservasi 
                  JOIN ruangan ON reservasi.ruangan_id = ruangan.id 
                  WHERE reservasi.user_id = '$user_id' 
                  ORDER BY reservasi.id DESC";
        
        $data = mysqli_query($koneksi, $query);
        
        if(mysqli_num_rows($data) == 0){
            echo '
            <div class="text-center py-5">
                <i class="bi bi-calendar-x text-muted" style="font-size: 50px;"></i>
                <p class="text-muted mt-2">Belum ada riwayat pemesanan.</p>
            </div>';
        }

        while($d = mysqli_fetch_array($data)){
            // Tentukan class status
            $status_class = $d['status']; // pending, disetujui, ditolak
        ?>
        
        <!-- Card Riwayat -->
        <div class="card history-card shadow-sm <?php echo $status_class; ?>">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="text-muted small mb-1 d-block">
                            <i class="bi bi-tag-fill me-1"></i> ID #<?php echo $d['id']; ?>
                        </span>
                        <h6 class="fw-bold mb-0"><?php echo $d['nama_ruangan']; ?></h6>
                    </div>
                    <?php 
                    if($d['status'] == 'pending') echo '<span class="status-badge bg-warning text-dark">Pending</span>';
                    elseif($d['status'] == 'disetujui') echo '<span class="status-badge bg-success text-white">Disetujui</span>';
                    else echo '<span class="status-badge bg-danger text-white">Ditolak</span>';
                    ?>
                </div>

                <div class="row mt-3 border-top pt-2">
                    <div class="col-6 border-end">
                        <small class="text-muted d-block">Waktu & Tanggal:</small>
                        <small class="fw-bold text-primary">
                            <i class="bi bi-calendar3 me-1"></i>
                            <?php 
                                if($d['tipe'] == 'guest_house'){
                                    echo date('d M', strtotime($d['tgl_pinjam'])) . " - " . date('d M Y', strtotime($d['tgl_selesai']));
                                } else {
                                    echo date('d M Y', strtotime($d['tgl_pinjam'])) . "<br>";
                                    echo '<i class="bi bi-clock me-1"></i>' . substr($d['jam_mulai'],0,5) . " - " . substr($d['jam_selesai'],0,5);
                                }
                            ?>
                        </small>
                    </div>
                    <div class="col-6 px-3">
                        <small class="text-muted d-block">Keperluan:</small>
                        <small class="fw-bold d-block text-truncate"><?php echo $d['keperluan']; ?></small>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Card -->

        <?php } ?>

        <div class="text-center mt-4">
            <p class="small text-muted px-4">Ada kendala? Hubungi Admin Kantor WKP untuk konfirmasi lebih lanjut.</p>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <!-- JS CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>