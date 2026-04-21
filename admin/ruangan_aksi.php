<?php 
include '../config/koneksi.php';
session_start();
if($_SESSION['role'] != "admin") { header("location:../login.php"); }

$aksi = $_GET['aksi'];

if($aksi == "tambah"){
    $nama      = $_POST['nama_ruangan'];
    $tipe      = $_POST['tipe'];
    $kapasitas = $_POST['kapasitas'];
    $fasilitas = $_POST['fasilitas'];

    // CARA TERBAIK: Sebutkan kolomnya, lewatkan kolom 'id' karena otomatis terisi
    $query = "INSERT INTO ruangan (nama_ruangan, tipe, kapasitas, fasilitas, keterangan) 
              VALUES ('$nama', '$tipe', '$kapasitas', '$fasilitas', '')";
    
    mysqli_query($koneksi, $query);
    header("location:ruangan.php");
}