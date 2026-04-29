<?php 
include '../config/koneksi.php';
session_start();
if($_SESSION['role'] != "admin") { header("location:../login.php"); exit(); }

$aksi = $_GET['aksi'];

if($aksi == "tambah"){
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_ruangan']);
    $tipe      = mysqli_real_escape_string($koneksi, $_POST['tipe']);
    $kapasitas = mysqli_real_escape_string($koneksi, $_POST['kapasitas']);
    $fasilitas = mysqli_real_escape_string($koneksi, $_POST['fasilitas']);

    $query = "INSERT INTO ruangan (nama_ruangan, tipe, kapasitas, fasilitas, keterangan) 
              VALUES ('$nama', '$tipe', '$kapasitas', '$fasilitas', '')";
    
    mysqli_query($koneksi, $query);
    header("location:ruangan.php");
    exit();

} elseif($aksi == "edit"){
    // --- LOGIKA EDIT DISINI ---
    $id        = mysqli_real_escape_string($koneksi, $_POST['id']);
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_ruangan']);
    $tipe      = mysqli_real_escape_string($koneksi, $_POST['tipe']);
    $kapasitas = mysqli_real_escape_string($koneksi, $_POST['kapasitas']);
    $fasilitas = mysqli_real_escape_string($koneksi, $_POST['fasilitas']);

    $query = "UPDATE ruangan SET 
              nama_ruangan = '$nama', 
              tipe = '$tipe', 
              kapasitas = '$kapasitas', 
              fasilitas = '$fasilitas' 
              WHERE id = '$id'";
    
    mysqli_query($koneksi, $query);
    header("location:ruangan.php");
    exit();

} elseif($aksi == "hapus"){
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    mysqli_query($koneksi, "DELETE FROM ruangan WHERE id='$id'");
    header("location:ruangan.php");
    exit();
}
?>