<?php
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