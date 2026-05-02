<?php

// Matikan tampilan error ke layar publik
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Laporkan semua jenis error tapi jangan ditampilkan
error_reporting(E_ALL);

// Aktifkan pencatatan error ke file (Log)
ini_set('log_errors', 1);

// Tentukan lokasi file catatan error (pastikan folder ini ada)
ini_set('error_log', __DIR__ . '/../error_log.txt');
$host = "localhost";
$user = "root";
$pass = ""; 
$db   = "pintuwkp";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Ambil data pengaturan
$set = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
$sett = mysqli_fetch_array($set);

// JIKA TABEL KOSONG, BERI DATA DEFAULT AGAR SISTEM TIDAK ERROR
if (!$sett) {
    $sett = [
        'nama_sistem' => 'PINTU WKP',
        'copyright' => 'YPPH',
        'tahun_sistem' => '2026',
        'logo' => 'logo.png',
        'favicon' => 'favicon.ico'
    ];
}