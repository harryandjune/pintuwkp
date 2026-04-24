<?php
session_start();
include '../config/koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    header("location:../login.php");
    exit();
}

$user_id    = $_SESSION['id_user'];
$nama_user  = $_SESSION['nama'];
$ruangan_id = mysqli_real_escape_string($koneksi, $_POST['ruangan_id']);
$tipe       = mysqli_real_escape_string($koneksi, $_POST['tipe']);
$tgl_mulai  = mysqli_real_escape_string($koneksi, $_POST['tgl_pinjam']);
$tgl_selesai = mysqli_real_escape_string($koneksi, $_POST['tgl_selesai']);
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$jml_orang  = mysqli_real_escape_string($koneksi, $_POST['jumlah_orang']);
$institusi_peminjam = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);

// --- PENYESUAIAN JAM ---
if ($tipe == 'meeting_room') {
    $jam_mulai   = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
} else {
    // Untuk Guest House, kita anggap durasi full hari
    $jam_mulai   = '00:00:01';
    $jam_selesai = '23:59:59';
}

// Ambil Nama Ruangan untuk keperluan pesan WhatsApp
$q_room = mysqli_query($koneksi, "SELECT nama_ruangan FROM ruangan WHERE id='$ruangan_id'");
$data_room = mysqli_fetch_assoc($q_room);
$nama_ruangan = $data_room['nama_ruangan'];

// --- LOGIKA PENGECEKAN BENTROK (COLLISION DETECTION) ---

if ($tipe == 'guest_house') {
    // Logika Guest House: Cek tumpang tindih tanggal
    // Rumus: (Mulai_A < Selesai_B) DAN (Selesai_A > Mulai_B)
    $query_cek = "SELECT * FROM reservasi 
                  WHERE ruangan_id = '$ruangan_id' 
                  AND status = 'disetujui'
                  AND (tgl_pinjam < '$tgl_selesai' AND tgl_selesai > '$tgl_mulai')";
} else {
    // Logika Meeting Room: Cek tumpang tindih Jam pada Tanggal yang sama
    // Rumus: (Tgl Sama) DAN (JamMulai_A < JamSelesai_B) DAN (JamSelesai_A > JamMulai_B)
    $query_cek = "SELECT * FROM reservasi 
                  WHERE ruangan_id = '$ruangan_id' 
                  AND status = 'disetujui'
                  AND tgl_pinjam = '$tgl_mulai'
                  AND (jam_mulai < '$jam_selesai' AND jam_selesai > '$jam_mulai')";
}

$cek = mysqli_query($koneksi, $query_cek);

if (mysqli_num_rows($cek) > 0) {
    // Jika ditemukan jadwal yang bertabrakan
    echo "<script>alert('Maaf, Ruangan sudah terisi pada waktu tersebut (sudah disetujui untuk unit lain). Silakan pilih waktu atau ruangan lain.'); window.history.back();</script>";
} else {
    // --- SIMPAN DATA KE DATABASE ---
    $sql = "INSERT INTO reservasi (user_id, institusi_peminjam, ruangan_id, tgl_pinjam, tgl_selesai, jam_mulai, jam_selesai, keperluan, jumlah_orang, status) 
        VALUES ('$user_id', '$institusi_peminjam', '$ruangan_id', '$tgl_mulai', '$tgl_selesai', '$jam_mulai', '$jam_selesai', '$keperluan', '$jml_orang', 'pending')";

    if (mysqli_query($koneksi, $sql)) {
        // 1. Format Nomor WA Admin
        $wa_admin = preg_replace('/[^0-9]/', '', $sett['kontak_admin'] ?? '');
        if (substr($wa_admin, 0, 1) === '0') {
            $wa_admin = '62' . substr($wa_admin, 1);
        } elseif (substr($wa_admin, 0, 1) === '8') {
            $wa_admin = '62' . $wa_admin;
        }

        // 2. Susun Pesan
        $waktu_booking = ($tipe == 'guest_house')
            ? date('d/m/Y', strtotime($tgl_mulai)) . " s.d " . date('d/m/Y', strtotime($tgl_selesai))
            : date('d/m/Y', strtotime($tgl_mulai)) . " (" . substr($jam_mulai, 0, 5) . " - " . substr($jam_selesai, 0, 5) . ")";

        $pesan = "*NOTIFIKASI BOOKING BARU*\n";
        $pesan .= "--------------------------\n";
        $pesan .= "Pemohon: " . $nama_user . "\n";
        $pesan .= "Institusi: *" . $institusi_peminjam . "*\n"; // Tambahan ini
        $pesan .= "Ruangan: *" . $nama_ruangan . "*\n";
        $pesan .= "Waktu: " . $waktu_booking . "\n";
        $pesan .= "--------------------------\n";
        $pesan .= "Mohon segera diproses. Terima kasih.";

        $url_wa = "https://wa.me/" . $wa_admin . "?text=" . urlencode($pesan);
?>

        <!-- Tampilan Transisi Modern -->
        <!DOCTYPE html>
        <html lang="id">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Berhasil</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background: #f0f2f5;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                }

                .success-card {
                    background: white;
                    padding: 30px;
                    border-radius: 25px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 400px;
                }
            </style>
        </head>

        <body>
            <div class="success-card">
                <div class="mb-3 text-success">
                    <i class="bi bi-check-circle-fill" style="font-size: 60px;"></i>
                </div>
                <h4 class="fw-bold">Booking Terkirim!</h4>
                <p class="text-muted small">Pesanan Anda sudah masuk ke sistem. Silakan klik tombol di bawah untuk memberitahu Admin via WhatsApp.</p>

                <a href="<?php echo $url_wa; ?>" class="btn btn-success w-100 py-3 mb-2 fw-bold" style="border-radius: 15px;">
                    <i class="bi bi-whatsapp me-2"></i> Kirim Notif Ke Admin
                </a>

                <a href="riwayat.php" class="btn btn-link text-muted text-decoration-none small">Nanti Saja, Lihat Riwayat</a>
            </div>
        </body>

        </html>

<?php
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>