<?php 
include '../config/koneksi.php';
session_start();

// 1. Cek keamanan akses
if ($_SESSION['role'] != "admin_kendaraan") {
    header("location:../login.php");
    exit();
}

// 2. Ambil data dari URL
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id     = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);

    // --- LOGIKA UPDATE DATABASE ---
    if ($status == 'disetujui') {
        if (!isset($_GET['kendaraan_id']) || empty($_GET['kendaraan_id'])) {
            echo "<script>alert('ID Kendaraan tidak terpilih!'); window.history.back();</script>";
            exit();
        }
        $kendaraan_id = mysqli_real_escape_string($koneksi, $_GET['kendaraan_id']);
        $query = "UPDATE reservasi_kendaraan SET status = 'disetujui', kendaraan_id = '$kendaraan_id' WHERE id = '$id'";
    } else {
        $query = "UPDATE reservasi_kendaraan SET status = 'ditolak' WHERE id = '$id'";
    }

    $eksekusi = mysqli_query($koneksi, $query);

    if ($eksekusi) {
        // 3. Ambil data lengkap (Gunakan LEFT JOIN agar data tetap muncul meski kendaraan_id NULL saat ditolak)
        $q_info = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, u.no_wa, k.merk, k.model, k.nomor_plat 
                                          FROM reservasi_kendaraan r 
                                          JOIN users u ON r.user_id = u.id 
                                          LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
                                          WHERE r.id = '$id'");
        $d = mysqli_fetch_array($q_info);

        // 4. Normalisasi nomor WA User
        $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
        if (!empty($phone)) {
            if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
            elseif (substr($phone, 0, 1) === '8') { $phone = '62' . $phone; }
        }

        // 5. Format Waktu (Tanggal dan Jam)
        $waktu_mulai   = date('d/m/Y (H:i)', strtotime($d['tgl_mulai']));
        $waktu_selesai = date('d/m/Y (H:i)', strtotime($d['tgl_selesai']));

        // 6. Susun Pesan WA Dinamis
        $nama_sistem = $sett['nama_sistem'] ?? 'PINTU WKP';
        $pesan = "Assalamualaikum, Ustadz *" . $d['nama_lengkap'] . "*,\n\n";

        if ($status == 'disetujui') {
            $pesan .= "Kabar baik! Pengajuan mobil Anda di *" . $nama_sistem . "* telah *DISETUJUI*.\n\n";
            $pesan .= "*Unit Armada:* \n";
            $pesan .= "• Mobil: " . $d['merk'] . " " . $d['model'] . "\n";
            $pesan .= "• Plat: " . $d['nomor_plat'] . "\n";
        } else {
            $pesan .= "Mohon maaf, pengajuan mobil Anda di *" . $nama_sistem . "* saat ini *DITOLAK* / Belum dapat disetujui.\n\n";
            $pesan .= "*Detail Permintaan:* \n";
            $pesan .= "• Jenis: " . $d['jenis_permintaan'] . "\n";
        }

        // Informasi Waktu, Tujuan, dan Keperluan (Muncul di Setuju maupun Tolak)
        $pesan .= "*Detail Jadwal:* \n";
        $pesan .= "• Mulai: " . $waktu_mulai . " WITA\n";
        $pesan .= "• Selesai: " . $waktu_selesai . " WITA\n";
        $pesan .= "• Tujuan: " . $d['tujuan'] . "\n";
        $pesan .= "• Keperluan: " . $d['keperluan'] . "\n\n";
        
        if ($status == 'disetujui') {
            $pesan .= "Silakan hubungi Admin Transportasi untuk pengambilan kunci. Terima kasih.";
        } else {
            $pesan .= "Silakan hubungi Admin Transportasi untuk informasi lebih lanjut. Terima kasih.";
        }

        $url_wa = "https://wa.me/" . $phone . "?text=" . urlencode($pesan);

        // 7. TAMPILKAN HALAMAN SUKSES
        tampilkan_sukses($status, $url_wa);

    } else {
        echo "Error Database: " . mysqli_error($koneksi);
    }
} else {
    echo "ID atau Status tidak valid.";
}

// Fungsi tampilan UI sukses
function tampilkan_sukses($status, $url_wa)
{
    $color = ($status == 'disetujui') ? 'success' : 'danger';
    $icon  = ($status == 'disetujui') ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    $title = ($status == 'disetujui') ? 'Berhasil Disetujui' : 'Berhasil Ditolak';
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
            body { font-family: 'Poppins', sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
            .status-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5); text-align: center; max-width: 450px; width: 90%; }
        </style>
    </head>
    <body>
        <div class="status-card">
            <div class="mb-3 text-<?php echo $color; ?>">
                <i class="bi <?php echo $icon; ?>" style="font-size: 60px;"></i>
            </div>
            <h4 class="fw-bold"><?php echo $title; ?></h4>
            <p class="text-muted small">Status telah diperbarui. Pesan WhatsApp sudah menyertakan detail waktu mulai hingga selesai secara lengkap.</p>
            <a href="<?php echo $url_wa; ?>" target="_blank" class="btn btn-<?php echo $color; ?> w-100 py-3 mb-2 fw-bold" style="border-radius: 15px;">
                <i class="bi bi-whatsapp me-2"></i> Kirim Kabar Ke User
            </a>
            <a href="persetujuan.php" class="btn btn-link text-muted text-decoration-none small">Kembali ke Daftar</a>
        </div>
    </body>
    </html>
<?php
}
?>