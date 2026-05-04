<?php
// Ambil nama file halaman saat ini untuk menentukan menu "active"
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil jumlah pending untuk lencana (badge)
include_once '../config/koneksi.php';
$count_pending_nav = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<style>
    /* Kontainer Utama Navigasi */
    .nav-wrapper {
        position: fixed;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 95%;
        max-width: 550px;
        z-index: 1000;
    }

    /* Bar Navigasi Utama */
    .bottom-nav {
        background: rgba(30, 41, 59, 0.98); /* Slate Dark */
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
        top: -28px; /* Membuatnya menonjol ke atas */
        left: 50%;
        transform: translateX(-50%);
        width: 52px;
        height: 52px;
        background: #0d6efd; /* Warna Biru Admin */
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
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
        font-size: 8px; /* Ukuran font kecil agar muat 6 menu */
        flex: 1;
        position: relative;
        z-index: 1000;
    }

    /* Memberi celah di tengah agar ikon tidak tertabrak FAB */
    .nav-item:nth-child(3) { margin-right: 20px; }
    .nav-item:nth-child(4) { margin-left: 20px; }

    .nav-item i {
        font-size: 18px;
        display: block;
        margin-bottom: 2px;
    }

    .nav-item.active {
        color: #38bdf8; /* Warna Biru Muda saat aktif */
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
    <!-- Tombol Bulat Create (Booking Manual) -->
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
            <?php if($count_pending_nav > 0) { ?>
                <span class="badge rounded-pill bg-danger nav-badge">
                    <?php echo $count_pending_nav; ?>
                </span>
            <?php } ?>
        </a>
        
        <!-- RUANGAN -->
        <a href="ruangan.php" class="nav-item <?php echo ($current_page == 'ruangan.php' || $current_page == 'ruangan_tambah.php' || $current_page == 'ruangan_edit.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'ruangan.php' || $current_page == 'ruangan_tambah.php' || $current_page == 'ruangan_edit.php') ? 'bi-door-open-fill' : 'bi-door-open'; ?>"></i>
            <span>Ruang</span>
        </a>
        
        <!-- RUANG KOSONG UNTUK FAB DI SINI -->

        <!-- REKAP TAMU -->
        <a href="rekap.php" class="nav-item <?php echo ($current_page == 'rekap.php' || $current_page == 'tamu_list.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'rekap.php' || $current_page == 'tamu_list.php') ? 'bi-person-rolodex' : 'bi-person-lines-fill'; ?>"></i>
            <span>Tamu</span>
        </a>
        
        <!-- PENGATURAN -->
        <a href="pengaturan.php" class="nav-item <?php echo ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
            <i class="bi <?php echo ($current_page == 'pengaturan.php') ? 'bi-gear-fill' : 'bi-gear'; ?>"></i>
            <span>Set</span>
        </a>
        
        <!-- LOGOUT -->
        <a href="../logout.php" class="nav-item text-danger" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-power"></i>
            <span>Out</span>
        </a>
    </div>
</div>