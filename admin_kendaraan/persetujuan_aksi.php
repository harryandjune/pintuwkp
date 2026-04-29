<?php 
include '../config/koneksi.php';
session_start();

// 1. Cek keamanan akses
if($_SESSION['role'] != "admin_kendaraan") { 
    header("location:../login.php"); 
    exit();
}

// 2. Ambil data dari URL
if(isset($_GET['id']) && isset($_GET['status'])){
    $id     = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);

    if($status == 'disetujui'){
        // Pastikan kendaraan_id juga dikirim
        if(!isset($_GET['kendaraan_id']) || empty($_GET['kendaraan_id'])){
             echo "<script>alert('ID Kendaraan tidak terpilih!'); window.history.back();</script>";
             exit();
        }
        $kendaraan_id = mysqli_real_escape_string($koneksi, $_GET['kendaraan_id']);
        
        // Update status dan pasangkan dengan armada pilihan admin
        $query = "UPDATE reservasi_kendaraan SET status = 'disetujui', kendaraan_id = '$kendaraan_id' WHERE id = '$id'";
        $eksekusi = mysqli_query($koneksi, $query);

        if($eksekusi){
            // Ambil data lengkap untuk notifikasi WA (JOIN ke kendaraan dan users)
            $q_info = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, u.no_wa, k.merk, k.model, k.nomor_plat 
                                              FROM reservasi_kendaraan r 
                                              JOIN users u ON r.user_id = u.id 
                                              JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                                              WHERE r.id = '$id'");
            $d = mysqli_fetch_array($q_info);

            // Normalisasi nomor WA
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (!empty($phone)) {
                if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                elseif (substr($phone, 0, 1) === '8') { $phone = '62' . $phone; }
            }

            // Susun pesan WA
            $pesan = "Halo *" . $d['nama_lengkap'] . "*,\n\n";
            $pesan .= "Pengajuan mobil Anda di *" . ($sett['nama_sistem'] ?? 'PINTU WKP') . "* telah *DISETUJUI*.\n\n";
            $pesan .= "*Unit Armada:* \n";
            $pesan .= "• Mobil: " . $d['merk'] . " " . $d['model'] . "\n";
            $pesan .= "• Plat: " . $d['nomor_plat'] . "\n";
            $pesan .= "• Waktu: " . date('d/m/Y H:i', strtotime($d['tgl_mulai'])) . "\n\n";
            $pesan .= "Silakan hubungi Admin Transportasi untuk pengambilan kunci. Terima kasih.";

            $url_wa = "https://wa.me/" . $phone . "?text=" . urlencode($pesan);
            
            // TAMPILKAN HALAMAN SUKSES (Bukan blank)
            tampilkan_sukses($status, $url_wa);

        } else {
            echo "Error Database: " . mysqli_error($koneksi);
        }

    } elseif ($status == 'ditolak') {
        // Logika jika ditolak
        mysqli_query($koneksi, "UPDATE reservasi_kendaraan SET status = 'ditolak' WHERE id = '$id'");
        header("location:persetujuan.php?pesan=ditolak");
        exit();
    }
} else {
    echo "ID atau Status tidak valid.";
}

// Fungsi sederhana untuk menampilkan UI sukses agar tidak blank
function tampilkan_sukses($status, $url_wa) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status Diperbarui</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body { font-family: sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .status-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); text-align: center; max-width: 400px; width: 90%; }
        </style>
    </head>
    <body>
        <div class="status-card">
            <div class="mb-3 text-success">
                <i class="bi bi-check-circle-fill" style="font-size: 60px;"></i>
            </div>
            <h4 class="fw-bold">Berhasil Disetujui</h4>
            <p class="text-muted small">Armada telah dialokasikan. Silakan klik tombol di bawah untuk mengabari User.</p>
            <a href="<?php echo $url_wa; ?>" target="_blank" class="btn btn-success w-100 py-3 mb-2 fw-bold" style="border-radius: 15px;">
                <i class="bi bi-whatsapp me-2"></i> Kabari User via WA
            </a>
            <a href="persetujuan.php" class="btn btn-link text-muted text-decoration-none small">Kembali ke Daftar</a>
        </div>
    </body>
    </html>
    <?php
}
?>