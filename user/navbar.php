<?php
// Ambil nama file halaman saat ini untuk menentukan menu "active"
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    /* CSS Navigasi Bawah Khusus User */
    .bottom-nav {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 450px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        border-radius: 25px;
        display: flex;
        justify-content: space-around;
        padding: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        z-index: 1000;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .nav-item {
        text-align: center;
        color: #adb5bd;
        text-decoration: none;
        font-size: 11px;
        transition: all 0.3s;
        flex: 1;
    }

    .nav-item i {
        font-size: 22px;
        display: block;
        margin-bottom: 2px;
    }

    .nav-item.active {
        color: #0d6efd;
    }

    .nav-item:active {
        transform: scale(0.9);
    }
</style>

<!-- Floating Bottom Navigation -->
<div class="bottom-nav">
    <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <i class="bi <?php echo ($current_page == 'index.php') ? 'bi-grid-1x2-fill' : 'bi-grid-1x2'; ?>"></i>
        <span>Beranda</span>
    </a>
    <a href="aktif.php" class="nav-item <?php echo ($current_page == 'aktif.php') ? 'active' : ''; ?>">
        <i class="bi <?php echo ($current_page == 'aktif.php') ? 'bi-lightning-charge-fill' : 'bi-lightning-charge'; ?>"></i>
        <span>Aktif</span>
    </a>
    <a href="riwayat.php" class="nav-item <?php echo ($current_page == 'riwayat.php') ? 'active' : ''; ?>">
        <i class="bi <?php echo ($current_page == 'riwayat.php') ? 'bi-calendar-event-fill' : 'bi-calendar-event'; ?>"></i>
        <span>Riwayat</span>
    </a>
    <a href="profil.php" class="nav-item <?php echo ($current_page == 'profil.php') ? 'active' : ''; ?>">
        <i class="bi <?php echo ($current_page == 'profil.php') ? 'bi-person-fill' : 'bi-person'; ?>"></i>
        <span>Profil</span>
    </a>
    <a href="../logout.php" class="nav-item text-danger" id="logoutBtn">
        <i class="bi bi-box-arrow-right"></i>
        <span>Keluar</span>
    </a>
</div>

<!-- Script Umum untuk Navigasi -->
<script>
    $(document).ready(function() {
        // Konfirmasi Logout
        $('#logoutBtn').on('click', function(e) {
            if (!confirm('Apakah anda yakin ingin keluar dari PINTU WKP?')) {
                e.preventDefault();
            }
        });
    });
</script>