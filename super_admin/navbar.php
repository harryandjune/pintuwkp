<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .bottom-nav-sa { position: fixed; bottom: 15px; left: 50%; transform: translateX(-50%); width: 95%; max-width: 500px; background: #312e81; backdrop-filter: blur(15px); border-radius: 20px; display: flex; justify-content: space-around; padding: 12px 5px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); z-index: 1000; }
    .nav-item-sa { text-align: center; color: #a5b4fc; text-decoration: none; font-size: 9px; flex: 1; position: relative; }
    .nav-item-sa i { font-size: 18px; display: block; margin-bottom: 2px; }
    .nav-item-sa.active { color: #fff; font-weight: bold; }
</style>

<div class="bottom-nav-sa">
    <a href="index.php" class="nav-item-sa <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
        <i class="bi bi-speedometer2"></i>
        <span>Dash</span>
    </a>
    <a href="manage_users.php" class="nav-item-sa <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>">
        <i class="bi bi-people-fill"></i>
        <span>Users</span>
    </a>
    <a href="manage_reservasi.php" class="nav-item-sa <?php echo ($current_page == 'manage_reservasi.php') ? 'active' : ''; ?>">
        <i class="bi bi-calendar-check"></i>
        <span>Records</span>
    </a>
    <a href="manage_tamu.php" class="nav-item-sa <?php echo ($current_page == 'manage_tamu.php') ? 'active' : ''; ?>">
        <i class="bi bi-journal-text"></i>
        <span>Guest</span>
    </a>
    <a href="../logout.php" class="nav-item-sa text-danger" onclick="return confirm('Keluar?')">
        <i class="bi bi-power"></i>
        <span>Out</span>
    </a>
</div>