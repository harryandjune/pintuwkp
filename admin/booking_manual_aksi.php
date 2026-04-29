<?php
session_start();
include '../config/koneksi.php';

if($_SESSION['role'] != "admin") { exit(); }

$user_id    = $_SESSION['id_user'];
$ruangan_id = $_POST['ruangan_id'];
$institusi  = mysqli_real_escape_string($koneksi, $_POST['institusi_peminjam']);
$tgl_mulai  = $_POST['tgl_pinjam'];
$tgl_selesai= $_POST['tgl_selesai'];
$keperluan  = mysqli_real_escape_string($koneksi, $_POST['keperluan']);
$jml_orang  = $_POST['jumlah_orang'];

// Tentukan Tipe Permintaan untuk database
$q_tipe = mysqli_query($koneksi, "SELECT tipe FROM ruangan WHERE id='$ruangan_id'");
$tipe_data = mysqli_fetch_assoc($q_tipe);
$tipe_permintaan = $tipe_data['tipe'];

$jam_mulai   = ($tipe_permintaan == 'meeting_room') ? $_POST['jam_mulai'] : '00:00:00';
$jam_selesai = ($tipe_permintaan == 'meeting_room') ? $_POST['jam_selesai'] : '23:59:59';

// Cek Bentrok (Wajib agar admin tidak tabrakan jadwal)
$cek = mysqli_query($koneksi, "SELECT * FROM reservasi WHERE ruangan_id='$ruangan_id' AND status='disetujui' AND (tgl_pinjam <= '$tgl_selesai' AND tgl_selesai >= '$tgl_mulai')");

if(mysqli_num_rows($cek) > 0 && $tipe_permintaan == 'guest_house') {
    echo "<script>alert('Gagal! Kamar tersebut sudah terisi pada tanggal tersebut.'); window.history.back();</script>";
} else {
    // Simpan Langsung 'disetujui'
    $sql = "INSERT INTO reservasi (user_id, tipe_permintaan, institusi_peminjam, ruangan_id, tgl_pinjam, tgl_selesai, jam_mulai, jam_selesai, keperluan, jumlah_orang, status) 
            VALUES ('$user_id', '$tipe_permintaan', '$institusi', '$ruangan_id', '$tgl_mulai', '$tgl_selesai', '$jam_mulai', '$jam_selesai', '$keperluan', '$jml_orang', 'disetujui')";

    if(mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Booking Berhasil Dicatat!'); window.location.href='kalender.php';</script>";
    }
}