<?php
include '../config/koneksi.php';

// 1. Ambil data yang berstatus 'disetujui' (Aktif) dan 'selesai' (Arsip)
// Gunakan LEFT JOIN ke ruangan agar data tetap aman jika ada anomali
$query = "SELECT r.*, rm.nama_ruangan, u.nama_lengkap 
          FROM reservasi r 
          LEFT JOIN ruangan rm ON r.ruangan_id = rm.id 
          JOIN users u ON r.user_id = u.id
          WHERE r.status IN ('disetujui', 'selesai')";

$data = mysqli_query($koneksi, $query);
$events = array();

while($row = mysqli_fetch_assoc($data)) {
    
    // 2. Logika Warna Berdasarkan Status dan Tipe
    if ($row['status'] == 'selesai') {
        // Jika sudah selesai, beri warna abu-abu (Arsip)
        $color = '#94a3b8'; 
    } else {
        // Jika masih aktif, bedakan warna berdasarkan kategori
        // Guest House: Cyan, Meeting Room: Biru
        $color = ($row['tipe_permintaan'] == 'guest_house') ? '#0dcaf0' : '#0d6efd';
    }

    // 3. Susun Data untuk FullCalendar
    $events[] = array(
        'id'    => $row['id'],
        // Judul: [Nama Ruangan] - [Nama Institusi]
        'title' => ($row['nama_ruangan'] ?? 'Unit') . " - " . $row['institusi_peminjam'],
        
        // Format ISO8601 untuk FullCalendar (YYYY-MM-DDTHH:MM:SS)
        'start' => $row['tgl_pinjam'] . "T" . $row['jam_mulai'],
        'end'   => $row['tgl_selesai'] . "T" . $row['jam_selesai'],
        
        'backgroundColor' => $color,
        'borderColor'     => $color,
        
        // Data tambahan untuk ditampilkan di Modal saat diklik
        'extendedProps'   => array(
            'keperluan' => $row['keperluan'],
            'pemohon'   => $row['institusi_peminjam'],
            'pic'       => $row['nama_lengkap'],
            'status'    => strtoupper($row['status']),
            'ruangan'   => $row['nama_ruangan'] ?? 'Belum Ditentukan'
        )
    );
}

// 4. Kirim sebagai JSON
header('Content-Type: application/json');
echo json_encode($events);