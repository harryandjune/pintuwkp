<?php
$current_page = basename($_SERVER['PHP_SELF']);
include_once '../config/koneksi.php';
$nav_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
?>

<style>
    .bottom-nav { position: fixed; bottom: 15px; left: 50%; transform: translateX(-50%); width: 95%; max-width: 500px; background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(15px); border-radius: 20px; display: flex; justify-content: space-around; padding: 10px 5px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; }
    .nav-item { text-align: center; color: #94a3b8; text-decoration: none; font-size: 9px; flex: 1; position: relative; }
    .nav-item i { font-size: 18px; display: block; margin-bottom: 2px; }
    .nav-item.active { color: #f59e0b; } /* Warna Kuning untuk Admin Kendaraan */
</style>

<div class="bottom-nav">
    <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <i class="bi <?php echo ($current_page == 'index.php') ? 'bi-speedometer2' : 'bi-speedometer'; ?>"></i>
        <span>Dash</span>
    </a>
    <a href="persetujuan.php" class="nav-item <?php echo ($current_page == 'persetujuan.php') ? 'active' : ''; ?>">
        <i class="bi bi-clipboard-check"></i>
        <span>Izin</span>
        <?php if($nav_pending > 0) { ?>
            <span class="position-absolute top-0 start-60 translate-middle badge rounded-pill bg-danger" style="font-size: 8px;"><?php echo $nav_pending; ?></span>
        <?php } ?>
    </a>
    <!-- MENU KALENDER / JADWAL -->
    <a href="kalender.php" class="nav-item <?php echo ($current_page == 'kalender.php') ? 'active' : ''; ?>">
        <i class="bi bi-calendar3"></i>
        <span>Jadwal</span>
    </a>
    <a href="kendaraan.php" class="nav-item <?php echo ($current_page == 'kendaraan.php' || $current_page == 'kendaraan_tambah.php') ? 'active' : ''; ?>">
        <i class="bi bi-car-front-fill"></i>
        <span>Armada</span>
    </a>
    <a href="../logout.php" class="nav-item text-danger" onclick="return confirm('Keluar?')">
        <i class="bi bi-power"></i>
        <span>Out</span>
    </a>
</div>