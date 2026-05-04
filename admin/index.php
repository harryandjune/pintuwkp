<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman Admin
if ($_SESSION['role'] != "admin") {
    header("location:../login.php");
    exit();
}

// 1. STATISTIK KARTU (STAT CARDS)
$count_pending   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
$count_approved  = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='disetujui'"));
$count_ruangan   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM ruangan"));
$total_tamu      = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM buku_tamu"));

// 2. DATA UNTUK PIE CHART (Gedung vs GH)
$q_pie = mysqli_query($koneksi, "SELECT tipe_permintaan, COUNT(*) as total FROM reservasi GROUP BY tipe_permintaan");
$labels_pie = [];
$data_pie = [];
while($row = mysqli_fetch_assoc($q_pie)) {
    $labels_pie[] = ($row['tipe_permintaan'] == 'guest_house') ? 'Guest House' : 'Meeting Room';
    $data_pie[] = $row['total'];
}

// 3. DATA UNTUK BAR CHART (Top 5 Unit Peminjam)
$q_bar = mysqli_query($koneksi, "SELECT institusi_peminjam, COUNT(*) as total FROM reservasi GROUP BY institusi_peminjam ORDER BY total DESC LIMIT 5");
$labels_bar = [];
$data_bar = [];
while($row = mysqli_fetch_assoc($q_bar)) {
    $labels_bar[] = $row['institusi_peminjam'];
    $data_bar[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo $sett['nama_sistem']; ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 40px 20px 60px; border-radius: 0 0 40px 40px; }
        .stat-card { border: none; border-radius: 20px; padding: 20px; background: #fff; box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05); transition: 0.3s; }
        .stat-card:active { transform: scale(0.95); }
        .icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 10px; }
        .chart-card { border: none; border-radius: 25px; background: #fff; padding: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.05); margin-bottom: 20px; }
    </style>
</head>

<body>

    <!-- Header Section -->
    <div class="header-section shadow">
        <div class="container text-center">
            <h6 class="opacity-75 mb-1">Administrator Dashboard</h6>
            <h4 class="fw-bold"><?php echo $sett['nama_sistem']; ?></h4>

            <a href="users.php" class="text-decoration-none">
                <div class="mt-3 badge bg-primary px-3 py-2 rounded-pill shadow-sm border border-light border-opacity-25">
                    <i class="bi bi-person-circle me-2"></i> <?php echo $_SESSION['nama']; ?>
                    <i class="bi bi-chevron-right ms-2 small opacity-50"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-3">
        <?php if ($count_pending > 0) { ?>
            <div class="alert alert-warning border-0 shadow-sm mx-2 mb-4 d-flex align-items-center" style="border-radius: 15px; margin-top: -20px;">
                <i class="bi bi-bell-fill fs-4 me-3"></i>
                <small class="fw-bold" style="font-size: 12px;">Ada <?php echo $count_pending; ?> pengajuan menunggu persetujuan!</small>
            </div>
        <?php } ?>

        <!-- Grid Statistik 2x2 -->
        <div class="row g-3 px-2 mb-4">
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='persetujuan.php'">
                    <div class="icon-box bg-warning text-white shadow-sm mx-auto"><i class="bi bi-hourglass-split"></i></div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_pending; ?></h4>
                    <small class="text-muted d-block">Pending</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='rekap.php'">
                    <div class="icon-box bg-success text-white shadow-sm mx-auto"><i class="bi bi-people"></i></div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $total_tamu; ?></h4>
                    <small class="text-muted d-block">Tamu</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='ruangan.php'">
                    <div class="icon-box bg-primary text-white shadow-sm mx-auto"><i class="bi bi-building"></i></div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_ruangan; ?></h4>
                    <small class="text-muted d-block">Ruangan</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stat-card text-center" onclick="window.location.href='kalender.php'">
                    <div class="icon-box bg-danger text-white shadow-sm mx-auto"><i class="bi bi-calendar3"></i></div>
                    <h4 class="fw-bold mb-0 mt-2"><?php echo $count_approved; ?></h4>
                    <small class="text-muted d-block">Jadwal</small>
                </div>
            </div>
        </div>

        <!-- VISUALISASI GRAFIK -->
        <div class="px-2">
            <!-- Pie Chart -->
            <div class="chart-card">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Proporsi Layanan</h6>
                <div style="height: 200px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="chart-card">
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-bar-chart-line-fill me-2 text-success"></i>Top 5 Unit Peminjam</h6>
                <div style="height: 250px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Footer Identitas -->
        <div class="card stat-card mx-2 mt-2 text-center">
            <h6 class="fw-bold mb-2">Manajemen <?php echo $sett['nama_sistem']; ?></h6>
            <p class="small text-muted px-3 mb-0"><?php echo $sett['deskripsi']; ?></p>
            <div class="copyright-text border-top mt-3 pt-3">
                &copy; <?php echo $sett['tahun_sistem']; ?> <?php echo $sett['copyright']; ?>
            </div>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. LOGIKA PIE CHART
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels_pie); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_pie); ?>,
                    backgroundColor: ['#0dcaf0', '#0d6efd'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                }
            }
        });

        // 2. LOGIKA BAR CHART
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_bar); ?>,
                datasets: [{
                    label: 'Jumlah Booking',
                    data: <?php echo json_encode($data_bar); ?>,
                    backgroundColor: '#198754',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 10 } } }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>