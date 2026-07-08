<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Hanya Super Admin
if ($_SESSION['role'] != "super_admin") {
    header("location:../login.php");
    exit();
}

// --- 1. KONFIGURASI PENCARIAN & PAGINASI ---
$limit = 10;
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'gedung';
$search = isset($_GET['q']) ? mysqli_real_escape_string($koneksi, $_GET['q']) : '';

// Paginasi Gedung
$page_g = ($active_tab == 'gedung' && isset($_GET['p'])) ? (int)$_GET['p'] : 1;
$offset_g = ($page_g - 1) * $limit;

// Paginasi Mobil
$page_m = ($active_tab == 'mobil' && isset($_GET['p'])) ? (int)$_GET['p'] : 1;
$offset_m = ($page_m - 1) * $limit;

// --- 2. LOGIKA HAPUS DATA ---
if (isset($_GET['delete_id']) && isset($_GET['type'])) {
    $id_h = mysqli_real_escape_string($koneksi, $_GET['delete_id']);
    $type = $_GET['type'];
    $query_del = ($type == 'gedung') ? "DELETE FROM reservasi WHERE id = '$id_h'" : "DELETE FROM reservasi_kendaraan WHERE id = '$id_h'";
    if (mysqli_query($koneksi, $query_del)) {
        header("Location: manage_reservasi.php?tab=$type&pesan=hapus");
        exit();
    }
}

// --- 3. LOGIKA UPDATE (Gedung & Mobil) ---
if (isset($_POST['update_gedung'])) {
    $id = $_POST['id'];
    $inst = $_POST['institusi'];
    $tgl_p = $_POST['tgl_pinjam'];
    $tgl_s = $_POST['tgl_selesai'];
    $keperluan = $_POST['keperluan'];
    $status = $_POST['status'];
    mysqli_query($koneksi, "UPDATE reservasi SET institusi_peminjam='$inst', tgl_pinjam='$tgl_p', tgl_selesai='$tgl_s', keperluan='$keperluan', status='$status' WHERE id='$id'");
    header("Location: manage_reservasi.php?tab=gedung&pesan=update");
    exit();
}
if (isset($_POST['update_mobil'])) {
    $id = $_POST['id'];
    $inst = $_POST['institusi'];
    $tgl_m = $_POST['tgl_mulai'];
    $tgl_s = $_POST['tgl_selesai'];
    $tujuan = $_POST['tujuan'];
    $status = $_POST['status'];
    mysqli_query($koneksi, "UPDATE reservasi_kendaraan SET institusi_peminjam='$inst', tgl_mulai='$tgl_m', tgl_selesai='$tgl_s', tujuan='$tujuan', status='$status' WHERE id='$id'");
    header("Location: manage_reservasi.php?tab=mobil&pesan=update");
    exit();
}

// --- 4. QUERY DATA DENGAN FILTER & LIMIT ---
$where_g = !empty($search) ? "WHERE (u.nama_lengkap LIKE '%$search%' OR r.institusi_peminjam LIKE '%$search%' OR rm.nama_ruangan LIKE '%$search%')" : "";
$total_g = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi r JOIN users u ON r.user_id = u.id LEFT JOIN ruangan rm ON r.ruangan_id = rm.id $where_g"))['total'];
$q_gedung = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, rm.nama_ruangan FROM reservasi r JOIN users u ON r.user_id = u.id LEFT JOIN ruangan rm ON r.ruangan_id = rm.id $where_g ORDER BY r.id DESC LIMIT $offset_g, $limit");

$where_m = !empty($search) ? "WHERE (u.nama_lengkap LIKE '%$search%' OR r.institusi_peminjam LIKE '%$search%' OR k.model LIKE '%$search%')" : "";
$total_m = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM reservasi_kendaraan r JOIN users u ON r.user_id = u.id LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan $where_m"))['total'];
$q_mobil = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, k.merk, k.model FROM reservasi_kendaraan r JOIN users u ON r.user_id = u.id LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan $where_m ORDER BY r.id DESC LIMIT $offset_m, $limit");

