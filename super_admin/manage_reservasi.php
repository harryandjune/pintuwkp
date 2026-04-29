<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit();
}

// --- LOGIKA HAPUS DATA ---
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id_h = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    $type = $_GET['type'];

    if ($type == 'gedung') {
        $query_del = "DELETE FROM reservasi WHERE id = '$id_h'";
    } else {
        $query_del = "DELETE FROM reservasi_kendaraan WHERE id = '$id_h'";
    }

    if (mysqli_query($koneksi, $query_del)) {
        $msg = "<div class='alert alert-success border-0 small shadow-sm'>Data reservasi berhasil dihapus permanen.</div>";
    }
}

// Ambil data untuk Tab Gedung
$q_gedung = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, rm.nama_ruangan 
                                    FROM reservasi r 
                                    JOIN users u ON r.user_id = u.id 
                                    JOIN ruangan rm ON r.ruangan_id = rm.id 
                                    ORDER BY r.id DESC");

// Ambil data untuk Tab Kendaraan
$q_mobil = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, k.merk, k.model 
                                   FROM reservasi_kendaraan r 
                                   JOIN users u ON r.user_id = u.id 
                                   JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                                   ORDER BY r.id DESC");

// Cek tab mana yang harus aktif (default: gedung)
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'gedung';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservasi - Super Admin</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/<?php echo $sett['favicon']; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-bottom: 100px; }
        .header-section { background: linear-gradient(135deg, #312e81, #4338ca); color: white; padding: 30px 20px 50px; border-radius: 0 0 30px 30px; margin-bottom: 10px; }
        
        /* Tab Switcher */
        .tab-nav-sa { background: #fff; border-radius: 15px; padding: 5px; display: flex; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .tab-nav-sa button { flex: 1; border: none; background: none; padding: 10px; border-radius: 12px; font-size: 12px; font-weight: 600; color: #6c757d; transition: 0.3s; }
        .tab-nav-sa button.active { background: #312e81; color: #fff; }

        /* Card Styling */
        .res-card { border: none; border-radius: 20px; background: #fff; margin-bottom: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); }
        .status-badge { font-size: 9px; padding: 3px 8px; border-radius: 6px; font-weight: 700; text-transform: uppercase; }
        
        .bg-pending { background: #fef3c7; color: #92400e; }
        .bg-approved { background: #dcfce7; color: #15803d; }
        .bg-rejected { background: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>

    <div class="header-section shadow">
        <div class="container d-flex align-items-center">
            <a href="index.php" class="text-white me-3 fs-4"><i class="bi bi-arrow-left"></i></a>
            <h4 class="fw-bold mb-0">Audit Reservasi</h4>
        </div>
    </div>

    <div class="container mt-4">
        <?php if(isset($msg)) echo $msg; ?>

        <!-- Tab Switcher -->
        <div class="tab-nav-sa shadow-sm">
            <button id="btn-gedung" class="<?php echo ($active_tab == 'gedung') ? 'active' : ''; ?>">Ruangan/GH</button>
            <button id="btn-mobil" class="<?php echo ($active_tab == 'mobil') ? 'active' : ''; ?>">Kendaraan</button>
        </div>

        <!-- SECTION GEDUNG -->
        <div id="section-gedung" style="<?php echo ($active_tab == 'gedung') ? '' : 'display:none;'; ?>">
            <?php while($g = mysqli_fetch_array($q_gedung)){ ?>
                <div class="card res-card">
                    <div class="card-body p-3 text-start">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <span class="badge bg-light text-dark mb-1" style="font-size: 9px;"><?php echo $g['institusi_peminjam']; ?></span>
                                <h6 class="fw-bold mb-0" style="font-size: 14px;"><?php echo $g['nama_ruangan']; ?></h6>
                                <small class="text-muted" style="font-size: 11px;">PIC: <?php echo $g['nama_lengkap']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge bg-<?php echo ($g['status'] == 'pending' ? 'pending' : ($g['status'] == 'disetujui' ? 'approved' : 'rejected')); ?> d-block mb-2">
                                    <?php echo $g['status']; ?>
                                </span>
                                <a href="manage_reservasi.php?delete_id=<?php echo $g['id']; ?>&type=gedung&tab=gedung" class="text-danger" onclick="return confirm('Hapus data reservasi ini?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-calendar-event me-1"></i> <?php echo date('d M Y', strtotime($g['tgl_pinjam'])); ?></small>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- SECTION KENDARAAN -->
        <div id="section-mobil" style="<?php echo ($active_tab == 'mobil') ? '' : 'display:none;'; ?>">
            <?php while($m = mysqli_fetch_array($q_mobil)){ ?>
                <div class="card res-card">
                    <div class="card-body p-3 text-start">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <span class="badge bg-light text-dark mb-1" style="font-size: 9px;"><?php echo $m['institusi_peminjam']; ?></span>
                                <h6 class="fw-bold mb-0" style="font-size: 14px;"><?php echo $m['merk'].' '.$m['model']; ?></h6>
                                <small class="text-muted" style="font-size: 11px;">PIC: <?php echo $m['nama_lengkap']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge bg-<?php echo ($m['status'] == 'pending' ? 'pending' : ($m['status'] == 'disetujui' ? 'approved' : 'rejected')); ?> d-block mb-2">
                                    <?php echo $m['status']; ?>
                                </span>
                                <a href="manage_reservasi.php?delete_id=<?php echo $m['id']; ?>&type=mobil&tab=mobil" class="text-danger" onclick="return confirm('Hapus data reservasi ini?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            </div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-geo-alt me-1"></i> <?php echo $m['tujuan']; ?></small>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btn-gedung').click(function() {
                $(this).addClass('active');
                $('#btn-mobil').removeClass('active');
                $('#section-mobil').hide();
                $('#section-gedung').fadeIn();
            });
            $('#btn-mobil').click(function() {
                $(this).addClass('active');
                $('#btn-gedung').removeClass('active');
                $('#section-gedung').hide();
                $('#section-mobil').fadeIn();
            });
        });
    </script>
</body>
</html>