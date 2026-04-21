<?php
$host = "localhost";
$user = "root";
$pass = ""; // Kosongkan jika menggunakan standar XAMPP
$db   = "pintuwkp";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>