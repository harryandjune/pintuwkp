<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "user") { exit(); }

$user_id    = $_SESSION['id_user'];
$nama_user  = $_SESSION['nama'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_pinjam'];
$tgl_selesai= $_POST['tgl_selesai'];
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$jml_orang  = $_POST['jumlah_orang'];
$tipe       = $_POST['tipe_permintaan']; // 'guest_house' atau 'meeting_room'

// Logika pengambilan jam
$jam_mulai   = ($tipe == 'meeting_room') ? $_POST['jam_mulai'] : '00:00:00';
$jam_selesai = ($tipe == 'meeting_room') ? $_POST['jam_selesai'] : '23:59:59';

// SIMPAN DATA (ruangan_id diset NULL karena admin yang akan menentukan)
$sql = "INSERT INTO reservasi (user_id, tipe_permintaan, institusi_peminjam, ruangan_id, tgl_pinjam, tgl_selesai, jam_mulai, jam_selesai, keperluan, jumlah_orang, status) 
        VALUES ('$user_id', '$tipe', '$institusi', NULL, '$tgl_mulai', '$tgl_selesai', '$jam_mulai', '$jam_selesai', '$keperluan', '$jml_orang', 'pending')";

if (mysqli_query($koneksi, $sql)) {
    // 1. Format Nomor WA Admin
    $wa_admin = preg_replace('/[^0-9]/', '', $sett['kontak_admin'] ?? '');
    if (substr($wa_admin, 0, 1) === '0') $wa_admin = '62' . substr($wa_admin, 1);

    // 2. KONDISI PESAN WAKTU (DINAMIS)
    $tgl_f = date('d/m/Y', strtotime($tgl_mulai));
    
    if ($tipe == 'meeting_room') {
        // Jika Meeting Room, tampilkan Tanggal dan Jam (Mulai - Selesai)
        $waktu_info = $tgl_f . " (Pukul " . substr($jam_mulai, 0, 5) . " - " . substr($jam_selesai, 0, 5) . " WIB)";
    } else {
        // Jika Guest House, tampilkan Rentang Tanggal
        $waktu_info = $tgl_f . " s.d " . date('d/m/Y', strtotime($tgl_selesai));
    }

    // 3. Susun Pesan WA (Termasuk Jumlah Peserta)
    $pesan = "*PENGAJUAN PENGGUNAAN LAYANAN*\n";
    $pesan .= "------------------\n";
    $pesan .= "Pemohon: *$nama_user*\n";
    $pesan .= "Unit/Instansi: $institusi\n";
    $pesan .= "Layanan: *" . strtoupper(str_replace('_', ' ', $tipe)) . "*\n";
    $pesan .= "Waktu: $waktu_info\n";
    $pesan .= "Jumlah Peserta: *$jml_orang Orang*\n"; // PENAMBAHAN INFO PESERTA
    $pesan .= "Keperluan: $keperluan\n";
    $pesan .= "------------------\n";
    $pesan .= "Mohon admin segera menentukan ruangan melalui dashboard.";

    $url_wa = "https://wa.me/$wa_admin?text=" . urlencode($pesan);
    ?>
    <script>
        alert('Pengajuan berhasil dikirim! Meneruskan notifikasi ke WhatsApp Admin...');
        window.location.href = '<?php echo $url_wa; ?>';
    </script>
    <?php
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>