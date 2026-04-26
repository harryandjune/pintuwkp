<?php 
include '../config/koneksi.php';
session_start();

if($_SESSION['role'] != "admin_kendaraan") { 
    header("location:../login.php"); 
    exit();
}

$aksi = $_GET['aksi'];

if($aksi == "tambah"){
    $plat   = mysqli_real_escape_string($koneksi, $_POST['nomor_plat']);
    $merk   = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $model  = mysqli_real_escape_string($koneksi, $_POST['model']);
    $tahun  = mysqli_real_escape_string($koneksi, $_POST['tahun_produksi']);
    $jenis  = mysqli_real_escape_string($koneksi, $_POST['jenis_kendaraan']);
    $kap    = mysqli_real_escape_string($koneksi, $_POST['kapasitas']);

    $query = "INSERT INTO kendaraan (nomor_plat, merk, model, tahun_produksi, jenis_kendaraan, kapasitas, status_kendaraan) 
              VALUES ('$plat', '$merk', '$model', '$tahun', '$jenis', '$kap', 'tersedia')";
    
    mysqli_query($koneksi, $query);
    header("location:kendaraan.php");
    exit();

} elseif($aksi == "hapus"){
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    mysqli_query($koneksi, "DELETE FROM kendaraan WHERE id_kendaraan='$id'");
    header("location:kendaraan.php");
    exit();
}
?>