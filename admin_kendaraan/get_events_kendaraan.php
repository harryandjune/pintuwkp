<?php
include '../config/koneksi.php';

// Ambil data disetujui DAN selesai agar riwayat tetap terlihat di kalender
$query = "SELECT r.*, k.merk, k.model, k.nomor_plat, u.nama_lengkap 
          FROM reservasi_kendaraan r 
          LEFT JOIN kendaraan k ON r.kendaraan_id = k.id_kendaraan 
          JOIN users u ON r.user_id = u.id
          WHERE r.status IN ('disetujui', 'selesai')";

$data = mysqli_query($koneksi, $query);
$events = array();

while($row = mysqli_fetch_assoc($data)) {
    // Bedakan warna: Biru untuk yang sudah Selesai, Amber untuk yang masih Aktif/Disetujui
    $color = ($row['status'] == 'selesai') ? '#64748b' : '#f59e0b'; 

    $events[] = array(
        'id'    => $row['id'],
        'title' => ($row['merk'] ?? 'Mobil')." (".$row['institusi_peminjam'].")",
        'start' => $row['tgl_mulai'],
        'end'   => $row['tgl_selesai'],
        'backgroundColor' => $color,
        'borderColor'     => $color,
        'extendedProps'   => array(
            'tujuan'      => $row['tujuan'],
            'peminjam'    => $row['institusi_peminjam'],
            'pic'         => $row['nama_lengkap'],
            'sopir'       => strtoupper($row['pakai_sopir']),
            'plat'        => $row['nomor_plat'] ?? '-',
            'status'      => strtoupper($row['status']) // Tambahkan status ke props
        )
    );
}

echo json_encode($events);