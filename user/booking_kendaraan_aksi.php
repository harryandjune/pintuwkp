<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "user") {
    exit();
}

$user_id    = $_SESSION['id_user'];
$nama_user  = $_SESSION['nama'];
$mobil_id   = $_POST['kendaraan_id'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_mulai'];
$tgl_selesai = $_POST['tgl_selesai'];
$tujuan     = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$sopir      = $_POST['pakai_sopir'];
$nama_sopir_alt = mysqli_real_escape_string($koneksi, $_POST['nama_sopir_alt'] ?? '');

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
    $sql = "INSERT INTO reservasi_kendaraan (user_id, institusi_peminjam, kendaraan_id, tgl_mulai, tgl_selesai, tujuan, keperluan, pakai_sopir, nama_sopir_alt, status) 
        VALUES ('$user_id', '$institusi', '$mobil_id', '$tgl_mulai', '$tgl_selesai', '$tujuan', '$keperluan', '$sopir', '$nama_sopir_alt', 'pending')";

    if (mysqli_query($koneksi, $sql)) {
        // --- NOTIF WA KE ADMIN KENDARAAN ---
        $get_admin = mysqli_query($koneksi, "SELECT no_wa FROM users WHERE role = 'admin_kendaraan' LIMIT 1");
        $adm = mysqli_fetch_assoc($get_admin);

        $wa_admin = preg_replace('/[^0-9]/', '', $adm['no_wa'] ?? '');
        if (substr($wa_admin, 0, 1) === '0') $wa_admin = '62' . substr($wa_admin, 1);

        if ($sopir == 'ya') {
            $ket_sopir = "YA (Sopir Yayasan)";
        } else {
            // Jika tidak, tampilkan nama sopir yang dibawa user
            $ket_sopir = "TIDAK (Bawa Sendiri: *$nama_sopir_alt*)";
        }

        // 3. Susun Pesan WA secara lengkap
        $pesan = "*BOOKING MOBIL BARU*\n";
        $pesan .= "------------------\n";
        $pesan .= "Pemohon: $nama_user\n";
        $pesan .= "Unit: $institusi\n";
        $pesan .= "Mobil: *$nama_mobil*\n";
        $pesan .= "Tujuan: $tujuan\n";
        $pesan .= "Waktu: $tgl_mulai s.d $tgl_selesai\n";
        $pesan .= "Sopir: $ket_sopir\n"; // Menggunakan keterangan yang dibuat di atas
        $pesan .= "------------------\n";
        $pesan .= "Mohon admin segera mengecek dashboard untuk konfirmasi.";

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
