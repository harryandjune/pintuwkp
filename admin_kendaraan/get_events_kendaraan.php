<?php
include '../config/koneksi.php';

$query = "SELECT r.*, k.merk, k.model, k.nomor_plat, u.nama_lengkap 
          FROM reservasi_kendaraan r 
          LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
          JOIN users u ON r.user_id = u.id
          WHERE r.status IN ('disetujui', 'selesai')";

$data = mysqli_query($koneksi, $query);
$events = array();

while($row = mysqli_fetch_assoc($data)) {
    $color = ($row['status'] == 'selesai') ? '#64748b' : '#f59e0b'; 

    $events[] = array(
        'id'    => $row['id'],
        'title' => ($row['model'] ?? 'Mobil') . " (" . $row['institusi_peminjam'] . ")", 
        'start' => $row['tgl_mulai'],
        'end'   => $row['tgl_selesai'],
        'backgroundColor' => $color,
        'borderColor'     => $color,
        'extendedProps'   => array(
            'merk'        => $row['merk'] ?? '', // Ambil Merk
            'model'       => $row['model'] ?? '', // Ambil Model
            'tujuan'      => $row['tujuan'],
            'peminjam'    => $row['institusi_peminjam'],
            'pic'         => $row['nama_lengkap'],
            'sopir'       => strtoupper($row['pakai_sopir']),
            'plat'        => $row['nomor_plat'] ?? '-',
            'status'      => strtoupper($row['status'])
        )
    );
}
echo json_encode($events);