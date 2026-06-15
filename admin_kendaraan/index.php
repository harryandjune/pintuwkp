<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Admin Kendaraan yang boleh masuk
if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

// --- LOGIKA AUTO-SELESAI (OTOMATIS) ---
date_default_timezone_set('Asia/Makassar'); 
$now = date('Y-m-d H:i:s');
mysqli_query($koneksi, "UPDATE reservasi_kendaraan SET status = 'selesai' WHERE status = 'disetujui' AND tgl_selesai < '$now'");


// 1. HITUNG STATISTIK KARTU
$count_pending   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
$count_approved  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='disetujui'"));
$count_mobil     = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_kendaraan FROM kendaraan"));

$tgl_sekarang = date('Y-m-d');
$count_today = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE DATE(tgl_mulai) = '$tgl_sekarang' AND status='disetujui'"));
$count_finished = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='selesai'"));
$count_rejected = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='ditolak'"));


// 2. DATA UNTUK GRAFIK: 5 KENDARAAN PALING SERING DIGUNAKAN
$sql_top_cars = "SELECT k.merk, k.model, COUNT(r.id) as total_pemakaian 
                 FROM reservasi_kendaraan r 
                 JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                 WHERE r.status IN ('disetujui', 'selesai')
                 GROUP BY r.kendaraan_id 
                 ORDER BY total_pemakaian DESC 
                 LIMIT 5";

$res_top_cars = mysqli_query($koneksi, $sql_top_cars);
$labels_mobil = [];
$data_pemakaian = [];

while($row = mysqli_fetch_assoc($res_top_cars)) {
    $labels_mobil[] = $row['merk'] . " " . $row['model'];
    $data_pemakaian[] = $row['total_pemakaian'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Transport - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 120px; }
        .header-section { background: linear-gradient(135deg, #0f172a, #1e293b); color: white; padding: 40px 20px 60px; border-radius: 0 0 40px 40px; }
        .stat-card { border: none; border-radius: 25px; padding: 20px; background: #fff; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); transition: 0.3s; text-align: center; height: 100%; }
        .stat-card:active { transform: scale(0.95); }
        .icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin: 0 auto 10px; }
        
        .bg-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .bg-today   { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
        .bg-active  { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .bg-done    { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
        .bg-reject  { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .bg-fleet   { background: rgba(30, 41, 59, 0.1); color: #1e293b; }

        .count-number { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 0; }
        .stat-label { font-size: 9px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }

        .chart-card { border: none; border-radius: 25px; background: #fff; padding: 25px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 20px; }
    </style>
</head>

<body>

    <div class="header-section shadow text-center">
        <h6 class="opacity-75 mb-1">Manajemen Transportasi</h6>
        <h4 class="fw-bold"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h4>
        <a href="users.php" class="text-decoration-none">
            <div class="mt-3 badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">
                <i class="bi bi-person-circle me-2"></i> <?php echo $_SESSION['nama']; ?>
            </div>
        </a>
    </div>

    <div class="container mt-n4" style="margin-top: -30px;">
        <?php if ($count_pending > 0) { ?>
            <div class="alert alert-warning border-0 shadow-sm mx-2 mb-4 d-flex align-items-center" style="border-radius: 15px;">
                <i class="bi bi-bell-fill fs-4 me-3"></i>
                <small class="fw-bold">Ada <?php echo $count_pending; ?> pengajuan menunggu konfirmasi!</small>
            </div>
        <?php } ?>

        <!-- Grid Statistik 2x2 -->
        <div class="row g-3 px-2">
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='persetujuan.php'">
                    <div class="icon-box bg-pending"><i class="bi bi-hourglass-split"></i></div>
                    <div class="count-number"><?php echo $count_pending; ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='kalender.php'">
                    <div class="icon-box bg-today"><i class="bi bi-calendar-check"></i></div>
                    <div class="count-number"><?php echo $count_today; ?></div>
                    <div class="stat-label">Hari Ini</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm">
                    <div class="icon-box bg-active"><i class="bi bi-play-circle"></i></div>
                    <div class="count-number"><?php echo $count_approved; ?></div>
                    <div class="stat-label">Aktif</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm">
                    <div class="icon-box bg-done"><i class="bi bi-check-all"></i></div>
                    <div class="count-number"><?php echo $count_finished; ?></div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm">
                    <div class="icon-box bg-reject"><i class="bi bi-x-circle"></i></div>
                    <div class="count-number"><?php echo $count_rejected; ?></div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card shadow-sm" onclick="location.href='kendaraan.php'">
                    <div class="icon-box bg-fleet"><i class="bi bi-car-front"></i></div>
                    <div class="count-number"><?php echo $count_mobil; ?></div>
                    <div class="stat-label">Armada</div>
                </div>
            </div>
        </div>

        <!-- VISUALISASI GRAFIK ARMADA TERAKTIF -->
        <div class="px-2 mt-4">
            <div class="chart-card">
                <h6 class="fw-bold mb-3 text-dark text-start">
                    <i class="bi bi-bar-chart-fill me-2 text-warning"></i>Top 5 Armada Teraktif
                </h6>
                <div style="height: 250px;">
                    <canvas id="carChart"></canvas>
                </div>
                <p class="text-muted mt-3 mb-0 text-start" style="font-size: 10px;">
                    *Data berdasarkan total peminjaman yang telah disetujui/selesai.
                </p>
            </div>
        </div>

        <div class="card stat-card mx-2 mt-2 text-center">
            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($sett['nama_sistem']); ?></h6>
            <p class="text-muted px-3 mb-0" style="font-size: 11px;"><?php echo htmlspecialchars($sett['deskripsi']); ?></p>
            <div class="mt-3 pt-3 border-top text-muted" style="font-size: 10px;">
                &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo htmlspecialchars($sett['copyright']); ?>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Logika Grafik Batang Horizontal
        const ctx = document.getElementById('carChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_mobil); ?>,
                datasets: [{
                    label: 'Total Pemakaian',
                    data: <?php echo json_encode($data_pemakaian); ?>,
                    backgroundColor: '#f59e0b',
                    borderRadius: 10,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'y', // Membuat grafik horizontal
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                    y: { ticks: { font: { size: 10, family: 'Poppins' } } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>

</html>