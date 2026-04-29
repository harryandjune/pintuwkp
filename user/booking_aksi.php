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

$jam_mulai   = ($tipe == 'meeting_room') ? $_POST['jam_mulai'] : '00:00:00';
$jam_selesai = ($tipe == 'meeting_room') ? $_POST['jam_selesai'] : '23:59:59';

// SIMPAN DATA (ruangan_id diset NULL)
$sql = "INSERT INTO reservasi (user_id, tipe_permintaan, institusi_peminjam, ruangan_id, tgl_pinjam, tgl_selesai, jam_mulai, jam_selesai, keperluan, jumlah_orang, status) 
        VALUES ('$user_id', '$tipe', '$institusi', NULL, '$tgl_mulai', '$tgl_selesai', '$jam_mulai', '$jam_selesai', '$keperluan', '$jml_orang', 'pending')";

if (mysqli_query($koneksi, $sql)) {
    // Susun Pesan WA ke Admin Gedung (WKP)
    $wa_admin = preg_replace('/[^0-9]/', '', $sett['kontak_admin'] ?? '');
    if (substr($wa_admin, 0, 1) === '0') $wa_admin = '62' . substr($wa_admin, 1);

    $pesan = "*PERMINTAAN GEDUNG BARU*\n";
    $pesan .= "------------------\n";
    $pesan .= "Pemohon: $nama_user\n";
    $pesan .= "Unit: $institusi\n";
    $pesan .= "Layanan: *" . strtoupper(str_replace('_', ' ', $tipe)) . "*\n";
    $pesan .= "Waktu: " . date('d/m/Y', strtotime($tgl_mulai)) . "\n";
    $pesan .= "------------------\n";
    $pesan .= "Mohon admin menentukan ruangan di dashboard.";

    $url_wa = "https://wa.me/$wa_admin?text=" . urlencode($pesan);
    ?>
    <script>
        alert('Pengajuan Gedung berhasil dikirim!');
        window.location.href = '<?php echo $url_wa; ?>';
    </script>
    <?php
}
?>