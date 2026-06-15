<?php
// 1. PENGATURAN ERROR
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error_log.txt');

// 2. PAKSA ZONA WAKTU PHP
// Kita set zona waktu, lalu kita ambil jamnya SETELAH koneksi database agar bisa sinkron
date_default_timezone_set('Asia/Makassar'); 

// 3. KONFIGURASI DATABASE
$host = "localhost";
$user = "root"; 
$pass = ""; 
$db   = "pintuwkp";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// 4. PAKSA ZONA WAKTU DATABASE (PENTING!)
// Mengatur agar MySQL menggunakan zona waktu WITA (+08:00)
mysqli_query($koneksi, "SET time_zone = '+08:00'");

// 5. AMBIL WAKTU SEKARANG DARI DATABASE (Agar PHP & DB Pasti Sama)
// Ini cara paling ampuh: kita tanya database jam berapa sekarang di WITA
$res_time = mysqli_query($koneksi, "SELECT NOW() as sekarang, CURDATE() as hari_ini");
$data_time = mysqli_fetch_assoc($res_time);

$now_datetime = $data_time['sekarang']; // Hasilnya pasti jam 10:xx (WITA)
$today_date   = $data_time['hari_ini'];


// -----------------------------------------------------------------------
// 6. LOGIKA AUTO-REJECT (MENGGUNAKAN JAM YANG SUDAH SINKRON)
// -----------------------------------------------------------------------

// A. Untuk Reservasi Gedung (GH & MR)
$sql_auto_reject_gedung = "UPDATE reservasi SET 
    status = 'ditolak', 
    catatan_admin = 'Sistem: Otomatis ditolak karena melewati batas waktu mulai penggunaan (Expired).' 
    WHERE status = 'pending' 
    AND (
        (tipe_permintaan = 'meeting_room' AND jam_mulai != '00:00:00' AND CONCAT(tgl_pinjam, ' ', jam_mulai) < '$now_datetime') 
        OR 
        (tipe_permintaan = 'guest_house' AND tgl_pinjam < '$today_date')
    )";
mysqli_query($koneksi, $sql_auto_reject_gedung);

// B. Untuk Reservasi Kendaraan
$sql_auto_reject_mobil = "UPDATE reservasi_kendaraan SET 
    status = 'ditolak', 
    catatan_admin = 'Sistem: Otomatis ditolak karena melewati batas waktu mulai penggunaan (Expired).' 
    WHERE status = 'pending' 
    AND tgl_mulai < '$now_datetime'";
mysqli_query($koneksi, $sql_auto_reject_mobil);


// -----------------------------------------------------------------------
// 7. AMBIL DATA PENGATURAN SISTEM
// -----------------------------------------------------------------------
$set = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
$sett = mysqli_fetch_array($set);

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