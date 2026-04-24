<?php
include '../config/koneksi.php';

$query = "SELECT r.*, rm.nama_ruangan, rm.tipe, u.unit 
          FROM reservasi r 
          JOIN ruangan rm ON r.ruangan_id = rm.id 
          JOIN users u ON r.user_id = u.id
          WHERE r.status = 'disetujui'";

$data = mysqli_query($koneksi, $query);
$events = array();

while($row = mysqli_fetch_assoc($data)) {
    // Tentukan warna berdasarkan tipe
    $color = ($row['tipe'] == 'guest_house') ? '#0dcaf0' : '#0d6efd';

    $events[] = array(
        'id'    => $row['id'],
        'title' => $row['nama_ruangan'] . " - " . $row['unit'],
        'start' => $row['tgl_pinjam'] . "T" . $row['jam_mulai'],
        'end'   => $row['tgl_selesai'] . "T" . $row['jam_selesai'],
        'backgroundColor' => $color,
        'borderColor'     => $color,
        'extendedProps'   => array(
            'keperluan' => $row['keperluan'],
            'pemohon'   => $row['unit']
        )
    );
}

echo json_encode($events);