<?php 
include '../config/koneksi.php';
session_start();

// Proteksi akses
if($_SESSION['role'] != "admin") { 
    header("location:../login.php"); 
    exit();
}

// Pastikan variabel aksi ada
if(isset($_GET['aksi'])){
    $aksi = $_GET['aksi'];

    // LOGIKA TAMBAH RUANGAN
    if($aksi == "tambah"){
        $nama      = mysqli_real_escape_string($koneksi, $_POST['nama_ruangan']);
        $tipe      = mysqli_real_escape_string($koneksi, $_POST['tipe']);
        $kapasitas = mysqli_real_escape_string($koneksi, $_POST['kapasitas']);
        $fasilitas = mysqli_real_escape_string($koneksi, $_POST['fasilitas']);

        $query = "INSERT INTO ruangan (nama_ruangan, tipe, kapasitas, fasilitas, keterangan) 
                  VALUES ('$nama', '$tipe', '$kapasitas', '$fasilitas', '')";
        
        mysqli_query($koneksi, $query);
        header("location:ruangan.php");
        exit(); // Wajib ada exit
    }

    // LOGIKA HAPUS RUANGAN (Ini yang sebelumnya hilang)
    elseif($aksi == "hapus"){
        if(isset($_GET['id'])){
            $id = mysqli_real_escape_string($koneksi, $_GET['id']);
            
            // Jalankan perintah hapus
            $query = "DELETE FROM ruangan WHERE id='$id'";
            mysqli_query($koneksi, $query);
            
            header("location:ruangan.php");
            exit(); // Wajib ada exit
        }
    }
} else {
    // Jika aksi tidak ditemukan, kembalikan ke halaman ruangan
    header("location:ruangan.php");
    exit();
}
?>