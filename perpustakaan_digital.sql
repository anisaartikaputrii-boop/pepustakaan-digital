-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2025 at 09:18 AM
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
-- Database: `perpustakaan_digital`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id_anggota` int(11) NOT NULL,
  `nomor_anggota` varchar(20) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `tanggal_daftar` date NOT NULL,
  `foto_anggota` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id_anggota`, `nomor_anggota`, `nama_lengkap`, `jenis_kelamin`, `alamat`, `no_telepon`, `email`, `tanggal_daftar`, `foto_anggota`, `status`) VALUES
(1, 'A001', 'Budi Santoso', 'L', 'Jl. Veteran No. 10, Surabaya', '081234567890', 'budi@email.com', '2025-11-01', '69194ffa1233e_1763266554.jpg', 'aktif'),
(2, 'A002', 'Siti Nurhaliza', 'P', 'Jl. Pahlawan No. 25, Surabaya', '081234567891', 'siti@email.com', '2025-11-04', '69194f1a4c4a3_1763266330.jpg', 'aktif'),
(3, 'A003', 'Ahmad Yani', 'L', 'Jl. Pemuda No. 5, Surabaya', '081234567892', 'ahmad@email.com', '2025-11-08', '69194e5712255_1763266135.jpg', 'aktif'),
(4, 'A004', 'Safira Putri', 'P', 'Jl. Indrapura No. 15, Surabaya', '081234567893', 'safira@email.com', '2025-11-11', '69196fa4a5a58_1763274660.jpg', 'aktif'),
(5, 'A005', 'Raka Aditya', 'L', 'Jl. Dupak No. 22, Surabaya', '081234567894', 'raka@email.com', '2025-11-14', '691975aa83d6e_1763276202.jpg', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `pengarang` varchar(100) NOT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `jumlah_halaman` int(11) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `cover_buku` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `isbn`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `id_kategori`, `jumlah_halaman`, `stok`, `cover_buku`, `deskripsi`, `created_at`) VALUES
(2, '978-979-9731-23-4', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Lentera Dipantara', '2015', 1, 538, 2, '69198b2c8826d_1763281708.jpg', 'Tetralogi Buru bagian pertama', '2025-11-16 01:21:36'),
(3, '978-623-0100-61-1', 'Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', 'Abdul Kadir', 'Penerbit Andi', '2020', 3, 864, 1, '691988085289c_1763280904.jpg', 'Panduan lengkap pemrograman web', '2025-11-16 01:21:36'),
(4, '979-16-0012-0', 'Sejarah Indonesia Modern 1200-2008', 'M.C. Ricklefs', 'Kencana', '2008', 4, 865, 2, '69198600e26fd_1763280384.jpg', 'Sejarah Indonesia dari 1200-2008.', '2025-11-16 01:21:36'),
(5, '978-602-0528-54-0', 'Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', 'Mark Manson', 'Gramedia Widiasarana Indonesia', '2022', 2, 256, 3, '69198dba87292_1763282362.jpg', 'Panduan praktis mengelola emosi dan fokus pada hal penting hidup.', '2025-11-16 08:39:22'),
(6, '978-602-6008-56-5', 'Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp; Tata Bahasa Indonesia', 'Rahma Fitri &amp; Tim Ilmu Educentre', 'Ilmu Media', '2017', 6, 242, 3, '6919904b01dd4_1763283019.png', 'Panduan lengkap memahami ejaan dan tata bahasa', '2025-11-16 08:50:19'),
(7, '978-602-2497-30-1', 'Ensiklopedia Sains', 'Usborne', 'Bhuana Ilmu Populer', '2014', 5, 456, 2, '6919924f4fb73_1763283535.jpg', 'Ringkasan ilmu sains lengkap untuk pemahaman dasar.', '2025-11-16 08:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'Fiksi', 'Novel dan cerita fiksi'),
(2, 'Non-Fiksi', 'Buku pengetahuan umum'),
(3, 'Teknologi', 'Buku tentang teknologi dan komputer'),
(4, 'Sejarah', 'Buku sejarah dan biografi'),
(5, 'Sains', 'Buku ilmu pengetahuan'),
(6, 'Pendidikan', 'Buku pembelajaran dan pendidikan');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `detail` text DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `detail`, `waktu`) VALUES
(6, 1, 'Logout', 'User keluar dari sistem', '2025-11-16 03:26:40'),
(7, 1, 'Login', 'User berhasil login', '2025-11-16 03:28:29'),
(8, 1, 'Logout', 'User keluar dari sistem', '2025-11-16 03:52:16'),
(9, 2, 'Login', 'User berhasil login', '2025-11-16 03:54:08'),
(10, 2, 'Login', 'User berhasil login', '2025-11-16 03:58:34'),
(11, 2, 'Update Anggota', 'Mengupdate anggota: Ahmad Yani', '2025-11-16 04:08:55'),
(12, 2, 'Update Anggota', 'Mengupdate anggota: Siti Nurhaliza', '2025-11-16 04:12:10'),
(13, 2, 'Update Anggota', 'Mengupdate anggota: Budi Santoso', '2025-11-16 04:15:28'),
(14, 2, 'Update Anggota', 'Mengupdate anggota: Budi Santoso', '2025-11-16 04:15:54'),
(15, 2, 'Tambah Anggota', 'Menambahkan anggota: Safira Putri', '2025-11-16 04:24:33'),
(16, 2, 'Tambah Anggota', 'Menambahkan anggota: Raka Aditya', '2025-11-16 04:45:22'),
(17, 2, 'Update Anggota', 'Mengupdate anggota: Safira Putri', '2025-11-16 04:45:37'),
(18, 2, 'Tambah Anggota', 'Menambahkan anggota: Kesya Almira', '2025-11-16 04:49:50'),
(19, 2, 'Login', 'User berhasil login', '2025-11-16 05:32:42'),
(20, 2, 'Tambah Anggota', 'Menambahkan anggota: Davin Putra', '2025-11-16 05:39:58'),
(21, 2, 'Tambah Anggota', 'Menambahkan anggota: Nayara Kirana', '2025-11-16 05:42:36'),
(22, 2, 'Tambah Anggota', 'Menambahkan anggota: Farel Ramadhan', '2025-11-16 06:09:40'),
(23, 2, 'Tambah Anggota', 'Menambahkan anggota: Felisha Zahra', '2025-11-16 06:15:03'),
(24, 2, 'Tambah Anggota', 'Menambahkan anggota: Zidan Alvaro', '2025-11-16 06:19:28'),
(25, 2, 'Tambah Anggota', 'Menambahkan anggota: Isabella Putri', '2025-11-16 06:28:20'),
(26, 2, 'Update Anggota', 'Mengupdate anggota: Safira Putri', '2025-11-16 06:31:00'),
(27, 2, 'Update Anggota', 'Mengupdate anggota: Kesya Almira', '2025-11-16 06:32:56'),
(28, 2, 'Update Anggota', 'Mengupdate anggota: Nayara Kirana', '2025-11-16 06:35:48'),
(29, 2, 'Update Anggota', 'Mengupdate anggota: Felisha Zahra', '2025-11-16 06:37:39'),
(30, 2, 'Update Anggota', 'Mengupdate anggota: Isabella Putri', '2025-11-16 06:43:50'),
(31, 2, 'Update Anggota', 'Mengupdate anggota: Felisha Zahra', '2025-11-16 06:46:59'),
(32, 2, 'Update Anggota', 'Mengupdate anggota: Zidan Alvaro', '2025-11-16 06:49:19'),
(33, 2, 'Update Anggota', 'Mengupdate anggota: Raka Aditya', '2025-11-16 06:56:42'),
(34, 2, 'Update Anggota', 'Mengupdate anggota: Davin Putra', '2025-11-16 07:03:49'),
(35, 2, 'Update Anggota', 'Mengupdate anggota: Farel Ramadhan', '2025-11-16 07:36:44'),
(36, 2, 'Update Anggota', 'Mengupdate anggota: Davin Putra', '2025-11-16 07:37:35'),
(37, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 08:04:28'),
(38, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 08:06:24'),
(39, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 08:11:37'),
(40, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 08:11:43'),
(41, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 08:11:55'),
(42, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 08:11:59'),
(43, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 08:15:04'),
(44, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 08:23:39'),
(45, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 08:27:42'),
(46, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 08:28:28'),
(47, 2, 'Tambah Buku', 'Menambahkan buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 08:39:22'),
(48, 2, 'Tambah Buku', 'Menambahkan buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp; Tata Bahasa Indonesia', '2025-11-16 08:50:19'),
(49, 2, 'Update Buku', 'Mengupdate buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 08:52:32'),
(50, 2, 'Tambah Buku', 'Menambahkan buku: Ensiklopedia Sains', '2025-11-16 08:58:55'),
(52, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-16 11:47:38'),
(53, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 1', '2025-11-16 11:49:30'),
(54, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:49:46'),
(55, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 11:54:51'),
(56, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:54:51'),
(57, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 2', '2025-11-16 11:55:05'),
(58, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:55:05'),
(59, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 2', '2025-11-16 11:56:27'),
(60, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:56:27'),
(61, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 2', '2025-11-16 11:56:35'),
(62, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:56:35'),
(63, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 11:56:40'),
(64, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 1', '2025-11-16 11:56:40'),
(65, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 3', '2025-11-16 11:57:03'),
(66, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 2', '2025-11-16 11:57:07'),
(67, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 11:58:02'),
(68, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 2', '2025-11-16 11:58:02'),
(69, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 4', '2025-11-16 11:58:06'),
(70, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 2', '2025-11-16 11:58:06'),
(71, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 4', '2025-11-16 11:58:42'),
(72, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 2', '2025-11-16 11:58:42'),
(73, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 11:58:49'),
(74, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 2', '2025-11-16 11:58:49'),
(75, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 5', '2025-11-16 11:58:56'),
(76, 2, 'Update Buku', 'Mengupdate buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 12:00:16'),
(77, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 12:00:26'),
(78, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 12:00:43'),
(79, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 12:01:01'),
(80, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp;amp; Tata Bahasa Indonesia', '2025-11-16 12:01:41'),
(81, 2, 'Update Anggota', 'Mengupdate anggota: Budi Santoso', '2025-11-16 12:02:45'),
(82, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-16 12:04:44'),
(83, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 4', '2025-11-16 12:04:56'),
(84, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 5', '2025-11-16 12:05:18'),
(85, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 4', '2025-11-16 12:05:18'),
(86, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 6', '2025-11-16 12:23:23'),
(87, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 7', '2025-11-16 12:23:30'),
(88, 2, 'Login', 'User berhasil login', '2025-11-16 21:40:14'),
(89, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 21:41:54'),
(90, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-16 21:50:31'),
(91, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 9', '2025-11-16 21:51:17'),
(92, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 8', '2025-11-16 21:53:35'),
(93, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 8', '2025-11-16 21:53:49'),
(94, 2, 'Update Buku', 'Mengupdate buku: Ensiklopedia Sains', '2025-11-16 21:55:20'),
(95, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp;amp;amp; Tata Bahasa Indonesia', '2025-11-16 21:55:32'),
(96, 2, 'Update Buku', 'Mengupdate buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 21:55:47'),
(97, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 21:55:57'),
(98, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 21:56:07'),
(99, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 21:56:16'),
(100, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 4', '2025-11-16 21:58:16'),
(101, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 10', '2025-11-16 21:58:32'),
(102, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 5', '2025-11-16 22:01:05'),
(103, 2, 'Update Buku', 'Mengupdate buku: Ensiklopedia Sains', '2025-11-16 22:02:13'),
(104, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp;amp;amp;amp; Tata Bahasa Indonesia', '2025-11-16 22:02:29'),
(105, 2, 'Update Buku', 'Mengupdate buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 22:02:45'),
(106, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 22:02:59'),
(107, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 22:03:08'),
(108, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 22:03:16'),
(109, 2, 'Update Buku', 'Mengupdate buku: Ensiklopedia Sains', '2025-11-16 22:07:56'),
(110, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp;amp;amp;amp;amp; Tata Bahasa Indonesia', '2025-11-16 22:08:27'),
(111, 2, 'Update Buku', 'Mengupdate buku: Ensiklopedia Sains', '2025-11-16 22:08:37'),
(112, 2, 'Update Buku', 'Mengupdate buku: Ensiklopedia Sains', '2025-11-16 22:09:06'),
(113, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp;amp;amp;amp;amp;amp; Tata Bahasa Indonesia', '2025-11-16 22:09:13'),
(114, 2, 'Update Buku', 'Mengupdate buku: Sebuah Seni untuk Bersikap Bodo Amat (edisi handy)', '2025-11-16 22:09:26'),
(115, 2, 'Update Buku', 'Mengupdate buku: Bumi Manusia', '2025-11-16 22:09:34'),
(116, 2, 'Update Buku', 'Mengupdate buku: Dasar Pemrograman Web Dinamis Menggunakan PHP Edisi Revisi Kedua', '2025-11-16 22:09:44'),
(117, 2, 'Update Buku', 'Mengupdate buku: Sejarah Indonesia Modern 1200-2008', '2025-11-16 22:09:52'),
(118, 2, 'Logout', 'User keluar dari sistem', '2025-11-16 22:09:55'),
(119, 2, 'Login', 'User berhasil login', '2025-11-16 22:11:16'),
(120, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-16 22:13:27'),
(121, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-16 22:16:00'),
(122, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-16 22:18:10'),
(123, 2, 'Logout', 'User keluar dari sistem', '2025-11-16 22:19:30'),
(124, 2, 'Login', 'User berhasil login', '2025-11-17 03:47:31'),
(125, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 13', '2025-11-17 04:21:02'),
(126, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 11', '2025-11-17 04:21:07'),
(127, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 12', '2025-11-17 04:21:10'),
(128, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 14', '2025-11-17 04:21:14'),
(129, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 10', '2025-11-17 04:21:18'),
(130, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:24:34'),
(131, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 10', '2025-11-17 04:24:34'),
(132, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:26:03'),
(133, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 10', '2025-11-17 04:26:03'),
(134, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 16', '2025-11-17 04:26:10'),
(135, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-17 04:26:39'),
(136, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 16', '2025-11-17 04:26:39'),
(137, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 17', '2025-11-17 04:26:43'),
(138, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 04:26:54'),
(139, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 17', '2025-11-17 04:26:54'),
(140, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 18', '2025-11-17 04:26:57'),
(141, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 17', '2025-11-17 04:26:57'),
(142, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 04:27:04'),
(143, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 17', '2025-11-17 04:27:04'),
(144, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 18', '2025-11-17 04:27:12'),
(145, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 19', '2025-11-17 04:27:15'),
(146, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:28:33'),
(147, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 20', '2025-11-17 04:28:38'),
(148, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 15', '2025-11-17 04:33:31'),
(149, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:33:51'),
(150, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:34:08'),
(151, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:34:08'),
(152, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 21 pada tanggal 2025-11-05', '2025-11-17 04:35:05'),
(153, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:35:05'),
(154, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:35:21'),
(155, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:35:21'),
(156, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 22 pada tanggal 2025-11-17', '2025-11-17 04:35:25'),
(157, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:35:25'),
(158, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:35:34'),
(159, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 20', '2025-11-17 04:35:34'),
(160, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 22', '2025-11-17 04:35:55'),
(161, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 23', '2025-11-17 04:35:58'),
(162, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 21', '2025-11-17 04:37:58'),
(163, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 04:39:00'),
(164, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 21', '2025-11-17 04:39:00'),
(165, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 24 pada tanggal 2025-08-12', '2025-11-17 04:39:22'),
(166, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 21', '2025-11-17 04:39:22'),
(167, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 24 pada tanggal 2025-08-12', '2025-11-17 04:41:04'),
(168, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-17 04:42:55'),
(169, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 25 pada tanggal 2025-08-23', '2025-11-17 04:43:34'),
(170, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 5', '2025-11-17 04:45:14'),
(171, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 5', '2025-11-17 04:46:24'),
(172, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 27', '2025-11-17 04:46:34'),
(173, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 26 pada tanggal 2025-08-14', '2025-11-17 04:47:53'),
(174, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 26 pada tanggal 2025-08-14', '2025-11-17 04:50:16'),
(175, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 04:51:58'),
(176, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 28 pada tanggal 2025-09-03', '2025-11-17 04:52:24'),
(177, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 28', '2025-11-17 05:07:33'),
(178, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 25', '2025-11-17 05:07:37'),
(179, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 26', '2025-11-17 05:07:41'),
(180, 2, 'Hapus Peminjaman', 'Menghapus peminjaman ID: 24', '2025-11-17 05:07:44'),
(181, 2, 'Login', 'User berhasil login', '2025-11-17 06:21:09'),
(182, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp; Tata Bahasa Indonesia', '2025-11-17 06:22:27'),
(183, 2, 'Update Buku', 'Mengupdate buku: Buku Pembahasan Terlengkap PUEBI: Pedoman Umum Ejaan Bahasa Indonesia &amp;amp; Tata Bahasa Indonesia', '2025-11-17 06:34:55'),
(184, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 06:35:26'),
(185, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 29 pada tanggal 2025-11-17', '2025-11-17 06:35:41'),
(186, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 6', '2025-11-17 06:36:54'),
(187, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 30 pada tanggal 2025-11-11', '2025-11-17 06:37:15'),
(188, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 06:38:34'),
(189, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 31 pada tanggal 2025-11-13', '2025-11-17 06:39:00'),
(190, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 5', '2025-11-17 06:40:14'),
(191, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 2', '2025-11-17 06:41:12'),
(192, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 06:44:14'),
(193, 2, 'Logout', 'User keluar dari sistem', '2025-11-17 07:12:09'),
(194, 2, 'Login', 'User berhasil login', '2025-11-17 07:12:12'),
(195, 2, 'Login', 'User berhasil login', '2025-11-17 09:49:04'),
(196, 2, 'Login', 'User berhasil login', '2025-11-17 13:54:56'),
(197, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 4', '2025-11-17 14:25:11'),
(198, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 14:28:17'),
(199, 2, 'Login', 'User berhasil login', '2025-11-17 22:49:32'),
(200, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 7', '2025-11-17 22:51:33'),
(201, 2, 'Peminjaman Buku', 'Memproses peminjaman buku ID: 3', '2025-11-17 22:52:52'),
(202, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 32 pada tanggal 2025-11-14', '2025-11-17 22:53:50'),
(203, 2, 'Tambah Review', 'Menambahkan review untuk buku ID: 4', '2025-11-17 22:55:52'),
(204, 2, 'Reservasi Buku', 'Reservasi buku ID: 3 untuk anggota ID: 4', '2025-11-17 22:56:56'),
(205, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 37 pada tanggal 2025-11-16', '2025-11-17 22:58:10'),
(206, 2, 'Login', 'User berhasil login', '2025-11-18 06:43:14'),
(207, 2, 'Pengembalian Buku', 'Memproses pengembalian ID: 36 pada tanggal 2025-11-18', '2025-11-18 06:44:01'),
(208, 2, 'Tambah Review', 'Menambahkan review untuk buku ID: 7', '2025-11-18 06:44:55'),
(209, 2, 'Login', 'User berhasil login', '2025-11-18 06:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `tanggal_dikembalikan` date DEFAULT NULL,
  `denda` decimal(10,2) DEFAULT 0.00,
  `status` enum('dipinjam','dikembalikan','terlambat') DEFAULT 'dipinjam',
  `id_petugas` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali`, `tanggal_dikembalikan`, `denda`, `status`, `id_petugas`) VALUES
(29, 1, 3, '2025-11-01', '2025-11-08', '2025-11-17', 45000.00, 'dikembalikan', 2),
(30, 2, 6, '2025-11-04', '2025-11-11', '2025-11-11', 0.00, 'dikembalikan', 2),
(31, 3, 7, '2025-11-08', '2025-11-15', '2025-11-13', 0.00, 'dikembalikan', 2),
(32, 4, 5, '2025-11-11', '2025-11-18', '2025-11-14', 0.00, 'dikembalikan', 2),
(33, 5, 2, '2025-11-14', '2025-11-21', NULL, 0.00, 'dipinjam', 2),
(34, 1, 7, '2025-11-02', '2025-11-09', NULL, 0.00, 'terlambat', 2),
(35, 1, 4, '2025-11-05', '2025-11-12', NULL, 0.00, 'terlambat', 2),
(36, 2, 3, '2025-11-11', '2025-11-18', '2025-11-18', 0.00, 'dikembalikan', 2),
(37, 2, 7, '2025-11-13', '2025-11-20', '2025-11-16', 0.00, 'dikembalikan', 2),
(38, 3, 3, '2025-11-13', '2025-11-20', NULL, 0.00, 'dipinjam', 2);

-- --------------------------------------------------------

--
-- Table structure for table `reservasi`
--

CREATE TABLE `reservasi` (
  `id_reservasi` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `tanggal_reservasi` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('menunggu','diproses','dibatalkan') DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservasi`
--

INSERT INTO `reservasi` (`id_reservasi`, `id_anggota`, `id_buku`, `tanggal_reservasi`, `status`) VALUES
(1, 4, 3, '2025-11-17 22:56:56', 'menunggu');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `id_anggota` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `tanggal_review` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id_review`, `id_buku`, `id_anggota`, `rating`, `komentar`, `tanggal_review`) VALUES
(1, 4, 1, 4, 'bagus', '2025-11-17 22:55:52'),
(2, 7, 3, 2, 'b', '2025-11-18 06:44:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','petugas') DEFAULT 'petugas',
  `foto_profile` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `foto_profile`, `created_at`) VALUES
(1, 'admin', 'admin123', 'Administrator', 'admin', NULL, '2025-11-16 01:20:45'),
(2, 'petugas1', 'petugas123', 'Petugas Perpustakaan', 'petugas', NULL, '2025-11-16 01:20:45');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_anggota_aktif`
-- (See below for the actual view)
--
CREATE TABLE `view_anggota_aktif` (
`id_anggota` int(11)
,`nomor_anggota` varchar(20)
,`nama_lengkap` varchar(100)
,`jenis_kelamin` enum('L','P')
,`alamat` text
,`no_telepon` varchar(15)
,`email` varchar(100)
,`tanggal_daftar` date
,`foto_anggota` varchar(255)
,`status` enum('aktif','nonaktif')
,`total_peminjaman` bigint(21)
,`sedang_dipinjam` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_buku_populer`
-- (See below for the actual view)
--
CREATE TABLE `view_buku_populer` (
`id_buku` int(11)
,`judul` varchar(200)
,`pengarang` varchar(100)
,`cover_buku` varchar(255)
,`nama_kategori` varchar(50)
,`total_dipinjam` bigint(21)
,`rata_rata_rating` decimal(14,4)
,`jumlah_review` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_detail_peminjaman`
-- (See below for the actual view)
--
CREATE TABLE `view_detail_peminjaman` (
`id_peminjaman` int(11)
,`tanggal_pinjam` date
,`tanggal_kembali` date
,`tanggal_dikembalikan` date
,`status` enum('dipinjam','dikembalikan','terlambat')
,`denda` decimal(10,2)
,`nomor_anggota` varchar(20)
,`nama_anggota` varchar(100)
,`no_telepon` varchar(15)
,`judul_buku` varchar(200)
,`isbn` varchar(20)
,`pengarang` varchar(100)
,`nama_petugas` varchar(100)
,`hari_terlambat` int(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_statistik_kategori`
-- (See below for the actual view)
--
CREATE TABLE `view_statistik_kategori` (
`nama_kategori` varchar(50)
,`jumlah_buku` bigint(21)
,`total_stok` decimal(32,0)
,`rata_rata_rating` decimal(14,4)
);

-- --------------------------------------------------------

--
-- Structure for view `view_anggota_aktif`
--
DROP TABLE IF EXISTS `view_anggota_aktif`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_anggota_aktif`  AS SELECT `a`.`id_anggota` AS `id_anggota`, `a`.`nomor_anggota` AS `nomor_anggota`, `a`.`nama_lengkap` AS `nama_lengkap`, `a`.`jenis_kelamin` AS `jenis_kelamin`, `a`.`alamat` AS `alamat`, `a`.`no_telepon` AS `no_telepon`, `a`.`email` AS `email`, `a`.`tanggal_daftar` AS `tanggal_daftar`, `a`.`foto_anggota` AS `foto_anggota`, `a`.`status` AS `status`, count(distinct `p`.`id_peminjaman`) AS `total_peminjaman`, count(distinct case when `p`.`status` = 'dipinjam' then `p`.`id_peminjaman` end) AS `sedang_dipinjam` FROM (`anggota` `a` left join `peminjaman` `p` on(`a`.`id_anggota` = `p`.`id_anggota`)) WHERE `a`.`status` = 'aktif' GROUP BY `a`.`id_anggota` ;

-- --------------------------------------------------------

--
-- Structure for view `view_buku_populer`
--
DROP TABLE IF EXISTS `view_buku_populer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_buku_populer`  AS SELECT `b`.`id_buku` AS `id_buku`, `b`.`judul` AS `judul`, `b`.`pengarang` AS `pengarang`, `b`.`cover_buku` AS `cover_buku`, `k`.`nama_kategori` AS `nama_kategori`, count(distinct `p`.`id_peminjaman`) AS `total_dipinjam`, coalesce(avg(`r`.`rating`),0) AS `rata_rata_rating`, count(distinct `r`.`id_review`) AS `jumlah_review` FROM (((`buku` `b` left join `kategori` `k` on(`b`.`id_kategori` = `k`.`id_kategori`)) left join `peminjaman` `p` on(`b`.`id_buku` = `p`.`id_buku`)) left join `review` `r` on(`b`.`id_buku` = `r`.`id_buku`)) GROUP BY `b`.`id_buku` ORDER BY count(distinct `p`.`id_peminjaman`) DESC, coalesce(avg(`r`.`rating`),0) DESC ;

-- --------------------------------------------------------

--
-- Structure for view `view_detail_peminjaman`
--
DROP TABLE IF EXISTS `view_detail_peminjaman`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_detail_peminjaman`  AS SELECT `p`.`id_peminjaman` AS `id_peminjaman`, `p`.`tanggal_pinjam` AS `tanggal_pinjam`, `p`.`tanggal_kembali` AS `tanggal_kembali`, `p`.`tanggal_dikembalikan` AS `tanggal_dikembalikan`, `p`.`status` AS `status`, `p`.`denda` AS `denda`, `a`.`nomor_anggota` AS `nomor_anggota`, `a`.`nama_lengkap` AS `nama_anggota`, `a`.`no_telepon` AS `no_telepon`, `b`.`judul` AS `judul_buku`, `b`.`isbn` AS `isbn`, `b`.`pengarang` AS `pengarang`, `u`.`nama_lengkap` AS `nama_petugas`, to_days(curdate()) - to_days(`p`.`tanggal_kembali`) AS `hari_terlambat` FROM (((`peminjaman` `p` join `anggota` `a` on(`p`.`id_anggota` = `a`.`id_anggota`)) join `buku` `b` on(`p`.`id_buku` = `b`.`id_buku`)) left join `users` `u` on(`p`.`id_petugas` = `u`.`id_user`)) ;

-- --------------------------------------------------------

--
-- Structure for view `view_statistik_kategori`
--
DROP TABLE IF EXISTS `view_statistik_kategori`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_statistik_kategori`  AS SELECT `k`.`nama_kategori` AS `nama_kategori`, count(`b`.`id_buku`) AS `jumlah_buku`, sum(`b`.`stok`) AS `total_stok`, coalesce(avg(`r`.`rating`),0) AS `rata_rata_rating` FROM ((`kategori` `k` left join `buku` `b` on(`k`.`id_kategori` = `b`.`id_kategori`)) left join `review` `r` on(`b`.`id_buku` = `r`.`id_buku`)) GROUP BY `k`.`id_kategori`, `k`.`nama_kategori` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id_anggota`),
  ADD UNIQUE KEY `nomor_anggota` (`nomor_anggota`);

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_petugas` (`id_petugas`);

--
-- Indexes for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD PRIMARY KEY (`id_reservasi`),
  ADD KEY `id_anggota` (`id_anggota`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_buku` (`id_buku`),
  ADD KEY `id_anggota` (`id_anggota`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id_anggota` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `reservasi`
--
ALTER TABLE `reservasi`
  MODIFY `id_reservasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buku`
--
ALTER TABLE `buku`
  ADD CONSTRAINT `buku_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE,
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_petugas`) REFERENCES `users` (`id_user`) ON DELETE SET NULL;

--
-- Constraints for table `reservasi`
--
ALTER TABLE `reservasi`
  ADD CONSTRAINT `reservasi_ibfk_1` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservasi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
