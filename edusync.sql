-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2025 at 08:50 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edusync`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi_absensi_sekolah`
--

CREATE TABLE `absensi_absensi_sekolah` (
  `absensi_id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `waktu_masuk` time DEFAULT NULL,
  `waktu_pulang` time DEFAULT NULL,
  `waktu_masuk_aktual` time DEFAULT NULL,
  `waktu_pulang_aktual` time DEFAULT NULL,
  `status_kehadiran_id` int(11) NOT NULL,
  `status_absensi_masuk_id` int(11) NOT NULL,
  `status_absensi_pulang_id` int(11) NOT NULL,
  `keterangan` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_absensi_sekolah`
--

INSERT INTO `absensi_absensi_sekolah` (`absensi_id`, `siswa_id`, `tanggal`, `waktu_masuk`, `waktu_pulang`, `waktu_masuk_aktual`, `waktu_pulang_aktual`, `status_kehadiran_id`, `status_absensi_masuk_id`, `status_absensi_pulang_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(12, 81, '2025-01-22', '07:00:00', '12:00:00', '07:13:43', '17:00:24', 1, 1, 1, '', '2025-01-22 17:00:24', '2025-01-22 17:00:24'),
(13, 13, '2025-01-25', '07:00:00', '12:00:00', '08:59:01', NULL, 1, 2, 5, '', '2025-01-25 08:59:01', '2025-01-25 08:59:01');

-- --------------------------------------------------------

--
-- Table structure for table `absensi_konfigurasi_waktu`
--

CREATE TABLE `absensi_konfigurasi_waktu` (
  `konfigurasi_waktu_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu') NOT NULL,
  `jam_masuk` time NOT NULL,
  `jam_pulang` time NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_konfigurasi_waktu`
--

INSERT INTO `absensi_konfigurasi_waktu` (`konfigurasi_waktu_id`, `kelas_id`, `hari`, `jam_masuk`, `jam_pulang`, `created_at`, `updated_at`) VALUES
(1, 1, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(2, 1, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(3, 1, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(4, 1, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(5, 1, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(6, 1, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(7, 2, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(8, 2, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(9, 2, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(10, 2, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(11, 2, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(12, 2, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(13, 3, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(14, 3, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(15, 3, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(16, 3, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(17, 3, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(18, 3, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(19, 4, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(20, 4, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(21, 4, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(22, 4, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(23, 4, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(24, 4, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(25, 5, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(26, 5, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(27, 5, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(28, 5, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(29, 5, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(30, 5, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(31, 6, 'Senin', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(32, 6, 'Selasa', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(33, 6, 'Rabu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(34, 6, 'Kamis', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(35, 6, 'Jumat', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54'),
(36, 6, 'Sabtu', '07:00:00', '12:00:00', '2025-01-11 22:29:54', '2025-01-11 22:29:54');

-- --------------------------------------------------------

--
-- Table structure for table `absensi_log_tap`
--

CREATE TABLE `absensi_log_tap` (
  `log_id` varchar(50) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `waktu_tap` datetime NOT NULL,
  `jenis_tap` enum('in','out') NOT NULL,
  `rfid_tag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_log_tap`
--

INSERT INTO `absensi_log_tap` (`log_id`, `siswa_id`, `waktu_tap`, `jenis_tap`, `rfid_tag`) VALUES
('6790c1b86ade9', 81, '2025-01-22 17:00:24', 'out', '4210455791'),
('679445650d896', 13, '2025-01-25 08:59:01', 'in', '1344248033');

-- --------------------------------------------------------

--
-- Table structure for table `absensi_status_absensi`
--

CREATE TABLE `absensi_status_absensi` (
  `status_absensi_id` int(11) NOT NULL,
  `status_absensi` varchar(20) NOT NULL,
  `status_absensi_keterangan` text NOT NULL,
  `status_absensi_warna` varchar(15) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_status_absensi`
--

INSERT INTO `absensi_status_absensi` (`status_absensi_id`, `status_absensi`, `status_absensi_keterangan`, `status_absensi_warna`, `created_at`, `updated_at`) VALUES
(1, 'Tepat Waktu', '', 'success', '2025-01-16 06:19:45', '2025-01-16 06:19:59'),
(2, 'Terlambat', '', 'danger', '2025-01-16 06:19:49', '2025-01-16 06:20:02'),
(3, 'Lebih Awal', '', 'warning', '2025-01-16 06:19:52', '2025-01-16 06:20:04'),
(4, 'Tidak Tap In', '', 'danger', '2025-01-16 06:19:54', '2025-01-16 06:20:06'),
(5, 'Tidak Tap Out', '', 'danger', '2025-01-16 09:01:04', '2025-01-16 09:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `absensi_status_kehadiran`
--

CREATE TABLE `absensi_status_kehadiran` (
  `status_kehadiran_id` int(11) NOT NULL,
  `status_kehadiran` varchar(20) NOT NULL,
  `status_kehadiran_warna` varchar(15) NOT NULL,
  `status_kehadiran_keterangan` text NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi_status_kehadiran`
--

INSERT INTO `absensi_status_kehadiran` (`status_kehadiran_id`, `status_kehadiran`, `status_kehadiran_warna`, `status_kehadiran_keterangan`, `created_date`, `updated_date`) VALUES
(1, 'Hadir', 'success', '', '2025-01-16 02:39:57', '2025-01-16 02:39:57'),
(2, 'Sakit', 'primary', '', '2025-01-16 02:39:57', '2025-01-16 02:39:57'),
(3, 'Izin', 'info', '', '2025-01-16 02:39:57', '2025-01-16 02:39:57'),
(4, 'Tanpa Keterangan', 'danger', '', '2025-01-16 02:39:57', '2025-01-16 02:39:57');

-- --------------------------------------------------------

--
-- Table structure for table `master_jurusan`
--

CREATE TABLE `master_jurusan` (
  `jurusan_id` int(11) NOT NULL,
  `jurusan_nama` varchar(50) NOT NULL,
  `jurusan_kode` varchar(10) NOT NULL,
  `jurusan_keterangan` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_jurusan`
--

INSERT INTO `master_jurusan` (`jurusan_id`, `jurusan_nama`, `jurusan_kode`, `jurusan_keterangan`, `created_at`, `updated_at`) VALUES
(1, 'Teknik Komputer & Jaringan', 'TKJ', '', '2025-01-11 19:50:02', '2025-01-11 22:02:19');

-- --------------------------------------------------------

--
-- Table structure for table `master_kelas`
--

CREATE TABLE `master_kelas` (
  `kelas_id` int(11) NOT NULL,
  `kelas_tingkat` int(11) NOT NULL,
  `kelas_nama` varchar(50) NOT NULL,
  `kelas_keterangan` text NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_kelas`
--

INSERT INTO `master_kelas` (`kelas_id`, `kelas_tingkat`, `kelas_nama`, `kelas_keterangan`, `created_at`, `updated_at`) VALUES
(1, 7, 'VII', '', '2025-01-10 19:27:56', '2025-01-11 22:24:05'),
(2, 8, 'VIII', '', '2025-01-10 19:27:56', '2025-01-11 22:24:10'),
(3, 9, 'IX', '', '2025-01-10 19:27:56', '2025-01-11 22:24:12'),
(4, 10, 'X', '', '2025-01-10 19:27:56', '2025-01-11 22:24:15'),
(5, 11, 'XI', '', '2025-01-10 19:27:56', '2025-01-11 22:24:17'),
(6, 12, 'XII', '', '2025-01-10 19:27:56', '2025-01-11 22:05:33');

-- --------------------------------------------------------

--
-- Table structure for table `master_siswa`
--

CREATE TABLE `master_siswa` (
  `siswa_id` int(11) NOT NULL,
  `kelas_id` int(11) DEFAULT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `nis` varchar(20) DEFAULT NULL,
  `nisn` varchar(10) DEFAULT NULL,
  `jk` enum('p','l') DEFAULT NULL,
  `tempat_lahir` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(50) NOT NULL,
  `rfid_tag` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_siswa`
--

INSERT INTO `master_siswa` (`siswa_id`, `kelas_id`, `nama_siswa`, `nis`, `nisn`, `jk`, `tempat_lahir`, `tanggal_lahir`, `alamat`, `rfid_tag`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'A. Rizky Maulana', '24.25.187', '0129120612', 'l', 'Pandeglang', '2012-02-21', 'Kp. Purbasari', '1356657841', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(2, 1, 'Citra Nur Fadillah', '24.25.188', '0125374250', 'p', 'Bekasi', '2012-02-22', 'Kp. Bangkonol', '1563981824', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(3, 1, 'Erwin Permana', '24.25.189', '0129899074', 'l', 'Pandeglang', '2012-02-23', 'Kp. Pasir Pariuk', '1354217681', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(4, 1, 'Fitratun Nada', '24.25.190', '0113694989', 'p', 'Pandeglang', '2012-02-24', 'Kp. Sidamukti', '1560755712', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(5, 1, 'Fitratun Nida', '24.25.191', '0118722549', 'p', 'Pandeglang', '2012-02-25', 'Kp. Sidamukti', '1347436241', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(6, 1, 'Ibnu Nadzril', '24.25.192', '0115307306', 'l', 'Pandeglang', '2012-02-26', 'Kp. Kadupandak', '1552080624', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(7, 1, 'Muhamad Fathir Rizqi', NULL, NULL, 'l', NULL, NULL, NULL, '1573396480', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(8, 1, 'M. Elfin Suftiana', '24.25.194', '0122758784', 'l', 'Pandeglang', '2012-02-27', 'Kp. Kadomas', '1425431363', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(9, 1, 'M. Kafi Maula\'Abdillah', '24.25.195', '3116396879', 'l', 'Pandeglang', '2012-02-28', 'Kp. Kadubanen', '1418992435', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(10, 1, 'M. Reyfan Shahrusiyam Putra', '24.25.196', '0127420021', 'l', 'Pandeglang', '2012-02-29', 'Kp. Pakalongan ', '1352512977', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(11, 1, 'Nakhla Izzatunnisa', '24.25.197', '0127165287', 'p', 'Pandeglang', '2012-03-01', 'Kp. Pasir Walet', '1416024627', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(12, 1, 'Raffi Ulhak', '24.25.198', '0128731991', 'l', 'Pandeglang', '2012-03-02', 'Kp. Tari Kolot', '1424293171', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(13, 1, 'Ratu Dwifa Nurana', '24.25.199', '0111802790', 'p', 'Pandeglang', '2012-03-03', 'Kp. Andihiang Lebak', '1344248033', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(14, 1, 'Saiful Anwar ', '24.25.200', '3110068149', 'l', 'Pandeglang', '2012-03-04', 'Kp. Ciekek Babakan Karaton', '1555924464', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(15, 1, 'Sani', '24.25.202', '3113748807', 'l', 'Lebak', '2012-03-05', 'Kp. Ledug ', '1347030977', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(16, 1, 'Silpa', '24.25.203', '0116226537', 'p', 'Pandeglang', '2012-03-06', 'Kp. Mangkubumi', '1354823857', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(17, 1, 'St. Azkia Bilkis', '24.25.204', '0125236888', 'p', 'Pandeglang', '2012-03-07', 'Kp. Pabuaran', '1352998113', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(18, 1, 'Siti Aisyah', '24.25.205', '118773663', 'p', 'Pandeglang', '2012-03-08', 'Kp. Sabi Kumtir', '1358431169', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(19, 1, 'Siti Rachel Quaneisha', '24.25.206', '0116350776', 'p', 'Pandeglang', '2012-03-09', 'Kp. Jaliti', '1358466529', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(20, 1, 'Siti Wedal Arum', '24.25.207', '3112881688', 'p', 'Pandeglang', '2012-03-10', 'Kp. Ciekek Babakan Karaton', '1413165379', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(21, 1, 'Tb. Azka Ali', '24.25.208', '0115017335', 'l', 'Pandeglang', '2012-03-11', 'Kp. Pasir Kalapa', '1351616705', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(22, 1, 'Zahwa Anggraeni ', '24.25.209', '0122508467', 'p', 'Pandeglang', '2012-03-12', 'Kp. Beunying', '1422605907', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(23, 2, 'Agisni Aulia Sapitri ', '23.24.152', '3113370746', 'p', 'Pandeglang', '2011-10-05', 'Kp. Kadupandak', '1357241041', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(24, 2, 'Ahmad Munawar Sajali ', '23.24.153', '0111497623', 'l', 'Pandeglang', '2011-04-16', 'Kumalirang', '1356224481', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(25, 2, 'An Nazwa Baehaki ', '23.24.154', '0116286608', 'l', 'Lebak', '2011-01-22', 'Kp. Rumbut', '1574380800', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(26, 2, 'Fariz Abdu Raziq', '23.24.156', '3127286338', 'l', 'Pandeglang', '2012-01-09', 'Kp. Pasir Kalapa', '1549561328', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(27, 2, 'Fithrotul Muzayyanah', '23.24.157', '0104931112', 'p', 'Pandeglang', '2010-09-06', 'Kp. Kumalirang ', '1357604561', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(28, 2, 'Ida Lailatul Munawaroh ', '23.24.158', '3116789856', 'p', 'Cilacap', '2011-08-01', 'Kp. Pelag ', '1352742369', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(29, 2, 'Lina Rohimah', '23.24.160', '3115574965', 'p', 'Pandeglang', '2011-10-08', 'Kp. Pasir Kalapa', '1413566515', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(30, 2, 'M. Lutfi Alfansyah', '23.24.161', '0112284637', 'l', 'Pandeglang', '2011-09-02', 'Kp. Pasir Kalapa', '1349985249', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(31, 2, 'M. Miftahudin ', '23.24.162', '0115189299', 'l', 'Pandeglang', '2011-04-24', 'Kp. Cikole', '1351281585', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(32, 2, 'Moch Rafki Mubarok ', '23.24.164', '3117056214', 'l', 'Pandeglang', '2011-01-18', 'Kp. Pasir Kalapa ', '1565794560', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(33, 2, 'Muhamad Rifki', '23.24.168', '3110593462', 'l', 'Pandeglang', '2011-05-11', 'Kp. Pasir Kalapa', '1344310497', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(34, 2, 'Muhamad Zulfa', '23.24.169', '3118322524', 'l', 'Pandeglang', '2011-06-23', 'Kp. Cicalung ', '1548855024', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(35, 2, 'Nun Agis Kuraisin ', '23.24.171', '0109718385', 'p', 'Pandeglang', '2010-10-25', 'Kp. Kumalirang ', '1414426675', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(36, 2, 'Nurfikri ', '23.24.172', '0106450875', 'l', 'Pandeglang', '2010-10-31', 'Kp. Batu Karut ', '1345956065', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(37, 2, 'Rani Apriani', '23.24.173', '3111816419', 'p', 'Pandeglang', '2011-04-22', 'Kp. Pasir Kalapa ', '1353226721', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(38, 2, 'Ridho', '23.24.174', '0104865544', 'l', 'Lebak', '2010-06-23', 'Kp. Pasir Kalapa ', '1346816961', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(39, 2, 'Sahrul Rizal ', '23.24.175', '3119866878', 'l', 'Pandeglang', '2011-02-08', 'Kp. Paniis Baru ', '1348253889', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(40, 2, 'Septi Ramadani', '23.24.176', '3106534397', 'p', 'Pandeglang', '2010-09-08', 'Kp. Pasir Kalapa ', '1349783265', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(41, 2, 'Sindi Anggraeni', '23.24.177', '0111784903', 'p', 'Pandeglang', '2011-02-18', 'Kp. Pasir Kalapa ', '1357342673', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(42, 2, 'Siti Hanifah', '23.24.178', '0115343991', 'p', 'Pandeglang', '2011-04-12', 'Kp. Sabitangtu ', '1351957473', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(43, 2, 'Siti Maya Handayani ', '23.24.180', '0105002248', 'p', 'Pandeglang', '2010-10-06', 'Kp. Cikapais', '1413663027', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(44, 2, 'Siti Safnatussaniah', '23.24.181', '0105235320', 'p', 'Pandeglang', '2010-10-01', 'Kp. Pasir Kalapa ', '1342530017', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(45, 2, 'Siti Zahira Septia', '23.24.182', '0111207468', 'p', 'Pandeglang', '2011-07-25', 'Kp. Kalanganyar ', '1559704304', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(46, 2, 'Zahiratul Aprilia', '23.24.183', '0119290991', 'p', 'Jakarta', '2011-04-24', NULL, '1549165296', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(47, 2, 'Zahra Putri Maulida ', '23.24.184', '3119509837', 'p', 'Pandeglang', '2011-02-04', NULL, '1352588241', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(48, 2, 'Patimah Nurpidiah', NULL, NULL, 'p', NULL, NULL, NULL, '1412004931', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(49, 3, 'A. Albian Fahrezi ', NULL, NULL, 'l', NULL, NULL, NULL, NULL, '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(50, 3, 'Aira Pelia ', '22.23.133', '0096267290', 'p', 'Pandeglang', '2009-07-09', 'Kp. Pasir Kalapa', '1348913121', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(51, 3, 'Azzahra Putriani Muhtian ', '22.23.134', '0098416838', 'p', 'Tangerang', '2009-11-26', 'Kp. Kadulisung Keboncau', '1560679936', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(52, 3, 'Fadira ', '23.24.180', '0105002248', 'p', 'Pandeglang', '2010-07-30', 'Kp. Sabitangtu', '1410511411', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(53, 3, 'Lia Aulia Sari ', '22.23.138', '0104664916', 'p', 'Pandeglang', '2010-07-15', 'Kp.Kebon ', '1417094211', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(54, 3, 'M. Ahda Maulana ', '22.23.139', '0093926245', 'l', 'Pandeglang', '2009-12-21', 'Kp.Kadukempol', '1411253811', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(55, 3, 'Maria Ulfah ', '22.23.140', '0093284576', 'p', 'Pandeglang', '2009-09-17', 'Kp.Pasir Kalapa ', '1554874096', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(56, 3, 'Melda', '22.23.141', '0108131298', 'p', 'Cilegon', '2010-06-25', 'Linkungan Sukasari', '1413389395', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(57, 3, 'Muhamad Wildan Maulana', '22.23.142', '0102023247', 'l', 'Pandeglang', '2010-03-19', 'Kp. Kadugajah', '1352356801', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(58, 3, 'Raysard Hady ', '22.23.144', NULL, 'l', 'Cirebon', '2009-05-14', 'Dusun 01', '1345427153', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(59, 3, 'Realpatwa Pirdaus ', '22.23.145', '0107569567', 'l', 'Pandeglang', '2010-11-08', 'Kp.Kadugajah', '1564879360', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(60, 3, 'Riza Umami ', '22.23.146', '0097712711', 'p', 'Pandeglang', '2009-10-25', 'Kp.Pasir Kalapa ', '1350062545', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(61, 3, 'Abdul Malik', '22.23.147', '0096633138', 'l', 'Karawang', '2009-04-29', 'Blokkaraton', '1423024467', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(62, 3, 'Ahmad Fadliyudinal-Zabar', '23.24.151', '3104341498', 'l', 'Pandeglang', '2010-04-04', 'Kp. Nangka Beureum', '1347914977', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(63, 3, 'Asyifa Zahra Ramadani', '23.24.185', '0104268226', 'p', 'Pandeglang', '2010-08-13', NULL, '1344610273', '2025-01-11 22:29:54', '2025-01-11 22:29:54', NULL),
(64, 5, 'Linggar Al Latifah', '23.24.024', '0099174423', 'p', 'Tangerang', '2007-01-18', 'Kp. Pabuaran', '1342392273', NULL, NULL, NULL),
(65, 6, 'Pahruroji Aprian', '22.23.014', '0079176188', 'l', 'Lebak', '2007-04-07', 'Kp. Cibugang Pasir', '1410793539', NULL, NULL, NULL),
(66, 5, 'Hamdatun Nisa', '23.24.022', '0085561638', 'p', 'Pandeglang', '2008-01-15', 'Kp. Kebon', '1424960323', NULL, NULL, NULL),
(67, 5, 'Siti Ilfi Zakiyyatunnaja', '23.24.032', '0086166804', 'p', 'Pandeglang', '2008-07-28', 'Kp. Pasekon', '1357328097', NULL, NULL, NULL),
(68, 5, 'Wafa Aula Kholifah', '23.24.031', '0087253296', 'p', 'Pandeglang', '2008-11-15', 'Kp. Sindang Adipati', '1357095393', NULL, NULL, NULL),
(69, 5, 'Fathur Wazhi', '23.24.021', '0078080864', 'l', 'Pandeglang', '2007-10-13', 'Kp. Pasir Kalapa ', '1350702017', NULL, NULL, NULL),
(70, 5, 'M. Abdan Abadi', '23.24.025', '0077857818', 'l', 'Lebak', '2007-10-24', 'Kp. Rumbut', '1418485315', NULL, NULL, NULL),
(71, 5, 'Siti Badriah', '23.24.029', '0088820513', 'p', 'Lebak', '2008-06-27', 'Kp. Sempuren ', '1357565633', NULL, NULL, NULL),
(72, 5, 'Ahmad Salim Murtado', '23.24.016', '0088281184', 'l', 'Pandeglang', '2008-12-20', 'Kp. Pasir Kalapa ', '1357707217', NULL, NULL, NULL),
(73, 5, 'Andika', '23.24.017', '0084759318', 'l', 'Pandeglang', '2008-05-25', 'Kp. Pasir Pariuk ', '1557859056', NULL, NULL, NULL),
(74, 5, 'Silva Masfufah', '23.24.028', '0084888820', 'p', 'Pandeglang', '2008-04-07', 'Kp. Pasir Kalapa ', '1569546752', NULL, NULL, NULL),
(75, 5, 'Jepri Aminudin', '23.24.023', '0075671364', 'l', 'Pandeglang', '2007-12-31', 'Kp. Pasir Kalapa ', '1344760001', NULL, NULL, NULL),
(76, 5, 'Nailul Muna', '23.24.026', '0084159681', 'p', 'Pandeglang', '2008-01-21', 'Kp. Kumalirang', '1563460352', NULL, NULL, NULL),
(77, 5, 'Danu', '23.24.020', '0079402260', 'l', 'Pandeglang', '2007-05-02', 'Kp. Mangkubumi ', '1552424432', NULL, NULL, NULL),
(78, 5, 'Arilliansyah', '23.24.028', '0075828230', 'l', 'Pandeglang', '2005-04-05', 'Kp. Ciboboko', '1355010769', NULL, NULL, NULL),
(79, 5, 'Fatir Erpansyah', '23.24.033', '0083327157', 'l', 'Lebak', '2008-07-22', 'Kp. Rumbut', '1348619201', NULL, NULL, NULL),
(80, 5, 'Perdi Duhriansyah', '23.24.027', '0086315785', 'l', 'Pandeglang', '2008-05-26', 'Kp. Sindang', '1345445345', '2025-01-25 14:27:44', '2025-01-25 14:27:44', NULL),
(81, 5, 'Siti Sulistiani', '23.24.030', '0081769339', 'p', 'Pandeglang', '2008-04-17', 'Kp. Sampora', '1418475843', '2025-01-25 14:27:44', '2025-01-25 14:27:44', NULL),
(82, 4, 'Munajar Alvian Prasetya', '24.25.038', '0094892260', 'l', 'Pandeglang', '2009-11-03', 'Perum Teras', '1419823155', '2025-01-25 14:27:44', '2025-01-25 14:27:44', NULL),
(83, 4, 'Vina Zahrotu Ni\'mah', '24.25.037', '0102703114', 'p', 'Bogor', '2010-04-24', 'Kp. Juga Raya', '1552137712', '2025-01-25 14:27:44', '2025-01-25 14:27:44', NULL),
(84, 4, 'Reina Raudanisa A.', '24.25.036', '0095106535', 'p', 'Lampung', '2009-02-26', 'Kp. Pasir Walet', '1348060353', '2025-01-25 14:35:35', '2025-01-25 14:35:35', NULL),
(85, 4, 'Fatimah Ayu Rayhana P.', '24.25.034', '0094365575', 'p', 'Suban', '2009-03-23', 'Suban', '1351606977', '2025-01-25 14:37:38', '2025-01-25 14:37:38', NULL),
(86, 4, 'Nurul Aulia', '24.25.035', '0099681140', 'p', 'Pandeglang', '2009-12-17', 'Kp. Kadulisung', '1409363283', '2025-01-25 14:39:17', '2025-01-25 14:39:17', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi_absensi_sekolah`
--
ALTER TABLE `absensi_absensi_sekolah`
  ADD PRIMARY KEY (`absensi_id`);

--
-- Indexes for table `absensi_konfigurasi_waktu`
--
ALTER TABLE `absensi_konfigurasi_waktu`
  ADD PRIMARY KEY (`konfigurasi_waktu_id`);

--
-- Indexes for table `absensi_log_tap`
--
ALTER TABLE `absensi_log_tap`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `absensi_status_absensi`
--
ALTER TABLE `absensi_status_absensi`
  ADD PRIMARY KEY (`status_absensi_id`);

--
-- Indexes for table `absensi_status_kehadiran`
--
ALTER TABLE `absensi_status_kehadiran`
  ADD PRIMARY KEY (`status_kehadiran_id`);

--
-- Indexes for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  ADD PRIMARY KEY (`jurusan_id`);

--
-- Indexes for table `master_kelas`
--
ALTER TABLE `master_kelas`
  ADD PRIMARY KEY (`kelas_id`);

--
-- Indexes for table `master_siswa`
--
ALTER TABLE `master_siswa`
  ADD PRIMARY KEY (`siswa_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi_absensi_sekolah`
--
ALTER TABLE `absensi_absensi_sekolah`
  MODIFY `absensi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `absensi_konfigurasi_waktu`
--
ALTER TABLE `absensi_konfigurasi_waktu`
  MODIFY `konfigurasi_waktu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `absensi_status_absensi`
--
ALTER TABLE `absensi_status_absensi`
  MODIFY `status_absensi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `absensi_status_kehadiran`
--
ALTER TABLE `absensi_status_kehadiran`
  MODIFY `status_kehadiran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `master_jurusan`
--
ALTER TABLE `master_jurusan`
  MODIFY `jurusan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `master_kelas`
--
ALTER TABLE `master_kelas`
  MODIFY `kelas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `master_siswa`
--
ALTER TABLE `master_siswa`
  MODIFY `siswa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
