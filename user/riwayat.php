<?php
session_start();
include '../config/koneksi.php';

// Pastikan yang akses adalah user
if($_SESSION['role'] != "user") { 
    header("location:../login.php"); 
}

$user_id = $_SESSION['id_user'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Booking - PINTU WKP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">PINTU WKP</a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Daftar Ruangan</a>
                <a class="nav-link active" href="riwayat.php">Riwayat Booking</a>
                <a class="nav-link text-danger" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Riwayat Permohonan Ruangan</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Ruangan</th>
                            <th>Tanggal / Waktu</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        // Mengambil data reservasi milik user yang sedang login saja
                        $query = "SELECT reservasi.*, ruangan.nama_ruangan, ruangan.tipe 
                                  FROM reservasi 
                                  JOIN ruangan ON reservasi.ruangan_id = ruangan.id 
                                  WHERE reservasi.user_id = '$user_id' 
                                  ORDER BY reservasi.id DESC";
                        
                        $data = mysqli_query($koneksi, $query);
                        
                        if(mysqli_num_rows($data) == 0){
                            echo "<tr><td colspan='5' class='text-center text-muted'>Belum ada riwayat booking.</td></tr>";
                        }

                        while($d = mysqli_fetch_array($data)){
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <strong><?php echo $d['nama_ruangan']; ?></strong><br>
                                <small class="badge bg-secondary"><?php echo str_replace('_', ' ', $d['tipe']); ?></small>
                            </td>
                            <td>
                                <?php 
                                    if($d['tipe'] == 'guest_house'){
                                        // Tampilan untuk Guest House (Hanya Tanggal)
                                        echo date('d/m/Y', strtotime($d['tgl_pinjam'])) . " s/d " . date('d/m/Y', strtotime($d['tgl_selesai']));
                                    } else {
                                        // Tampilan untuk Meeting Room (Tanggal & Jam)
                                        echo date('d/m/Y', strtotime($d['tgl_pinjam'])) . "<br>";
                                        echo "<small class='text-primary'>" . substr($d['jam_mulai'],0,5) . " - " . substr($d['jam_selesai'],0,5) . " WIB</small>";
                                    }
                                ?>
                            </td>
                            <td><?php echo $d['keperluan']; ?></td>
                            <td>
                                <?php 
                                if($d['status'] == 'pending'){
                                    echo '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>';
                                } elseif($d['status'] == 'disetujui'){
                                    echo '<span class="badge bg-success">Disetujui</span>';
                                } else {
                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-3">
            <p class="small text-muted">* Jika status masih <b>Pending</b>, silakan hubungi Admin Kantor WKP untuk konfirmasi lebih lanjut.</p>
        </div>
    </div>
</body>
</html>