<?php
// 1. PENGATURAN ERROR (Sembunyikan dari publik, catat ke log)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');

// 2. SET ZONA WAKTU (Penting agar logika jam akurat)
date_default_timezone_set('Asia/Makassar'); // Gunakan 'Asia/Jakarta' jika di WIB
$now_datetime = date('Y-m-d H:i:s');
$today_date   = date('Y-m-d');

// 3. KONFIGURASI DATABASE
$host = "localhost";
$user = "root"; // Sesuaikan jika di server online
$pass = "";     // Sesuaikan jika di server online
$db   = "pintuwkp";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// -----------------------------------------------------------------------
// 4. LOGIKA AUTO-REJECT (OTOMATIS TOLAK JIKA KADALUARSA)
// -----------------------------------------------------------------------

// A. Untuk Reservasi Gedung (GH & MR)
// Meeting Room: Ditolak jika (Tgl + Jam Mulai) sudah lewat dari waktu sekarang
// Guest House: Ditolak jika Tgl Mulai sudah lebih kecil dari hari ini
$sql_auto_reject_gedung = "UPDATE reservasi SET 
    status = 'ditolak', 
    catatan_admin = 'Sistem: Otomatis ditolak karena melewati batas waktu mulai penggunaan (Expired).' 
    WHERE status = 'pending' 
    AND (
        (tipe_permintaan = 'meeting_room' AND CONCAT(tgl_pinjam, ' ', jam_mulai) < '$now_datetime') 
        OR 
        (tipe_permintaan = 'guest_house' AND tgl_pinjam < '$today_date')
    )";
mysqli_query($koneksi, $sql_auto_reject_gedung);

// B. Untuk Reservasi Kendaraan
// Karena menggunakan DATETIME, langsung bandingkan dengan waktu sekarang
$sql_auto_reject_mobil = "UPDATE reservasi_kendaraan SET 
    status = 'ditolak', 
    catatan_admin = 'Sistem: Otomatis ditolak karena melewati batas waktu mulai penggunaan (Expired).' 
    WHERE status = 'pending' 
    AND tgl_mulai < '$now_datetime'";
mysqli_query($koneksi, $sql_auto_reject_mobil);

// -----------------------------------------------------------------------
// 5. AMBIL DATA PENGATURAN SISTEM
// -----------------------------------------------------------------------
$set = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
$sett = mysqli_fetch_array($set);

// JIKA TABEL KOSONG, BERI DATA DEFAULT AGAR SISTEM TIDAK ERROR
if (!$sett) {
    $sett = [
        'nama_sistem' => 'PINTU WKP',
        'copyright' => 'YPPH',
        'tahun_sistem' => '2026',
        'logo' => 'logo.png',
        'favicon' => 'favicon.ico',
        'deskripsi' => 'Pusat Informasi & Tata Usaha WKP'
    ];
}
?>