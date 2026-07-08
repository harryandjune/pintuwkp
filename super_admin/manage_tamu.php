<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit();
}

// --- LOGIKA HAPUS TAMU ---
if (isset($_GET['delete_id'])) {
    $id_h = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    $query_del = "DELETE FROM buku_tamu WHERE id = '$id_h'";
    if (mysqli_query($koneksi, $query_del)) {
        $msg = "<div class='alert alert-success border-0 small shadow-sm'>Data kunjungan berhasil dihapus.</div>";
    }
}

// --- LOGIKA UPDATE TAMU ---
if (isset($_POST['update_tamu'])) {
    $id_u     = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama_u   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $inst_u   = mysqli_real_escape_string($koneksi, $_POST['instansi']);
    $tujuan_u = mysqli_real_escape_string($koneksi, $_POST['maksud_tujuan']);
    $alamat_u = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $wa_u     = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $tgl_u    = mysqli_real_escape_string($koneksi, $_POST['tanggal']);

    $query_upd = "UPDATE buku_tamu SET 
                    nama = '$nama_u', 
                    instansi = '$inst_u', 
                    maksud_tujuan = '$tujuan_u', 
                    alamat = '$alamat_u', 
                    no_wa = '$wa_u', 
                    tanggal = '$tgl_u' 
                  WHERE id = '$id_u'";
                  
    if (mysqli_query($koneksi, $query_upd)) {
        $msg = "<div class='alert alert-success border-0 small shadow-sm'>Data tamu berhasil diperbarui.</div>";
    } else {
        $msg = "<div class='alert alert-danger border-0 small shadow-sm'>Gagal memperbarui data.</div>";
    }
}

// Konfigurasi Pencarian & Paginasi
$limit = 10; // Dibatasi 10 agar seragam dan rapi
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

$search = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';
$where = !empty($search) ? "WHERE nama LIKE '%$search%' OR instansi LIKE '%$search%'" : "";

// Hitung total data
$total_res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku_tamu $where");
$total_data = mysqli_fetch_assoc($total_res)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data tamu
$query_tamu = mysqli_query($koneksi, "SELECT * FROM buku_tamu $where ORDER BY id DESC LIMIT $offset, $limit");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Buku Tamu - Super Admin</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #312e81, #4338ca); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        
        .search-card { border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); background: #fff; }
        .form-control { border-radius: 12px; font-size: 14px; background: #f8f9fa; border: 1px solid #eee; }
        .form-control:focus { background-color: #fff; border-color: #4338ca; box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.1); }
        
        .guest-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .avatar-box { width: 45px; height: 45px; background: #eef2ff; color: #4338ca; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        .pagination .page-link { border-radius: 8px; margin: 0 3px; border: none; color: #4338ca; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .pagination .page-item.active .page-link { background-color: #4338ca; color: #fff; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0 text-white">Log Tamu Global</h4>
        </div>
    </div>

    <div class="container mt-5">
        <?php if(isset($msg)) echo $msg; ?>

        <!-- Search Bar -->
        <div class="card search-card p-3 mb-4">
            <form method="GET" action="">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-0 bg-transparent" placeholder="Cari nama atau instansi..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-indigo text-white d-none">Cari</button>
                </div>
            </form>
        </div>

        <div class="px-2 mb-3 d-flex justify-content-between align-items-end">
            <div>
                <small class="text-muted d-block">Total: <?php echo $total_data; ?> Kunjungan</small>
                <?php if(!empty($search)) { ?><a href="manage_tamu.php" class="text-danger small text-decoration-none">Reset Filter</a><?php } ?>
            </div>
        </div>

        <!-- Loop Data Tamu -->
        <div class="row g-2">
            <?php 
            if(mysqli_num_rows($query_tamu) == 0) {
                echo '<div class="text-center py-5 text-muted small">Data tidak ditemukan.</div>';
            }
            while($t = mysqli_fetch_array($query_tamu)){
                $inisial = strtoupper(substr($t['nama'], 0, 1));
            ?>
            <div class="col-12">
                <div class="card guest-card">
                    <div class="card-body p-3 text-start">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar-box me-3"><?php echo $inisial; ?></div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-0" style="font-size: 14px;"><?php echo htmlspecialchars($t['nama']); ?></h6>
                                <small class="text-indigo fw-bold" style="font-size: 10px; color: #4338ca;"><?php echo htmlspecialchars($t['instansi']); ?></small>
                            </div>
                            <div class="text-end d-flex gap-2">
                                <!-- Tombol Edit -->
                                <button class="btn btn-outline-primary border-0 btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $t['id']; ?>">
                                    <i class="bi bi-pencil-square fs-5"></i>
                                </button>
                                <!-- Tombol Hapus -->
                                <a href="manage_tamu.php?delete_id=<?php echo $t['id']; ?>" class="btn btn-outline-danger border-0 btn-sm" onclick="return confirm('Hapus data kunjungan ini secara permanen?')">
                                    <i class="bi bi-trash3 fs-5"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-light p-2 rounded-3 mb-2" style="font-size: 11px;">
                            <span class="text-muted fw-bold">TUJUAN:</span> <?php echo htmlspecialchars($t['maksud_tujuan']); ?>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($t['alamat']); ?></small>
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 9px;"><?php echo date('d M Y', strtotime($t['tanggal'])); ?></small>
                                <small class="text-muted d-block" style="font-size: 10px;"><i class="bi bi-whatsapp me-1 text-success"></i> <?php echo $t['no_wa']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODAL EDIT TAMU -->
            <div class="modal fade" id="editModal<?php echo $t['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" class="modal-content" style="border-radius: 25px;">
                        <div class="modal-header border-0 pb-0">
                            <h6 class="fw-bold mb-0">Edit Data Tamu</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body pt-3 text-start">
                            <input type="hidden" name="id" value="<?php echo $t['id']; ?>">
                            
                            <div class="mb-2">
                                <label class="small fw-bold">Tanggal Kunjungan</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo $t['tanggal']; ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($t['nama']); ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Instansi / Asal</label>
                                <input type="text" name="instansi" class="form-control" value="<?php echo htmlspecialchars($t['instansi']); ?>" required>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Maksud & Tujuan</label>
                                <textarea name="maksud_tujuan" class="form-control" rows="2" required><?php echo htmlspecialchars($t['maksud_tujuan']); ?></textarea>
                            </div>
                            <div class="mb-2">
                                <label class="small fw-bold">Alamat</label>
                                <input type="text" name="alamat" class="form-control" value="<?php echo htmlspecialchars($t['alamat']); ?>">
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold">Nomor WhatsApp</label>
                                <input type="number" name="no_wa" class="form-control" value="<?php echo htmlspecialchars($t['no_wa']); ?>" required>
                            </div>

                            <button type="submit" name="update_tamu" class="btn btn-primary w-100 py-2 fw-bold shadow" style="border-radius: 12px; background: #312e81; border:none;">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END MODAL -->

            <?php } ?>
        </div>

        <!-- Paginasi -->
        <?php if($total_pages > 1) { ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page-1; ?>&q=<?php echo $search; ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php 
                // Logika Paginasi 5 Tombol agar tidak terlalu panjang
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                for($i = $start; $i <= $end; $i++) { ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link shadow-sm" href="?p=<?php echo $i; ?>&q=<?php echo $search; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page+1; ?>&q=<?php echo $search; ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
        <?php } ?>
    </div>

    <!-- Panggil Navbar Super Admin -->
    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>