// Ambil list institusi untuk Select2
$list_inst = mysqli_query($koneksi, "SELECT DISTINCT institusi_peminjam FROM (SELECT institusi_peminjam FROM reservasi UNION SELECT institusi_peminjam FROM reservasi_kendaraan) as combined");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Reservasi - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
            padding-bottom: 100px;
        }

        .header-section {
            background: linear-gradient(135deg, #312e81, #4338ca);
            color: white;
            padding: 30px 20px 50px;
            border-radius: 0 0 30px 30px;
            margin-bottom: 10px;
        }

        .tab-nav-sa {
            background: #fff;
            border-radius: 15px;
            padding: 5px;
            display: flex;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        .tab-nav-sa button {
            flex: 1;
            border: none;
            background: none;
            padding: 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #6c757d;
            transition: 0.3s;
        }

        .tab-nav-sa button.active {
            background: #312e81;
            color: #fff;
        }

        .res-card {
            border: none;
            border-radius: 20px;
            background: #fff;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
        }

        .status-badge {
            font-size: 9px;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .bg-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .bg-approved {
            background: #dcfce7;
            color: #15803d;
        }

        .bg-rejected {
            background: #fee2e2;
            color: #b91c1c;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 12px;
            height: 45px;
            padding: 8px;
            border: 1px solid #eee;
            background: #f8f9fa;
        }
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
        <?php if (isset($_GET['pesan'])) {
            echo "<div class='alert alert-success border-0 small shadow-sm'>Data berhasil diperbarui.</div>";
        } ?>

        <!-- PENCARIAN CANGGIH -->
        <div class="card border-0 shadow-sm p-3 mb-3" style="border-radius:20px;">
            <form method="GET" action="manage_reservasi.php" id="searchForm">
                <input type="hidden" name="tab" value="<?php echo $active_tab; ?>">
                <div class="row g-2">
                    <div class="col-12">
                        <!-- Kita ubah tampilannya agar Select2 bisa berfungsi layaknya search bar -->
                        <select name="q" id="searchInst" class="form-control" style="width: 100%;">
                            <!-- Opsi Default -->
                            <option value="">Ketik untuk mencari Institusi atau Nama PIC...</option>

                            <!-- Jika sedang melakukan pencarian, pertahankan nilainya -->
                            <?php if (!empty($search)) { ?>
                                <option value="<?php echo htmlspecialchars($search); ?>" selected><?php echo htmlspecialchars($search); ?></option>
                            <?php } ?>

                            <!-- Data dari Database dimasukkan ke sini -->
                            <?php while ($i = mysqli_fetch_array($list_inst)) {
                                // Jangan tampilkan lagi jika sudah ditampilkan di atas (selected)
                                if ($search != $i['institusi_peminjam']) { ?>
                                    <option value="<?php echo htmlspecialchars($i['institusi_peminjam']); ?>"><?php echo htmlspecialchars($i['institusi_peminjam']); ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-sm btn-primary px-4" style="border-radius:10px;">Cari Data</button>
                        <?php if (!empty($search)) { ?>
                            <a href="manage_reservasi.php?tab=<?php echo $active_tab; ?>" class="btn btn-sm btn-outline-danger ms-2" style="border-radius:10px;">Hapus Filter</a>
                        <?php } ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="tab-nav-sa shadow-sm">
            <button id="btn-gedung" class="<?php echo ($active_tab == 'gedung') ? 'active' : ''; ?>">Ruangan/GH</button>
            <button id="btn-mobil" class="<?php echo ($active_tab == 'mobil') ? 'active' : ''; ?>">Kendaraan</button>
        </div>

        <!-- SECTION GEDUNG -->
        <div id="section-gedung" style="<?php echo ($active_tab == 'gedung') ? '' : 'display:none;'; ?>">
            <?php while ($g = mysqli_fetch_array($q_gedung)) { ?>
                <div class="card res-card">
                    <div class="card-body p-3 text-start">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="badge bg-light text-dark mb-1" style="font-size: 9px;"><?php echo $g['institusi_peminjam']; ?></span>
                                <h6 class="fw-bold mb-0"><?php echo ($g['nama_ruangan'] ?? 'Minta: ' . strtoupper($g['tipe_permintaan'])); ?></h6>
                                <small class="text-muted">PIC: <?php echo $g['nama_lengkap']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge bg-<?php echo ($g['status'] == 'pending' ? 'pending' : ($g['status'] == 'disetujui' ? 'approved' : 'rejected')); ?> d-block mb-2"><?php echo $g['status']; ?></span>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#editGedung<?php echo $g['id']; ?>"><i class="bi bi-pencil-square"></i></button>
                                    <a href="manage_reservasi.php?delete_id=<?php echo $g['id']; ?>&type=gedung&tab=gedung" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus permanen?')"><i class="bi bi-trash3"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-calendar-event me-1"></i> <?php echo date('d M Y', strtotime($g['tgl_pinjam'])); ?></small>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT GEDUNG -->
                <div class="modal fade" id="editGedung<?php echo $g['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" class="modal-content" style="border-radius:20px;">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="fw-bold">Edit Reservasi Gedung</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-start">
                                <input type="hidden" name="id" value="<?php echo $g['id']; ?>">
                                <div class="mb-2"><label class="small fw-bold">Institusi</label><input type="text" name="institusi" class="form-control" value="<?php echo $g['institusi_peminjam']; ?>" required></div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="small fw-bold">Tgl Mulai</label><input type="date" name="tgl_pinjam" class="form-control" value="<?php echo $g['tgl_pinjam']; ?>" required></div>
                                    <div class="col-6"><label class="small fw-bold">Tgl Selesai</label><input type="date" name="tgl_selesai" class="form-control" value="<?php echo $g['tgl_selesai']; ?>" required></div>
                                </div>
                                <div class="mb-2"><label class="small fw-bold">Keperluan</label><textarea name="keperluan" class="form-control" rows="2"><?php echo $g['keperluan']; ?></textarea></div>
                                <div class="mb-3"><label class="small fw-bold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" <?php if ($g['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="disetujui" <?php if ($g['status'] == 'disetujui') echo 'selected'; ?>>Disetujui</option>
                                        <option value="ditolak" <?php if ($g['status'] == 'ditolak') echo 'selected'; ?>>Ditolak</option>
                                        <option value="dibatalkan" <?php if ($g['status'] == 'dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
                                        <option value="selesai" <?php if ($g['status'] == 'selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_gedung" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius:12px; background:#312e81;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>

            <?php renderPagination($total_g, $limit, $page_g, 'gedung', $search); ?>
        </div>

        <!-- SECTION KENDARAAN -->
        <div id="section-mobil" style="<?php echo ($active_tab == 'mobil') ? '' : 'display:none;'; ?>">
            <?php while ($m = mysqli_fetch_array($q_mobil)) { ?>
                <div class="card res-card">
                    <div class="card-body p-3 text-start">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <span class="badge bg-light text-dark mb-1" style="font-size: 9px;"><?php echo $m['institusi_peminjam']; ?></span>
                                <h6 class="fw-bold mb-0"><?php echo ($m['merk'] ? $m['merk'] . ' ' . $m['model'] : 'Minta: ' . strtoupper($m['jenis_permintaan'])); ?></h6>
                                <small class="text-muted">PIC: <?php echo $m['nama_lengkap']; ?></small>
                            </div>
                            <div class="text-end">
                                <span class="status-badge bg-<?php echo ($m['status'] == 'pending' ? 'pending' : ($m['status'] == 'disetujui' ? 'approved' : 'rejected')); ?> d-block mb-2"><?php echo $m['status']; ?></span>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal" data-bs-target="#editMobil<?php echo $m['id']; ?>"><i class="bi bi-pencil-square"></i></button>
                                    <a href="manage_reservasi.php?delete_id=<?php echo $m['id']; ?>&type=mobil&tab=mobil" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Hapus permanen?')"><i class="bi bi-trash3"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="border-top pt-2 mt-2">
                            <small class="text-muted" style="font-size: 10px;"><i class="bi bi-geo-alt me-1"></i> <?php echo $m['tujuan']; ?></small>
                        </div>
                    </div>
                </div>

                <!-- MODAL EDIT MOBIL -->
                <div class="modal fade" id="editMobil<?php echo $m['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form method="POST" class="modal-content" style="border-radius:20px;">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="fw-bold">Edit Reservasi Kendaraan</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-start">
                                <input type="hidden" name="id" value="<?php echo $m['id']; ?>">
                                <div class="mb-2"><label class="small fw-bold">Institusi</label><input type="text" name="institusi" class="form-control" value="<?php echo $m['institusi_peminjam']; ?>" required></div>
                                <div class="row g-2 mb-2">
                                    <div class="col-6"><label class="small fw-bold">Waktu Mulai</label><input type="datetime-local" name="tgl_mulai" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($m['tgl_mulai'])); ?>" required></div>
                                    <div class="col-6"><label class="small fw-bold">Waktu Selesai</label><input type="datetime-local" name="tgl_selesai" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($m['tgl_selesai'])); ?>" required></div>
                                </div>
                                <div class="mb-2"><label class="small fw-bold">Tujuan</label><input type="text" name="tujuan" class="form-control" value="<?php echo $m['tujuan']; ?>" required></div>
                                <div class="mb-3"><label class="small fw-bold">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" <?php if ($m['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="disetujui" <?php if ($m['status'] == 'disetujui') echo 'selected'; ?>>Disetujui</option>
                                        <option value="ditolak" <?php if ($m['status'] == 'ditolak') echo 'selected'; ?>>Ditolak</option>
                                        <option value="dibatalkan" <?php if ($m['status'] == 'dibatalkan') echo 'selected'; ?>>Dibatalkan</option>
                                        <option value="selesai" <?php if ($m['status'] == 'selesai') echo 'selected'; ?>>Selesai</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_mobil" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius:12px; background:#312e81;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>

            <?php renderPagination($total_m, $limit, $page_m, 'mobil', $search); ?>
        </div>
    </div>

    <?php include 'navbar.php'; ?>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Konfigurasi Select2 yang lebih rapi
            $('#searchInst').select2({
                placeholder: "Ketik institusi atau PIC...",
                allowClear: true,
                tags: true, // Memungkinkan Admin mengetik nama yang tidak ada di list
                minimumInputLength: 1, // BARU MUNCUL SARAN JIKA SUDAH KETIK 1 HURUF
                language: {
                    inputTooShort: function() {
                        return 'Ketik minimal 1 huruf untuk mencari...';
                    },
                    noResults: function() {
                        return 'Tidak ditemukan data yang cocok';
                    }
                }
            });

            // Tab Logic (Pastikan ini tetap ada)
            $('#btn-gedung').click(function() {
                location.href = 'manage_reservasi.php?tab=gedung&q=<?php echo urlencode($search); ?>';
            });
            $('#btn-mobil').click(function() {
                location.href = 'manage_reservasi.php?tab=mobil&q=<?php echo urlencode($search); ?>';
            });
        });
    </script>
</body>

</html>

<?php
// FUNGSI PAGINASI 5 TOMBOL
function renderPagination($total_data, $limit, $current_page, $tab, $search)
{
    $total_pages = ceil($total_data / $limit);
    if ($total_pages <= 1) return;

    echo '<nav><ul class="pagination pagination-sm justify-content-center">';

    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    if ($current_page > 1) echo "<li class='page-item'><a class='page-link shadow-sm' href='?tab=$tab&p=" . ($current_page - 1) . "&q=$search'>&laquo;</a></li>";

    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? 'active' : '';
        echo "<li class='page-item $active'><a class='page-link shadow-sm' href='?tab=$tab&p=$i&q=$search'>$i</a></li>";
    }

    if ($current_page < $total_pages) echo "<li class='page-item'><a class='page-link shadow-sm' href='?tab=$tab&p=" . ($current_page + 1) . "&q=$search'>&raquo;</a></li>";

    echo '</ul></nav>';
}
?>