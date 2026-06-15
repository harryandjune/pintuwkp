<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit();
}

// --- 1. LOGIKA HAPUS USER ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    if ($id_to_delete != $_SESSION['id_user']) {
        mysqli_query($koneksi, "DELETE FROM users WHERE id = '$id_to_delete'");
        $msg = "<div class='alert alert-success border-0 small shadow-sm'>Akun berhasil dihapus.</div>";
    }
}

// --- 2. LOGIKA UPDATE USER (EDIT) ---
if (isset($_POST['update_user'])) {
    $id_user   = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $unit      = mysqli_real_escape_string($koneksi, $_POST['unit']);
    $no_wa     = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $role      = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password  = $_POST['password'];

    // Update data dasar
    $sql_update = "UPDATE users SET nama_lengkap='$nama', unit='$unit', no_wa='$no_wa', role='$role' WHERE id='$id_user'";
    
    if (mysqli_query($koneksi, $sql_update)) {
        // Jika password diisi, maka update password juga
        if (!empty($password)) {
            $pass_fix = md5($password); // Sesuaikan jika Anda sudah beralih ke password_hash
            mysqli_query($koneksi, "UPDATE users SET password='$pass_fix' WHERE id='$id_user'");
        }
        $msg = "<div class='alert alert-success border-0 small shadow-sm'>Data user berhasil diperbarui!</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 small shadow-sm'>Gagal memperbarui data.</div>";
    }
}

// Ambil data semua user
$query_users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role ASC, unit ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Users - Super Admin</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #312e81, #4338ca); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        .user-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .role-badge { font-size: 9px; padding: 3px 8px; border-radius: 6px; font-weight: 700; text-transform: uppercase; }
        .bg-sa { background: #e0e7ff; color: #4338ca; }
        .bg-adm { background: #dcfce7; color: #15803d; }
        .bg-tr { background: #fef3c7; color: #92400e; }
        .bg-usr { background: #f1f5f9; color: #475569; }
        .avatar-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; background: #f8fafc; color: #312e81; border: 1px solid #e2e8f0; }
        .form-control, .form-select { border-radius: 12px; font-size: 14px; background: #f8f9fa; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Manajemen Akun</h4>
        </div>
    </div>

    <div class="container mt-5">
        <?php if(isset($msg)) echo $msg; ?>

        <div class="px-2 mb-4 text-start">
            <h6 class="fw-bold mb-0 text-dark">Daftar Pengguna Sistem</h6>
            <small class="text-muted">Total: <?php echo mysqli_num_rows($query_users); ?> Akun</small>
        </div>

        <div class="row g-2">
            <?php 
            while($u = mysqli_fetch_array($query_users)){
                $inisial = strtoupper(substr($u['nama_lengkap'], 0, 1));
                
                $role_class = 'bg-usr'; $role_name = 'User Unit';
                if($u['role'] == 'super_admin') { $role_class = 'bg-sa'; $role_name = 'Super Admin'; }
                elseif($u['role'] == 'admin') { $role_class = 'bg-adm'; $role_name = 'Admin WKP'; }
                elseif($u['role'] == 'admin_kendaraan') { $role_class = 'bg-tr'; $role_name = 'Admin Transport'; }
            ?>
            
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar-box me-3"><?php echo $inisial; ?></div>
                            
                            <div class="flex-grow-1 text-start">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 me-2" style="font-size: 14px;"><?php echo htmlspecialchars($u['nama_lengkap']); ?></h6>
                                    <span class="role-badge <?php echo $role_class; ?>"><?php echo $role_name; ?></span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($u['unit']); ?>
                                </div>
                            </div>

                            <div class="text-end d-flex gap-2">
                                <!-- Tombol Edit (Trigger Modal) -->
                                <button class="btn btn-outline-primary border-0 btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $u['id']; ?>">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>

                                <?php if($u['id'] != $_SESSION['id_user']) { ?>
                                <a href="manage_users.php?delete_id=<?php echo $u['id']; ?>" class="btn btn-outline-danger border-0 btn-sm" onclick="return confirm('Hapus user ini?')">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT USER -->
            <div class="modal fade" id="editModal<?php echo $u['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" class="modal-content" style="border-radius: 25px;">
                        <div class="modal-header border-0">
                            <h6 class="fw-bold mb-0">Edit Informasi Akun</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-0 text-start">
                            <input type="hidden" name="id_user" value="<?php echo $u['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $u['nama_lengkap']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Unit / Instansi</label>
                                <input type="text" name="unit" class="form-control" value="<?php echo $u['unit']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Nomor WhatsApp</label>
                                <input type="number" name="no_wa" class="form-control" value="<?php echo $u['no_wa']; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold mb-1">Role Akses</label>
                                <select name="role" class="form-select" required>
                                    <option value="user" <?php if($u['role']=='user') echo 'selected'; ?>>User Unit</option>
                                    <option value="admin" <?php if($u['role']=='admin') echo 'selected'; ?>>Admin WKP (Gedung)</option>
                                    <option value="admin_kendaraan" <?php if($u['role']=='admin_kendaraan') echo 'selected'; ?>>Admin Transport (Mobil)</option>
                                    <option value="super_admin" <?php if($u['role']=='super_admin') echo 'selected'; ?>>Super Admin</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold mb-1 text-danger">Reset Password (Opsional)</label>
                                <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin ganti password">
                                <small class="text-muted" style="font-size: 10px;">*Kosongkan jika tidak ingin mengubah password.</small>
                            </div>

                            <button type="submit" name="update_user" class="btn btn-primary w-100 py-2 fw-bold shadow" style="border-radius: 12px; background:#312e81; border:none;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END MODAL -->

            <?php } ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>