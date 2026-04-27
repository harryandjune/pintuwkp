-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 26, 2026 at 08:51 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pintuwkp`
--

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int NOT NULL,
  `nomor_plat` varchar(20) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `tahun_produksi` year NOT NULL,
  `jenis_kendaraan` varchar(50) NOT NULL,
  `kapasitas` int NOT NULL,
  `foto` varchar(255) DEFAULT 'default_car.png',
  `status_kendaraan` enum('tersedia','perbaikan','nonaktif') DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `nomor_plat`, `merk`, `model`, `tahun_produksi`, `jenis_kendaraan`, `kapasitas`, `foto`, `status_kendaraan`, `created_at`) VALUES
(1, 'KT 123 YC', 'Honda', 'CRV', '2024', 'MPV', 8, 'default_car.png', 'tersedia', '2026-04-25 23:08:25'),
(2, 'KT 6787 GH', 'Toyota', 'Innova', '2021', 'MPV', 8, 'default_car.png', 'tersedia', '2026-04-25 23:08:54');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int NOT NULL,
  `nama_sistem` varchar(100) NOT NULL,
  `deskripsi` text,
  `logo` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `kontak_admin` varchar(50) DEFAULT NULL,
  `alamat_kantor` text,
  `copyright` varchar(100) DEFAULT NULL,
  `tahun_sistem` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_sistem`, `deskripsi`, `logo`, `favicon`, `kontak_admin`, `alamat_kantor`, `copyright`, `tahun_sistem`) VALUES
(1, 'Pintu WKP', 'Pusat Informasi dan Tata Usaha YPPH', 'logo_1776959664.png', 'fav_1776959664.png', '085345631391', 'Jl. Mulawarman', 'YPPH Balikpapan', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `institusi_peminjam` varchar(100) DEFAULT NULL,
  `ruangan_id` int NOT NULL,
  `tgl_pinjam` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `jam_mulai` time DEFAULT NULL,
  `jam_selesai` time DEFAULT NULL,
  `keperluan` text NOT NULL,
  `jumlah_orang` int DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `catatan_admin` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservasi_kendaraan`
--

CREATE TABLE `reservasi_kendaraan` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `institusi_peminjam` varchar(100) NOT NULL,
  `kendaraan_id` int NOT NULL,
  `tgl_mulai` datetime NOT NULL,
  `tgl_selesai` datetime NOT NULL,
  `tujuan` text NOT NULL,
  `keperluan` text NOT NULL,
  `pakai_sopir` enum('ya','tidak') DEFAULT 'ya',
  `status` enum('pending','disetujui','ditolak','selesai') DEFAULT 'pending',
  `catatan_admin` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `reservasi_kendaraan`
--

INSERT INTO `reservasi_kendaraan` (`id`, `user_id`, `institusi_peminjam`, `kendaraan_id`, `tgl_mulai`, `tgl_selesai`, `tujuan`, `keperluan`, `pakai_sopir`, `status`, `catatan_admin`, `created_at`) VALUES
(1, 3, 'MI RM Putra', 1, '2026-04-26 07:22:00', '2026-04-26 19:22:00', 'Bandara', 'Antar Ustadz DPP', 'tidak', 'disetujui', NULL, '2026-04-25 23:23:03');

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id` int NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `tipe` enum('guest_house','meeting_room') NOT NULL,
  `kapasitas` int DEFAULT NULL,
  `fasilitas` text,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id`, `nama_ruangan`, `tipe`, `kapasitas`, `fasilitas`, `keterangan`) VALUES
(1, 'Meetin Room Utama', 'meeting_room', 60, 'Sound sistem, AC', ''),
(2, 'Meeting Room VIP', 'meeting_room', 20, 'AC, Sound Sistem, TV LED', ''),
(4, 'Kamar 1 (VIP)', 'guest_house', 2, 'AC, Ruang Tamu, Dispenser', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `unit` varchar(100) DEFAULT NULL,
  `no_wa` varchar(20) DEFAULT NULL,
  `role` enum('admin','user','admin_kendaraan') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `unit`, `no_wa`, `role`, `created_at`) VALUES
(1, 'admin_wkp', '0192023a7bbd73250516f069df18b500', 'Administrator WKP', 'Kantor Pengurus', NULL, 'admin', '2026-04-12 20:58:52'),
(2, 'unit_sekolah', '6ad14ba9986e3615423dfca256d04e3f', 'Budi Setiadi', 'Unit Sekolah Yayasan', NULL, 'user', '2026-04-12 20:58:52'),
(3, 'hamimal', '1a50f204bef29eab587463f40b344dc6', 'Hamimal Mustafa', 'SMH', NULL, 'user', '2026-04-21 11:15:30'),
(5, 'muhakbar', 'f039e5f60e85d10bf7b742e65ad931ca', 'Muhammad Akbar', 'Sekretariat YPPH', '085345631391', 'admin_kendaraan', '2026-04-25 22:53:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD UNIQUE KEY `nomor_plat` (`nomor_plat`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ruangan_id` (`ruangan_id`);

--
-- Indexes for table `reservasi_kendaraan`
--
ALTER TABLE `reservasi_kendaraan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `kendaraan_id` (`kendaraan_id`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `reservasi_kendaraan`
--
ALTER TABLE `reservasi_kendaraan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservasi_kendaraan`
--
ALTER TABLE `reservasi_kendaraan`
  ADD CONSTRAINT `reservasi_kendaraan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_kendaraan_ibfk_2` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraan` (`id_kendaraan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
