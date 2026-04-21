<?php
session_start();
include '../config/koneksi.php';
if($_SESSION['role'] != "admin") { header("location:../login.php"); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Persetujuan Booking - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Daftar Pengajuan Reservasi</h3>
            <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Unit / Pemohon</th>
                            <th>Ruangan</th>
                            <th>Waktu Penggunaan</th>
                            <th>Keperluan / Tamu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        // Query JOIN untuk mengambil data lengkap
                        $query = "SELECT reservasi.*, users.nama_lengkap, users.unit, ruangan.nama_ruangan, ruangan.tipe 
                                  FROM reservasi 
                                  JOIN users ON reservasi.user_id = users.id 
                                  JOIN ruangan ON reservasi.ruangan_id = ruangan.id 
                                  ORDER BY reservasi.id DESC";
                        
                        $data = mysqli_query($koneksi, $query);
                        while($d = mysqli_fetch_array($data)){
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo $d['unit']; ?></strong><br>
                                <small class="text-muted"><?php echo $d['nama_lengkap']; ?></small>
                            </td>
                            <td>
                                <?php echo $d['nama_ruangan']; ?><br>
                                <span class="badge bg-secondary"><?php echo str_replace('_', ' ', $d['tipe']); ?></span>
                            </td>
                            <td>
                                <?php 
                                    if($d['tipe'] == 'guest_house'){
                                        echo date('d M Y', strtotime($d['tgl_pinjam'])) . " s/d " . date('d M Y', strtotime($d['tgl_selesai']));
                                    } else {
                                        echo date('d M Y', strtotime($d['tgl_pinjam'])) . "<br>";
                                        echo "<small>" . substr($d['jam_mulai'],0,5) . " - " . substr($d['jam_selesai'],0,5) . " WIB</small>";
                                    }
                                ?>
                            </td>
                            <td><?php echo $d['keperluan']; ?></td>
                            <td>
                                <?php 
                                if($d['status'] == 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                                elseif($d['status'] == 'disetujui') echo '<span class="badge bg-success">Disetujui</span>';
                                else echo '<span class="badge bg-danger">Ditolak</span>';
                                ?>
                            </td>
                            <td>
                                <?php if($d['status'] == 'pending'){ ?>
                                    <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=disetujui" class="btn btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini?')">Setujui</a>
                                    <a href="persetujuan_aksi.php?id=<?php echo $d['id']; ?>&status=ditolak" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">Tolak</a>
                                <?php } else { ?>
                                    <span class="text-muted small">Selesai</span>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>