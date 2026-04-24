-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 24, 2026 at 08:19 AM
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

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id`, `user_id`, `ruangan_id`, `tgl_pinjam`, `tgl_selesai`, `jam_mulai`, `jam_selesai`, `keperluan`, `jumlah_orang`, `status`, `catatan_admin`, `created_at`) VALUES
(1, 2, 1, '2026-04-14', '2026-04-14', '09:08:00', '10:08:00', 'Rapat Panitia Pernikahan Mubarakah', 20, 'disetujui', NULL, '2026-04-13 01:08:42'),
(2, 4, 1, '2026-04-23', '2026-04-24', '10:20:00', '22:20:00', 'Rapat Pleni', 39, 'disetujui', NULL, '2026-04-23 14:20:59'),
(3, 4, 4, '2026-04-25', '2026-04-26', '00:00:00', '00:00:00', 'Transit / Herianto', 2, 'ditolak', NULL, '2026-04-24 01:00:49'),
(4, 4, 1, '2026-04-28', '2026-04-30', '10:20:00', '22:20:00', 'Rapat Silatnas', 10, 'disetujui', NULL, '2026-04-24 02:21:36'),
(5, 4, 4, '2026-05-01', '2026-05-02', '00:00:00', '00:00:00', 'transit', 1, 'disetujui', NULL, '2026-04-24 02:23:47'),
(6, 4, 4, '2026-05-08', '2026-05-09', '00:00:00', '00:00:00', 'transit', 1, 'disetujui', NULL, '2026-04-24 02:29:52'),
(7, 4, 4, '2026-05-06', '2026-05-07', '00:00:00', '00:00:00', 'transit', 1, 'ditolak', NULL, '2026-04-24 02:46:44');

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
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `unit`, `no_wa`, `role`, `created_at`) VALUES
(1, 'admin_wkp', '0192023a7bbd73250516f069df18b500', 'Administrator WKP', 'Kantor Pengurus', NULL, 'admin', '2026-04-12 20:58:52'),
(2, 'unit_sekolah', '6ad14ba9986e3615423dfca256d04e3f', 'Budi Setiadi', 'Unit Sekolah Yayasan', NULL, 'user', '2026-04-12 20:58:52'),
(3, 'hamimal', '1a50f204bef29eab587463f40b344dc6', 'Hamimal Mustafa', 'SMH', NULL, 'user', '2026-04-21 11:15:30'),
(4, 'herianto', '1a50f204bef29eab587463f40b344dc6', 'Herianto', 'STIS Hidayatullah Balikpapan', '085345631391', 'user', '2026-04-23 14:19:17');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`ruangan_id`) REFERENCES `ruangan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
