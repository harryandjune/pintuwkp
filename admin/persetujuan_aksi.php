<?php 
include '../config/koneksi.php';
session_start();

// 1. Cek keamanan akses
if($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
    exit();
}

// 2. Ambil data dan pastikan tidak kosong
if(isset($_GET['id']) && isset($_GET['status'])){
    $id     = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);

    if($status == 'disetujui' || $status == 'ditolak'){
        
        // JIKA DISETUJUI, UPDATE STATUS DAN RUANGAN_ID
        if($status == 'disetujui'){
            $ruangan_id = mysqli_real_escape_string($koneksi, $_GET['ruangan_id']);
            $query = "UPDATE reservasi SET status = 'disetujui', ruangan_id = '$ruangan_id' WHERE id = '$id'";
        } else {
            // JIKA DITOLAK, HANYA UPDATE STATUS
            $query = "UPDATE reservasi SET status = 'ditolak' WHERE id = '$id'";
        }

        $eksekusi = mysqli_query($koneksi, $query);

        if($eksekusi){
            // Ambil data User & Ruangan (JOIN) untuk keperluan Notifikasi WhatsApp
            // Gunakan LEFT JOIN ke ruangan karena jika ditolak ruangan_id mungkin masih NULL
            $q_info = mysqli_query($koneksi, "SELECT r.*, u.nama_lengkap, u.no_wa, rm.nama_ruangan 
                                              FROM reservasi r 
                                              JOIN users u ON r.user_id = u.id 
                                              LEFT JOIN ruangan rm ON r.ruangan_id = rm.id 
                                              WHERE r.id = '$id'");
            $d = mysqli_fetch_array($q_info);

            // --- LOGIKA NOTIFIKASI KE USER ---
            
            // Format nomor WA User
            $phone = preg_replace('/[^0-9]/', '', $d['no_wa'] ?? '');
            if (!empty($phone)) {
                if (substr($phone, 0, 1) === '0') { $phone = '62' . substr($phone, 1); }
                elseif (substr($phone, 0, 1) === '8') { $phone = '62' . $phone; }
            }

            // Susun Pesan Berdasarkan Status
            $waktu = date('d/m/Y', strtotime($d['tgl_pinjam']));
            if($d['tipe_permintaan'] == 'meeting_room'){
                $waktu .= " (" . substr($d['jam_mulai'],0,5) . " - " . substr($d['jam_selesai'],0,5) . ")";
            }

            $nama_sistem = $sett['nama_sistem'] ?? 'PINTU WKP';

            if($status == 'disetujui'){
                $pesan = "Assalamualaikum, *" . $d['nama_lengkap'] . "*,\n\n";
                $pesan .= "Kabar baik! Pengajuan ruangan Anda di *" . $nama_sistem . "* telah *DISETUJUI*.\n\n";
                $pesan .= "*Detail Alokasi:* \n";
                $pesan .= "• Ruangan: " . ($d['nama_ruangan'] ?? '-') . "\n";
                $pesan .= "• Waktu: " . $waktu . "\n\n";
                $pesan .= "Silakan gunakan ruangan sesuai jadwal. Terima kasih.";
            } else {
                $pesan = "Assalamualaikum, *" . $d['nama_lengkap'] . "*,\n\n";
                $pesan .= "Mohon maaf, pengajuan ruangan Anda di *" . $nama_sistem . "* saat ini *DITOLAK* / Belum dapat disetujui.\n\n";
                $pesan .= "*Detail:* \n";
                $pesan .= "• Layanan: " . strtoupper(str_replace('_',' ',$d['tipe_permintaan'])) . "\n";
                $pesan .= "• Waktu: " . $waktu . "\n\n";
                $pesan .= "Silakan hubungi Admin untuk info lebih lanjut atau pilih jadwal lain.";
            }

            $url_wa = "https://wa.me/" . $phone . "?text=" . urlencode($pesan);
            ?>

            <!-- Tampilan Transisi Admin -->
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Status Diperbarui</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
                <style>
                    body { font-family: 'Poppins', sans-serif; background: #1e293b; display: flex; align-items: center; justify-content: center; height: 100vh; color: white; margin: 0;}
                    .status-card { background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); text-align: center; max-width: 400px; color: #333; width: 90%;}
                </style>
            </head>
            <body>
                <div class="status-card">
                    <div class="mb-3 <?php echo ($status == 'disetujui' ? 'text-success' : 'text-danger'); ?>">
                        <i class="bi <?php echo ($status == 'disetujui' ? 'bi-check-circle-fill' : 'bi-x-circle-fill'); ?>" style="font-size: 60px;"></i>
                    </div>
                    <h4 class="fw-bold">Status Berhasil Diubah!</h4>
                    <p class="text-muted small">Pengajuan sudah berstatus <b><?php echo strtoupper($status); ?></b>. Silakan beri tahu pemohon via WhatsApp.</p>
                    
                    <a href="<?php echo $url_wa; ?>" target="_blank" class="btn <?php echo ($status == 'disetujui' ? 'btn-success' : 'btn-danger'); ?> w-100 py-3 mb-2 fw-bold" style="border-radius: 15px;">
                        <i class="bi bi-whatsapp me-2"></i> Kirim Kabar Ke User
                    </a>
                    
                    <a href="persetujuan.php" class="btn btn-link text-muted text-decoration-none small">Kembali ke Daftar</a>
                </div>
            </body>
            </html>
            <?php
            exit();

        } else {
            echo "Gagal update database: " . mysqli_error($koneksi);
        }
    } else {
        echo "Status tidak valid!";
    }
} else {
    echo "ID atau Status tidak ditemukan!";
}
?>