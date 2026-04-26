<?php
session_start();
include '../config/koneksi.php';

if($_SESSION['role'] != "user") { exit(); }

$user_id    = $_SESSION['id_user'];
$nama_user  = $_SESSION['nama'];
$mobil_id   = $_POST['kendaraan_id'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_mulai'];
$tgl_selesai= $_POST['tgl_selesai'];
$tujuan     = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$sopir      = $_POST['pakai_sopir'];

// Ambil info mobil
$m = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT merk, model, nomor_plat FROM kendaraan WHERE id_kendaraan='$mobil_id'"));
$nama_mobil = $m['merk'] . " " . $m['model'];

// --- LOGIKA CEK BENTROK MOBIL ---
$query_cek = "SELECT * FROM reservasi_kendaraan 
              WHERE kendaraan_id = '$mobil_id' 
              AND status = 'disetujui'
              AND (tgl_mulai < '$tgl_selesai' AND tgl_selesai > '$tgl_mulai')";

$cek = mysqli_query($koneksi, $query_cek);

if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Maaf, mobil sudah terisi pada waktu tersebut.'); window.history.back();</script>";
} else {
    // Simpan
    $sql = "INSERT INTO reservasi_kendaraan (user_id, institusi_peminjam, kendaraan_id, tgl_mulai, tgl_selesai, tujuan, keperluan, pakai_sopir, status) 
            VALUES ('$user_id', '$institusi', '$mobil_id', '$tgl_mulai', '$tgl_selesai', '$tujuan', '$keperluan', '$sopir', 'pending')";

    if (mysqli_query($koneksi, $sql)) {
        // --- NOTIF WA KE ADMIN KENDARAAN ---
        $get_admin = mysqli_query($koneksi, "SELECT no_wa FROM users WHERE role = 'admin_kendaraan' LIMIT 1");
        $adm = mysqli_fetch_assoc($get_admin);
        
        $wa_admin = preg_replace('/[^0-9]/', '', $adm['no_wa'] ?? '');
        if(substr($wa_admin, 0, 1) === '0') $wa_admin = '62' . substr($wa_admin, 1);

        $pesan = "*BOOKING MOBIL BARU*\n------------------\n";
        $pesan .= "Pemohon: $nama_user\nUnit: $institusi\nMobil: $nama_mobil\nTujuan: $tujuan\nWaktu: $tgl_mulai s/d $tgl_selesai\nSopir: " . strtoupper($sopir);
        
        $url_wa = "https://wa.me/$wa_admin?text=" . urlencode($pesan);
        ?>
        <!-- Tampilan Sukses -->
        <script>
            alert('Booking Mobil Berhasil dikirim!');
            window.location.href = '<?php echo $url_wa; ?>';
        </script>
        <?php
    }
}