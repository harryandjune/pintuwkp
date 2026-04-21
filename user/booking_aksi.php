<?php
session_start();
include '../config/koneksi.php';

$user_id    = $_SESSION['id_user'];
$ruangan_id = $_POST['ruangan_id'];
$tipe       = $_POST['tipe'];
$tgl_mulai  = $_POST['tgl_pinjam'];
$tgl_selesai= $_POST['tgl_selesai'];
$keperluan  = $_POST['keperluan'];
$jml_orang  = $_POST['jumlah_orang'];

// Jika meeting room, ambil jam. Jika guest house, set NULL.
$jam_mulai   = ($tipe == 'meeting_room') ? $_POST['jam_mulai'] : '00:00:00';
$jam_selesai = ($tipe == 'meeting_room') ? $_POST['jam_selesai'] : '00:00:00';

// LOGIKA CEK BENTROK
// Cek apakah ada booking di ruangan yang sama, status disetujui, dan waktunya bertabrakan
$query_cek = "SELECT * FROM reservasi 
              WHERE ruangan_id = '$ruangan_id' 
              AND status = 'disetujui'
              AND (
                  (tgl_pinjam <= '$tgl_selesai' AND tgl_selesai >= '$tgl_mulai')
              )";

$cek = mysqli_query($koneksi, $query_cek);

if (mysqli_num_rows($cek) > 0) {
    // Jika ada yang bentrok
    echo "<script>alert('Maaf, Ruangan sudah dipesan/disetujui pada tanggal tersebut. Silakan pilih tanggal lain.'); window.history.back();</script>";
} else {
    // Jika aman, simpan data
    $sql = "INSERT INTO reservasi (user_id, ruangan_id, tgl_pinjam, tgl_selesai, jam_mulai, jam_selesai, keperluan, jumlah_orang, status) 
            VALUES ('$user_id', '$ruangan_id', '$tgl_mulai', '$tgl_selesai', '$jam_mulai', '$jam_selesai', '$keperluan', '$jml_orang', 'pending')";
    
    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Booking berhasil dikirim! Menunggu persetujuan admin.'); window.location.href='riwayat.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>