<?php
session_start();
include '../config/koneksi.php';

// Proteksi Admin
if ($_SESSION['role'] != "admin") { header("location:../login.php"); exit(); }

// --- 1. KONFIGURASI PAGINASI & FILTER ---
$limit = 5; // Jumlah data per halaman
$page  = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Ambil keyword pencarian & filter tanggal
$search = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';
$filter_tgl = isset($_GET['d']) ? mysqli_real_escape_string($koneksi, $_GET['d']) : '';

// --- 2. QUERY SQL ---
// Membuat string query pencarian
$where_clause = "WHERE 1=1";
if (!empty($search)) {
    $where_clause .= " AND (nama LIKE '%$search%' OR instansi LIKE '%$search%' OR maksud_tujuan LIKE '%$search%')";
}
if (!empty($filter_tgl)) {
    $where_clause .= " AND tanggal = '$filter_tgl'";
}

// Hitung total data untuk paginasi
$total_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku_tamu $where_clause");
$total_data = mysqli_fetch_assoc($total_query)['total'];
$total_pages = ceil($total_data / $limit);

// Ambil data dengan Limit & Offset
$query_tamu = mysqli_query($koneksi, "SELECT * FROM buku_tamu $where_clause ORDER BY id DESC LIMIT $offset, $limit");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Tamu - <?php echo htmlspecialchars($sett['nama_sistem']); ?></title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #1e293b, #334155); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: -30px; }
        
        .search-card { border: none; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .form-control { border-radius: 12px; font-size: 13px; background: #f8f9fa; }

        .guest-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); transition: 0.3s; border-left: 5px solid #0d6efd; }
        .avatar-box { width: 45px; height: 45px; background: #f1f5f9; color: #1e293b; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        
        .pagination .page-link { border-radius: 10px; margin: 0 3px; border: none; color: #1e293b; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .pagination .page-item.active .page-link { background-color: #0d6efd; color: #fff; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="rekap.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Detail Buku Tamu</h4>
        </div>
    </div>

    <div class="container mt-5">
        <!-- Pencarian & Filter -->
        <div class="card search-card p-3 mb-4">
            <form method="GET" action="">
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-0 ps-0" placeholder="Cari nama atau instansi..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="row g-2">
                    <div class="col-8">
                        <input type="date" name="d" class="form-control" value="<?php echo $filter_tgl; ?>">
                    </div>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary w-100 rounded-3 small">Filter</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="px-2 mb-3 d-flex justify-content-between">
            <small class="text-muted">Menampilkan <?php echo mysqli_num_rows($query_tamu); ?> dari <?php echo $total_data; ?> tamu</small>
            <?php if(!empty($search) || !empty($filter_tgl)) { ?>
                <a href="tamu_list.php" class="text-danger small text-decoration-none">Hapus Filter</a>
            <?php } ?>
        </div>

        <!-- Loop Data Tamu -->
        <?php 
        if(mysqli_num_rows($query_tamu) == 0) {
            echo '<div class="text-center py-5 text-muted"><i class="bi bi-search fs-1 d-block"></i>Data tidak ditemukan.</div>';
        }

        while($t = mysqli_fetch_array($query_tamu)){
            $inisial = strtoupper(substr($t['nama'], 0, 1));
            // Format WA
            $phone = preg_replace('/[^0-9]/', '', $t['no_wa'] ?? '');
            if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
        ?>
        <div class="card guest-card">
            <div class="card-body p-3 text-start">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-box me-3"><?php echo $inisial; ?></div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($t['nama']); ?></h6>
                        <small class="text-primary fw-bold" style="font-size: 10px;"><?php echo htmlspecialchars($t['instansi']); ?></small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block" style="font-size: 9px;"><?php echo date('d M Y', strtotime($t['tanggal'])); ?></small>
                        <small class="text-muted d-block" style="font-size: 8px;"><?php echo date('H:i', strtotime($t['created_at'])); ?></small>
                    </div>
                </div>

                <div class="bg-light p-2 rounded-3 mb-3" style="font-size: 12px;">
                    <small class="text-muted d-block" style="font-size: 9px; font-weight: bold; text-transform: uppercase;">Keperluan:</small>
                    <?php echo htmlspecialchars($t['maksud_tujuan']); ?>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size: 11px;" class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i> <?php echo htmlspecialchars($t['alamat']); ?>
                    </div>
                    <a href="https://wa.me/<?php echo $phone; ?>" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm" style="font-size: 10px;">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>

        <!-- Paginasi -->
        <?php if($total_pages > 1) { ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page-1; ?>&q=<?php echo $search; ?>&d=<?php echo $filter_tgl; ?>"><i class="bi bi-chevron-left"></i></a>
                </li>
                <?php for($i=1; $i<=$total_pages; $i++) { ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link shadow-sm" href="?p=<?php echo $i; ?>&q=<?php echo $search; ?>&d=<?php echo $filter_tgl; ?>"><?php echo $i; ?></a>
                    </li>
                <?php } ?>
                <li class="page-item <?php if($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link shadow-sm" href="?p=<?php echo $page+1; ?>&q=<?php echo $search; ?>&d=<?php echo $filter_tgl; ?>"><i class="bi bi-chevron-right"></i></a>
                </li>
            </ul>
        </nav>
        <?php } ?>

    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>