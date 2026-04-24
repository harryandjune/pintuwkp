<?php
// Ambil nama file halaman saat ini untuk menentukan menu "active"
$current_page = basename($_SERVER['PHP_SELF']);

// Ambil jumlah pending untuk lencana (badge)
// Cek dulu apakah koneksi ada, jika tidak sertakan (opsional jika sudah ada di file utama)
include_once '../config/koneksi.php';
$count_pending_nav = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM reservasi WHERE status='pending'"));
?>

<style>
    /* CSS Navigasi Bawah (Centralized) */
    .bottom-nav {
        position: fixed;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        width: 95%;
        max-width: 550px;
        background: rgba(30, 41, 59, 0.98);
        backdrop-filter: blur(15px);
        border-radius: 20px;
        display: flex;
        justify-content: space-around;
        padding: 10px 5px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        z-index: 1000;
    }
    .nav-item {
        text-align: center;
        color: #94a3b8;
        text-decoration: none;
        font-size: 9px;
        flex: 1;
        position: relative;
    }
    .nav-item i {
        font-size: 18px;
        display: block;
        margin-bottom: 2px;
    }
    .nav-item.active {
        color: #38bdf8;
    }
    .nav-item.text-danger {
        color: #f87171 !important;
    }
</style>

<!-- Floating Bottom Navigation -->
<div class="bottom-nav">
    <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i>
        <span>Dash</span>
    </a>
    
    <a href="persetujuan.php" class="nav-item <?php echo ($current_page == 'persetujuan.php') ? 'active' : ''; ?>">
        <i class="bi bi-clipboard-check"></i>
        <span>Izin</span>
        <?php if($count_pending_nav > 0) { ?>
            <span class="position-absolute top-0 start-50 translate-middle badge rounded-pill bg-danger" style="font-size: 8px; margin-top: 5px;">
                <?php echo $count_pending_nav; ?>
            </span>
        <?php } ?>
    </a>
    
    <a href="ruangan.php" class="nav-item <?php echo ($current_page == 'ruangan.php' || $current_page == 'ruangan_tambah.php') ? 'active' : ''; ?>">
        <i class="bi bi-door-open"></i>
        <span>Ruang</span>
    </a>
    
    <a href="users.php" class="nav-item <?php echo ($current_page == 'users.php') ? 'active' : ''; ?>">
        <i class="bi bi-people"></i>
        <span>User</span>
    </a>
    
    <a href="pengaturan.php" class="nav-item <?php echo ($current_page == 'pengaturan.php') ? 'active' : ''; ?>">
        <i class="bi bi-gear"></i>
        <span>Set</span>
    </a>
    
    <a href="../logout.php" class="nav-item text-danger" onclick="return confirm('Yakin ingin keluar?')">
        <i class="bi bi-power"></i>
        <span>Out</span>
    </a>
</div>