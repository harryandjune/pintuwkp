<?php
include '../config/koneksi.php';
session_start();

// Pastikan hanya admin yang bisa akses
if($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
    exit(); // Tambahkan exit
}

// Ambil data teks
$nama       = mysqli_real_escape_string($koneksi, $_POST['nama_sistem']);
$deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
$kontak     = mysqli_real_escape_string($koneksi, $_POST['kontak_admin']);
$alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat_kantor']);
$copyright  = mysqli_real_escape_string($koneksi, $_POST['copyright']);
$tahun      = mysqli_real_escape_string($koneksi, $_POST['tahun_sistem']);

// Penanganan File Logo
$logo_name = $sett['logo']; 
if ($_FILES['logo']['name'] != "") {
    $ekstensi = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
    $logo_name = "logo_" . time() . "." . $ekstensi;
    move_uploaded_file($_FILES['logo']['tmp_name'], "../assets/img/" . $logo_name);
}

// Penanganan File Favicon
$favicon_name = $sett['favicon']; 
if ($_FILES['favicon']['name'] != "") {
    $ekstensi = pathinfo($_FILES['favicon']['name'], PATHINFO_EXTENSION);
    $favicon_name = "fav_" . time() . "." . $ekstensi;
    move_uploaded_file($_FILES['favicon']['tmp_name'], "../assets/img/" . $favicon_name);
}

$query = "UPDATE pengaturan SET 
            nama_sistem = '$nama', 
            deskripsi = '$deskripsi', 
            logo = '$logo_name', 
            favicon = '$favicon_name', 
            kontak_admin = '$kontak', 
            alamat_kantor = '$alamat', 
            copyright = '$copyright', 
            tahun_sistem = '$tahun' 
          WHERE id = 1";

if (mysqli_query($koneksi, $query)) {
    header("location:pengaturan.php?pesan=sukses");
    exit(); // Tambahkan exit
} else {
    echo "Gagal memperbarui: " . mysqli_error($koneksi);
}
?>