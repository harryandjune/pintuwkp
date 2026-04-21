<?php 
include '../config/koneksi.php';
session_start();
if($_SESSION['role'] != "admin") { header("location:../login.php"); }

$id     = $_GET['id'];
$status = $_GET['status']; // 'disetujui' atau 'ditolak'

// Update status di tabel reservasi
$query = "UPDATE reservasi SET status = '$status' WHERE id = '$id'";

if(mysqli_query($koneksi, $query)){
    header("location:persetujuan.php?pesan=berhasil");
} else {
    echo "Gagal memproses data: " . mysqli_error($koneksi);
}
?>