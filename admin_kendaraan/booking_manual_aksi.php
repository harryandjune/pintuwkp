<?php
session_start();
include '../config/koneksi.php';

if($_SESSION['role'] != "admin_kendaraan") { exit(); }

$user_id    = $_SESSION['id_user']; // ID Admin yang menginput
$mobil_id   = $_POST['kendaraan_id'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_mulai'];
$tgl_selesai= $_POST['tgl_selesai'];
$tujuan     = mysqli_real_escape_string($koneksi, $_POST['tujuan']);
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$sopir      = mysqli_real_escape_string($koneksi, $_POST['sopir']);

// --- TETAP CEK BENTROK AGAR TIDAK DOUBLE BOOKING ---
$query_cek = "SELECT * FROM reservasi_kendaraan 
              WHERE kendaraan_id = '$mobil_id' 
              AND status = 'disetujui'
              AND (tgl_mulai < '$tgl_selesai' AND tgl_selesai > '$tgl_mulai')";

$cek = mysqli_query($koneksi, $query_cek);

if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Gagal! Mobil tersebut sudah memiliki jadwal disetujui di jam yang sama.'); window.history.back();</script>";
} else {
    // Simpan langsung dengan status 'disetujui'
    $sql = "INSERT INTO reservasi_kendaraan (user_id, institusi_peminjam, kendaraan_id, tgl_mulai, tgl_selesai, tujuan, keperluan, pakai_sopir, nama_sopir_alt, status) 
            VALUES ('$user_id', '$institusi', '$mobil_id', '$tgl_mulai', '$tgl_selesai', '$tujuan', '$keperluan', 'ya', '$sopir', 'disetujui')";

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Reservasi Pengurus Berhasil Dicatat!'); window.location.href='kalender.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}