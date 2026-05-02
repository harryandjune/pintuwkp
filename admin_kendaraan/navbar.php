<?php
$current_page = basename($_SERVER['PHP_SELF']);
include_once '../config/koneksi.php';
$nav_pending = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi_kendaraan WHERE status='pending'"));
?>

<style>
    /* Kontainer Utama Navigasi */
    .nav-wrapper {
        position: fixed;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 95%;
        max-width: 500px;
        z-index: 1000;
    }

    /* Bar Navigasi Utama */
    .bottom-nav {
        background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        padding: 10px 2px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    /* Tombol Bulat Tengah (FAB) */
    .fab-button {
        position: absolute;
        top: -28px; /* Posisi menonjol ke atas */
        left: 50%;
        transform: translateX(-50%);
        width: 52px;
        height: 52px;
        background: #f59e0b; /* Warna Amber/Mustard */
        color: #0f172a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px; /* Ukuran ikon disesuaikan */
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.4);
        border: 4px solid #f4f7f6; /* Border pemisah agar terlihat tenggelam */
        text-decoration: none;
        z-index: 1001;
        transition: all 0.3s ease;
    }

    .fab-button:active {
        transform: translateX(-50%) scale(0.9);
    }

    .nav-item {
        text-align: center;
        color: #94a3b8;
        text-decoration: none;
        font-size: 8px; /* Font kecil agar muat 6 menu */
        flex: 1;
        position: relative;
        z-index: 1000;
    }

    /* Memberi celah di tengah agar ikon tidak tertutup FAB */
    .nav-item:nth-child(3) { margin-right: 20px; }
    .nav-item:nth-child(4) { margin-left: 20px; }

    .nav-item i {
        font-size: 18px;
        display: block;
        margin-bottom: 2px;
    }

    .nav-item.active {
        color: #f59e0b;
    }

    .nav-item.text-danger {
        color: #f87171 !important;
    }

    /* Badge Notifikasi */
    .nav-badge {
        position: absolute;
        top: -4px;
        right: 10%;
        font-size: 7px;
        padding: 2px 5px;
        border-radius: 50%;
    }
</style>

<div class="nav-wrapper">
    <!-- Tombol Bulat Create (Booking Manual) dengan Ikon Pensil/Create -->
    <a href="booking_manual.php" class="fab-button">
        <i class="bi bi-pencil-square"></i>
    </a>

    <!-- Bar Menu -->
    <div class="bottom-nav">
        <!-- DASHBOARD -->
        <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'index.php') ? 'bi-speedometer2' : 'bi-speedometer'; ?>"></i>
            <span>Dash</span>
        </a>

        <!-- PERSETUJUAN -->
        <a href="persetujuan.php" class="nav-item <?php echo ($current_page == 'persetujuan.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'persetujuan.php') ? 'bi-clipboard-check-fill' : 'bi-clipboard-check'; ?>"></i>
            <span>Izin</span>
            <?php if ($nav_pending > 0) { ?>
                <span class="badge rounded-pill bg-danger nav-badge"><?php echo $nav_pending; ?></span>
            <?php } ?>
        </a>

        <!-- KALENDER -->
        <a href="kalender.php" class="nav-item <?php echo ($current_page == 'kalender.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'kalender.php') ? 'bi-calendar3-fill' : 'bi-calendar3'; ?>"></i>
            <span>Jadwal</span>
        </a>

        <!-- RUANG KOSONG UNTUK FAB DI SINI -->

        <!-- ARMADA -->
        <a href="kendaraan.php" class="nav-item <?php echo ($current_page == 'kendaraan.php' || $current_page == 'kendaraan_tambah.php' || $current_page == 'kendaraan_edit.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'kendaraan.php') ? 'bi-car-front-fill' : 'bi-car-front'; ?>"></i>
            <span>Armada</span>
        </a>

        <!-- PROFIL -->
        <a href="profil.php" class="nav-item <?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'profil.php') ? 'bi-person-fill' : 'bi-person'; ?>"></i>
            <span>Profil</span>
        </a>

        <!-- LOGOUT -->
        <a href="../logout.php" class="nav-item text-danger" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-power"></i>
            <span>Out</span>
        </a>
    </div>
</div>