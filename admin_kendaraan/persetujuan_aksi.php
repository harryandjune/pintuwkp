<?php 
include '../config/koneksi.php';
session_start();

if($_SESSION['role'] != "admin_kendaraan") { exit(); }

$id     = mysqli_real_escape_string($koneksi, $_GET['id']);
$status = mysqli_real_escape_string($koneksi, $_GET['status']);

if($status == 'disetujui' || $status == 'ditolak'){
    
    // Ambil info detail untuk notifikasi WA
    $q = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, u.no_wa, k.merk, k.model 
                                 FROM reservasi_kendaraan r 
                                 JOIN users u ON r.user_id = u.id 
                                 JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                                 WHERE r.id = '$id'");
    $d = mysqli_fetch_array($q);

    // Update status
    $query = "UPDATE reservasi_kendaraan SET status = '$status' WHERE id = '$id'";
    
    if(mysqli_query($koneksi, $query)){
        // Format WA User
        $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
        if(substr($phone, 0, 1) === '0') $phone = '62' . substr($phone, 1);
        elseif(substr($phone, 0, 1) === '8') $phone = '62' . $phone;

        // Pesan WA
        $hasil = ($status == 'disetujui') ? "*DISETUJUI*" : "*DITOLAK*";
        $pesan = "Halo *" . $d['nama_lengkap'] . "*,\n\n";
        $pesan .= "Pengajuan peminjaman mobil Anda di *" . $sett['nama_sistem'] . "* telah $hasil.\n\n";
        $pesan .= "*Detail:* \n";
        $pesan .= "• Mobil: " . $d['merk'] . " " . $d['model'] . "\n";
        $pesan .= "• Tujuan: " . $d['tujuan'] . "\n";
        $pesan .= "• Waktu: " . date('d/m/Y H:i', strtotime($d['tgl_mulai'])) . "\n\n";
        
        if($status == 'disetujui'){
            $pesan .= "Silakan koordinasi dengan Bagian Transportasi terkait kunci/sopir. Terima kasih.";
        } else {
            $pesan .= "Mohon maaf atas ketidaknyamanannya. Silakan hubungi Admin Transport untuk info lebih lanjut.";
        }

        $url_wa = "https://wa.me/" . $phone . "?text=" . urlencode($pesan);
        ?>

        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Berhasil</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
            <style>
                body { font-family: sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; }
                .success-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center; max-width: 400px; }
            </style>
        </head>
        <body>
            <div class="success-card">
                <div class="mb-3 <?php echo ($status == 'disetujui' ? 'text-success' : 'text-danger'); ?>">
                    <i class="bi <?php echo ($status == 'disetujui' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'); ?>" style="font-size: 60px;"></i>
                </div>
                <h4 class="fw-bold">Status Berhasil Diubah</h4>
                <p class="text-muted small">Status pengajuan menjadi <b><?php echo strtoupper($status); ?></b>. Silakan kabari User via WhatsApp.</p>
                
                <a href="<?php echo $url_wa; ?>" class="btn <?php echo ($status == 'disetujui' ? 'btn-success' : 'btn-danger'); ?> w-100 py-3 mb-2 fw-bold" style="border-radius: 15px;">
                    <i class="bi bi-whatsapp me-2"></i> Kirim Kabar Ke User
                </a>
                <a href="persetujuan.php" class="btn btn-link text-muted text-decoration-none small">Kembali ke Daftar</a>
            </div>
        </body>
        </html>

        <?php
        exit();
    }
}
?>