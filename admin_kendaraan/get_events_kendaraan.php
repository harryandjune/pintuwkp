<?php
include '../config/koneksi.php';

// Ambil hanya yang statusnya disetujui untuk kalender
$query = "SELECT r.*, k.merk, k.model, k.nomor_plat, u.nama_lengkap 
          FROM reservasi_kendaraan r 
          JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
          JOIN users u ON r.user_id = u.id
          WHERE r.status = 'disetujui'";

$data = mysqli_query($koneksi, $query);
$events = array();

while($row = mysqli_fetch_assoc($data)) {
    // Warna Kuning Amber untuk Kendaraan
    $color = '#f59e0b'; 

    $events[] = array(
        'id'    => $row['id'],
        'title' => $row['merk']." ".$row['model']." (".$row['institusi_peminjam'].")",
        'start' => $row['tgl_mulai'],
        'end'   => $row['tgl_selesai'],
        'backgroundColor' => $color,
        'borderColor'     => $color,
        'extendedProps'   => array(
            'tujuan'      => $row['tujuan'],
            'peminjam'    => $row['institusi_peminjam'],
            'pic'         => $row['nama_lengkap'],
            'sopir'       => strtoupper($row['pakai_sopir']),
            'plat'        => $row['nomor_plat']
        )
    );
}

echo json_encode($events);