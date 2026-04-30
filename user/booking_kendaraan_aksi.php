<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "user") {
    exit();
}

// 1. Tangkap Data dari Form
$user_id    = $_SESSION['id_user'];
$nama_user  = $_SESSION['nama'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_mulai'];
$tgl_selesai = $_POST['tgl_selesai'];
$tujuan     = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$sopir      = $_POST['pakai_sopir'];
$nama_sopir_alt = mysqli_real_escape_string($koneksi, $_POST['nama_sopir_alt'] ?? '');
$jenis_permintaan = mysqli_real_escape_string($koneksi, $_POST['jenis_permintaan']);

// --- CATATAN LOGIKA BENTROK ---
// Karena user belum memilih mobil spesifik (ID mobil masih NULL), 
// kita tidak bisa mengecek bentrok fisik kendaraan di sini.
// Pengecekan bentrok akan dilakukan oleh ADMIN saat mengalokasikan unit.

// 2. Simpan ke Database (Kolom kendaraan_id diisi NULL karena belum ditentukan admin)
$sql = "INSERT INTO reservasi_kendaraan (user_id, institusi_peminjam, jenis_permintaan, kendaraan_id, tgl_mulai, tgl_selesai, tujuan, keperluan, pakai_sopir, nama_sopir_alt, status) 
        VALUES ('$user_id', '$institusi', '$jenis_permintaan', NULL, '$tgl_mulai', '$tgl_selesai', '$tujuan', '$keperluan', '$sopir', '$nama_sopir_alt', 'pending')";

if (mysqli_query($koneksi, $sql)) {
    
    // --- 3. LOGIKA NOTIFIKASI WHATSAPP KE ADMIN ---
    
    // Ambil nomor WA Admin Kendaraan
    $get_admin = mysqli_query($koneksi, "SELECT no_wa FROM users WHERE role = 'admin_kendaraan' LIMIT 1");
    $adm = mysqli_fetch_assoc($get_admin);

    $wa_admin = preg_replace('/[^0-9]/', '', $adm['no_wa'] ?? '');
    if (substr($wa_admin, 0, 1) === '0') $wa_admin = '62' . substr($wa_admin, 1);

    // Keterangan Sopir
    if ($sopir == 'ya') {
        $ket_sopir = "YA (Sopir Yayasan)";
    } else {
        $ket_sopir = "TIDAK (Bawa Sendiri: *$nama_sopir_alt*)";
    }

    // Susun Pesan WA (Menggunakan Jenis Permintaan, bukan merk mobil)
    $pesan = "*PEMINJAMAN KENDARAAN*\n";
    $pesan .= "------------------\n";
    $pesan .= "Pemohon: $nama_user\n";
    $pesan .= "Unit: $institusi\n";
    $pesan .= "Jenis Dibutuhkan: *$jenis_permintaan*\n";
    $pesan .= "Tujuan: $tujuan\n";
    $pesan .= "Keperluan: $keperluan\n";
    $pesan .= "Waktu: ".date('d/m/Y H:i', strtotime($tgl_mulai))." s.d ".date('d/m/Y H:i', strtotime($tgl_selesai))."\n";
    $pesan .= "Sopir: $ket_sopir\n";
    $pesan .= "------------------\n";
    $pesan .= "Mohon admin segera menentukan unit armada di dashboard.";

    $url_wa = "https://wa.me/$wa_admin?text=" . urlencode($pesan);
?>
    <!-- Tampilan Sukses -->
    <script>
        alert('Pengajuan berhasil dikirim! Silakan teruskan notifikasi ke WhatsApp Admin.');
        window.location.href = '<?php echo $url_wa; ?>';
    </script>
<?php
} else {
    echo "Error: " . mysqli_error($koneksi);
}
?>