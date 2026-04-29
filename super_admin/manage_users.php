<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit();
}

// --- LOGIKA HAPUS USER ---
if (isset($_GET['delete_id'])) {
    $id_to_delete = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    
    // Keamanan: Jangan biarkan Super Admin menghapus dirinya sendiri
    if ($id_to_delete == $_SESSION['id_user']) {
        $msg = "<div class='alert alert-danger border-0 small'>Gagal! Anda tidak bisa menghapus akun Anda sendiri.</div>";
    } else {
        $query_del = "DELETE FROM users WHERE id = '$id_to_delete'";
        if (mysqli_query($koneksi, $query_del)) {
            $msg = "<div class='alert alert-success border-0 small'>Akun berhasil dihapus secara permanen.</div>";
        }
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
        
        .user-card { 
            border: none; 
            border-radius: 20px; 
            background: #fff; 
            margin-bottom: 12px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            transition: 0.3s;
        }

        .role-badge {
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .bg-sa { background: #e0e7ff; color: #4338ca; } /* Super Admin */
        .bg-adm { background: #dcfce7; color: #15803d; } /* Admin WKP */
        .bg-tr { background: #fef3c7; color: #92400e; } /* Admin Kendaraan */
        .bg-usr { background: #f1f5f9; color: #475569; } /* User Unit */

        .avatar-box {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            background: #f8fafc;
            color: #312e81;
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Kelola Seluruh Akun</h4>
        </div>
    </div>

    <div class="container mt-5">
        <?php if(isset($msg)) echo $msg; ?>

        <div class="px-2 mb-4">
            <h6 class="fw-bold mb-0 text-dark">Daftar Pengguna Sistem</h6>
            <small class="text-muted">Total: <?php echo mysqli_num_rows($query_users); ?> Akun terdaftar</small>
        </div>

        <div class="row g-2">
            <?php 
            while($u = mysqli_fetch_array($query_users)){
                $inisial = strtoupper(substr($u['nama_lengkap'], 0, 1));
                
                // Tentukan warna badge berdasarkan role
                $role_class = 'bg-usr';
                $role_name = 'User Unit';
                if($u['role'] == 'super_admin') { $role_class = 'bg-sa'; $role_name = 'Super Admin'; }
                elseif($u['role'] == 'admin') { $role_class = 'bg-adm'; $role_name = 'Admin WKP'; }
                elseif($u['role'] == 'admin_kendaraan') { $role_class = 'bg-tr'; $role_name = 'Admin Transport'; }
            ?>
            
            <div class="col-12">
                <div class="card user-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <!-- Avatar -->
                            <div class="avatar-box me-3">
                                <?php echo $inisial; ?>
                            </div>
                            
                            <!-- Info -->
                            <div class="flex-grow-1 text-start">
                                <div class="d-flex align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 me-2" style="font-size: 14px;"><?php echo htmlspecialchars($u['nama_lengkap']); ?></h6>
                                    <span class="role-badge <?php echo $role_class; ?>"><?php echo $role_name; ?></span>
                                </div>
                                <div class="text-muted" style="font-size: 11px;">
                                    <i class="bi bi-building me-1"></i> <?php echo htmlspecialchars($u['unit']); ?> <br>
                                    <i class="bi bi-whatsapp me-1 text-success"></i> <?php echo $u['no_wa']; ?>
                                </div>
                            </div>

                            <!-- Tombol Hapus (Kecuali diri sendiri) -->
                            <?php if($u['id'] != $_SESSION['id_user']) { ?>
                            <div class="text-end">
                                <a href="manage_users.php?delete_id=<?php echo $u['id']; ?>" 
                                   class="btn btn-outline-danger border-0 btn-sm" 
                                   onclick="return confirm('PERINGATAN: Menghapus user akan menghapus seluruh riwayat booking miliknya. Lanjutkan?')">
                                    <i class="bi bi-trash3-fill fs-5"></i>
                                </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <!-- Panggil Navbar Super Admin -->
    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>