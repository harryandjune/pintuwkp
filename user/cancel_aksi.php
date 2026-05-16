<?php
session_start();
include '../config/koneksi.php';

if ($_SESSION['role'] != "user") { exit(); }

$id     = mysqli_real_escape_string($koneksi, $_POST['id']);
$type   = $_POST['type']; // 'gedung' atau 'mobil'
$alasan = mysqli_real_escape_string($koneksi, $_POST['alasan']);
$nama_user = $_SESSION['nama'];

if ($type == 'gedung') {
    // 1. Update DB Gedung
    mysqli_query($koneksi, "UPDATE reservasi SET status = 'dibatalkan', catatan_admin = 'Dibatalkan oleh User: $alasan' WHERE id = '$id'");
    
    // 2. Tujuan WA: Admin Gedung (dari tabel pengaturan)
    $wa_tujuan = preg_replace('/[^0-9]/', '', $sett['kontak_admin']);
    $item_name = "Ruangan/GH";

} else {
    // 1. Update DB Mobil
    mysqli_query($koneksi, "UPDATE reservasi_kendaraan SET status = 'dibatalkan', catatan_admin = 'Dibatalkan oleh User: $alasan' WHERE id = '$id'");
    
    // 2. Tujuan WA: Admin Kendaraan (Cari user dengan role admin_kendaraan)
    $get_adm = mysqli_query($koneksi, "SELECT no_wa FROM users WHERE role = 'admin_kendaraan' LIMIT 1");
    $adm = mysqli_fetch_assoc($get_adm);
    $wa_tujuan = preg_replace('/[^0-9]/', '', $adm['no_wa'] ?? '');
    $item_name = "Kendaraan/Mobil";
}

// 3. Format nomor WA ke Internasional
if (substr($wa_tujuan, 0, 1) === '0') $wa_tujuan = '62' . substr($wa_tujuan, 1);

// 4. Susun Pesan WA
$pesan = "*PEMBATALAN BOOKING OLEH USER*\n";
$pesan .= "------------------\n";
$pesan .= "Pemohon: $nama_user\n";
$pesan .= "Layanan: *$item_name*\n";
$pesan .= "Alasan: _\"$alasan\"_\n";
$pesan .= "------------------\n";
$pesan .= "Sistem telah otomatis membatalkan pesanan tersebut.";

$url_wa = "https://wa.me/$wa_tujuan?text=" . urlencode($pesan);

echo "<script>alert('Berhasil dibatalkan. Mohon teruskan informasi ini ke Admin via WhatsApp.'); window.location.href='$url_wa';</script>";
?>