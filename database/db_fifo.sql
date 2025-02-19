-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Feb 2025 pada 15.16
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_fifo`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang`
--

CREATE TABLE `tb_barang` (
  `barang_id` int(3) NOT NULL,
  `kategori_id` int(3) NOT NULL,
  `barang_kode` varchar(15) DEFAULT NULL,
  `barang_nama` varchar(50) DEFAULT NULL,
  `barang_stok` int(5) NOT NULL,
  `barang_tgl` date NOT NULL,
  `barang_jual` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_barang`
--

INSERT INTO `tb_barang` (`barang_id`, `kategori_id`, `barang_kode`, `barang_nama`, `barang_stok`, `barang_tgl`, `barang_jual`) VALUES
(58, 22, 'MI-001', 'Indomie Ayam Bawang', 33, '2024-12-01', 4000),
(59, 13, 'TT-001', 'Tepung Terigu Sovia (1kg)', 6, '2024-11-04', 9000),
(60, 23, 'MYK-001', 'Minyak Sayur Bimoli (1L)', 5, '2024-12-01', 23000),
(61, 16, 'G-001', 'Cap Naga Berlian (Cair) M', 2, '2024-10-17', 5000),
(62, 24, 'SA-001', 'Saos ABC ', 8, '2025-01-01', 10000),
(63, 22, 'MI-002', 'Indomie Rendang', 6, '2024-10-11', 4000),
(64, 19, 'MRC-001', 'Mericaku', 13, '2025-01-07', 500),
(65, 13, 'TT-002', 'Tepung Terigu Tulip (1kg)', 11, '2024-11-20', 12000),
(66, 24, 'SA-0001', 'Indofood Saus Sambal', 6, '2024-12-01', 8000),
(67, 13, 'TT-003', 'Tepung Terigu Segitiga (1kg)', 5, '2024-01-01', 10000),
(68, 26, 'AGR-001', 'Swallow Globe Powder (CKLT)', 5, '2025-01-01', 5000),
(69, 28, 'TP-001', 'Toples Plastik (1L)', 16, '2024-12-01', 12000),
(70, 28, 'TP-002', 'Toples Plastik (500ML)', 15, '2024-01-01', 5500),
(71, 26, 'AGR-STR-001', 'Swallow Globe Powder (STRY)', 10, '2024-01-01', 5000),
(72, 22, NULL, 'Mie Gelas Bakso', 40, '2024-12-01', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang_msk`
--

CREATE TABLE `tb_barang_msk` (
  `msk_id` int(3) NOT NULL,
  `supplier_id` int(3) NOT NULL,
  `barang_id` int(3) NOT NULL,
  `msk_faktur` varchar(15) NOT NULL,
  `msk_jumlah` int(5) NOT NULL,
  `msk_harga_beli` decimal(10,2) NOT NULL,
  `msk_tgl` date NOT NULL,
  `msk_ket` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_barang_msk`
--

INSERT INTO `tb_barang_msk` (`msk_id`, `supplier_id`, `barang_id`, `msk_faktur`, `msk_jumlah`, `msk_harga_beli`, `msk_tgl`, `msk_ket`) VALUES
(49, 13, 58, 'FAK-1225-075658', 50, '2000.00', '2025-01-24', 'tunai'),
(50, 14, 59, 'RXKKM152', 25, '7500.00', '2024-12-01', 'Tunai '),
(51, 14, 60, 'KNG98142', 20, '20000.00', '2024-12-01', 'Tunai '),
(52, 16, 61, 'FAK-1225-075658', 15, '2000.00', '2024-10-17', 'Tunai '),
(53, 13, 58, 'FAK-1223-37682', 25, '2000.00', '2024-12-01', 'Tunai '),
(54, 17, 62, 'FAK-123-3567', 20, '6000.00', '2024-12-01', 'Tunai '),
(55, 13, 63, 'FAK-1226-8952', 20, '3500.00', '2025-12-05', 'Tunai '),
(56, 13, 63, 'FAK-1223-06689', 25, '3500.00', '2024-10-11', 'Tunai '),
(57, 15, 64, 'EPLX397', 35, '300.00', '2025-01-07', 'Tunai '),
(58, 13, 65, 'FAK-1225-07321', 25, '8000.00', '2024-11-20', 'Tunai '),
(59, 17, 66, 'FAK-123-3568', 10, '6000.00', '2024-12-01', 'Tunai '),
(60, 22, 67, 'RKMIJK', 5, '8000.00', '2024-12-01', 'Tunai '),
(61, 20, 68, 'MMAG085', 20, '3800.00', '2024-12-01', 'Tunai '),
(62, 15, 69, 'EPLX891', 20, '9000.00', '2024-12-01', 'Tunai '),
(63, 15, 70, 'EPX892', 15, '4500.00', '2024-12-01', 'Tunai '),
(64, 20, 71, 'MMAG086', 10, '3800.00', '2024-12-01', 'Tunai '),
(65, 13, 72, 'FAK-1227-5321', 40, '2000.00', '2024-12-01', 'Tunai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_detail_transaksi`
--

CREATE TABLE `tb_detail_transaksi` (
  `detail_id` int(3) NOT NULL,
  `id_transaksi` varchar(20) DEFAULT NULL,
  `barang_id` int(3) NOT NULL,
  `detail_jumlah` int(5) NOT NULL,
  `detail_total` decimal(10,2) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_detail_transaksi`
--

INSERT INTO `tb_detail_transaksi` (`detail_id`, `id_transaksi`, `barang_id`, `detail_jumlah`, `detail_total`, `is_deleted`) VALUES
(202, 'TRX-20250128102558', 58, 3, '12000.00', 0),
(203, 'TRX-20250128102558', 63, 3, '12000.00', 0),
(204, 'TRX-20250128102628', 64, 3, '1500.00', 0),
(205, 'TRX-20250128102628', 63, 5, '20000.00', 0),
(206, 'TRX-20250128102711', 60, 2, '46000.00', 0),
(207, 'TRX-20250128102711', 65, 2, '24000.00', 0),
(208, 'TRX-20250128102711', 63, 2, '8000.00', 0),
(209, 'TRX-20250128102711', 64, 5, '2500.00', 0),
(210, 'TRX-20250128121234', 58, 5, '20000.00', 0),
(211, 'TRX-20250128142813', 61, 13, '65000.00', 0),
(212, 'TRX-20250128190526', 63, 3, '12000.00', 0),
(213, 'TRX-20250205112914', 68, 5, '25000.00', 0),
(214, 'TRX-20250205112914', 65, 1, '12000.00', 0),
(215, 'TRX-20250205113014', 60, 3, '69000.00', 1),
(216, 'TRX-20250205113102', 60, 5, '115000.00', 0),
(217, 'TRX-20250205113130', 59, 3, '27000.00', 0),
(218, 'TRX-20250205113130', 63, 2, '8000.00', 0),
(219, 'TRX-20250205113130', 68, 1, '5000.00', 0),
(220, 'TRX-20250205113130', 65, 2, '24000.00', 0),
(221, 'TRX-20250205113130', 64, 2, '1000.00', 0),
(222, 'TRX-20250205113130', 62, 2, '20000.00', 0),
(223, 'TRX-20250205205116', 58, 12, '48000.00', 0),
(224, 'TRX-20250205205116', 65, 5, '60000.00', 0),
(225, 'TRX-20250205205116', 64, 2, '1000.00', 0),
(226, 'TRX-20250205205219', 63, 4, '16000.00', 0),
(227, 'TRX-20250205205246', 69, 4, '48000.00', 0),
(228, 'TRX-20250205205246', 60, 6, '138000.00', 0),
(229, 'TRX-20250205205333', 63, 4, '16000.00', 0),
(230, 'TRX-20250205205333', 62, 2, '20000.00', 0),
(231, 'TRX-20250205205408', 59, 3, '27000.00', 0),
(232, 'TRX-20250205205408', 66, 2, '16000.00', 0),
(233, 'TRX-20250205205436', 64, 4, '2000.00', 0),
(234, 'TRX-20250205205436', 59, 2, '18000.00', 0),
(235, 'TRX-20250205205502', 58, 10, '40000.00', 0),
(236, 'TRX-20250205205502', 62, 2, '20000.00', 0),
(237, 'TRX-20250205205541', 65, 4, '48000.00', 0),
(238, 'TRX-20250205205606', 63, 5, '20000.00', 0),
(239, 'TRX-20250205205626', 68, 5, '25000.00', 0),
(240, 'TRX-20250205205651', 59, 5, '45000.00', 0),
(241, 'TRX-20250205205713', 62, 4, '40000.00', 0),
(242, 'TRX-20250205205736', 64, 4, '2000.00', 0),
(243, 'TRX-20250205205736', 60, 2, '46000.00', 0),
(244, 'TRX-20250205205803', 59, 6, '54000.00', 0),
(245, 'TRX-20250205205827', 63, 7, '28000.00', 0),
(246, 'TRX-20250205210608', 68, 4, '20000.00', 0),
(247, 'TRX-20250205210608', 63, 2, '8000.00', 0),
(248, 'TRX-20250205210608', 58, 6, '24000.00', 0),
(249, 'TRX-20250205210726', 58, 2, '8000.00', 0),
(250, 'TRX-20250205213313', 58, 2, '8000.00', 0),
(251, 'TRX-20250205221109', 62, 2, '20000.00', 0),
(252, 'TRX-20250205221324', 66, 2, '16000.00', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kategori`
--

CREATE TABLE `tb_kategori` (
  `kategori_id` int(3) NOT NULL,
  `kategori_nama` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_kategori`
--

INSERT INTO `tb_kategori` (`kategori_id`, `kategori_nama`) VALUES
(13, 'Tepung '),
(14, 'Kecap '),
(16, 'Pewarna Makanan'),
(19, 'Bumbu Masak'),
(21, 'Susu '),
(22, 'Mie '),
(23, 'Minyak '),
(24, 'Saos'),
(25, 'Kelapa'),
(26, 'Agar-agar'),
(27, 'Bahan-Bahan Kue'),
(28, 'Kemasan Makanan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pelanggan`
--

CREATE TABLE `tb_pelanggan` (
  `pelanggan_id` int(3) NOT NULL,
  `pelanggan_nama` varchar(50) NOT NULL,
  `pelanggan_alamat` text NOT NULL,
  `pelanggan_tlp` varchar(10) NOT NULL,
  `pelanggan_diskon` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_pelanggan`
--

INSERT INTO `tb_pelanggan` (`pelanggan_id`, `pelanggan_nama`, `pelanggan_alamat`, `pelanggan_tlp`, `pelanggan_diskon`) VALUES
(16, 'Rizki', 'Kuranji', '0853848397', '5.00'),
(17, 'Amel', 'Jambi', '0864827144', '5.00'),
(18, 'Nagita', 'Kayu Aro', '081241345', '5.00'),
(19, 'Anita Trisya', 'kerinci', '0853887598', '5.00'),
(20, 'Mufrinal', 'Lubuk Begalung', '0853876988', '5.00'),
(21, 'Mutia', 'Bangko', '0821389475', '5.00'),
(22, 'Muzaqi', 'Kayu Aro', '0822135465', '5.00'),
(23, 'Ramadhan Afiq', 'Sarolangun', '0822135465', '5.00'),
(24, 'Nurlena', 'Batang Merangin', '0813669738', '5.00'),
(25, 'Rizkika Syakirah', 'Tapan', '0822154327', '5.00'),
(26, 'Lestari', 'Kayu aro', '0821787865', '5.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pengguna`
--

CREATE TABLE `tb_pengguna` (
  `pengguna_id` int(3) NOT NULL,
  `pengguna_user` varchar(10) NOT NULL,
  `pengguna_pass` varchar(10) NOT NULL,
  `pengguna_nama` varchar(50) NOT NULL,
  `pengguna_level` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_pengguna`
--

INSERT INTO `tb_pengguna` (`pengguna_id`, `pengguna_user`, `pengguna_pass`, `pengguna_nama`, `pengguna_level`) VALUES
(1, 'admin', 'admin', 'Administrator', 'Admin'),
(2, 'Kasir', 'Kasir1', 'Amelia', 'Kasir'),
(3, 'Ade', 'pemilik', 'Ade Arahman', 'Pemilik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_penggunaan_stok`
--

CREATE TABLE `tb_penggunaan_stok` (
  `penggunaan_id` int(3) NOT NULL,
  `detail_id` int(3) NOT NULL,
  `batch_id` int(3) NOT NULL,
  `jumlah` int(5) NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_penggunaan_stok`
--

INSERT INTO `tb_penggunaan_stok` (`penggunaan_id`, `detail_id`, `batch_id`, `jumlah`, `harga_beli`) VALUES
(130, 202, 49, 3, '2000.00'),
(131, 203, 52, 3, '3500.00'),
(132, 204, 53, 3, '300.00'),
(133, 205, 52, 5, '3500.00'),
(134, 206, 47, 2, '20000.00'),
(135, 207, 54, 2, '8000.00'),
(136, 208, 52, 2, '3500.00'),
(137, 209, 53, 5, '300.00'),
(138, 210, 49, 5, '2000.00'),
(139, 211, 48, 13, '2000.00'),
(140, 212, 52, 3, '3500.00'),
(141, 213, 57, 5, '3800.00'),
(142, 214, 54, 1, '8000.00'),
(144, 216, 47, 5, '20000.00'),
(145, 217, 46, 3, '7500.00'),
(146, 218, 52, 2, '3500.00'),
(147, 219, 57, 1, '3800.00'),
(148, 220, 54, 2, '8000.00'),
(149, 221, 53, 2, '300.00'),
(150, 222, 50, 2, '6000.00'),
(151, 223, 49, 12, '2000.00'),
(152, 224, 54, 5, '8000.00'),
(153, 225, 53, 2, '300.00'),
(154, 226, 52, 4, '3500.00'),
(155, 227, 58, 4, '9000.00'),
(156, 228, 47, 6, '20000.00'),
(157, 229, 52, 4, '3500.00'),
(158, 230, 50, 2, '6000.00'),
(159, 231, 46, 3, '7500.00'),
(160, 232, 55, 2, '6000.00'),
(161, 233, 53, 4, '300.00'),
(162, 234, 46, 2, '7500.00'),
(163, 235, 49, 3, '2000.00'),
(164, 235, 45, 7, '2000.00'),
(165, 236, 50, 2, '6000.00'),
(166, 237, 54, 4, '8000.00'),
(167, 238, 51, 5, '3500.00'),
(168, 239, 57, 5, '3800.00'),
(169, 240, 46, 5, '7500.00'),
(170, 241, 50, 4, '6000.00'),
(171, 242, 53, 4, '300.00'),
(172, 243, 47, 2, '20000.00'),
(173, 244, 46, 6, '7500.00'),
(174, 245, 51, 7, '3500.00'),
(175, 246, 57, 4, '3800.00'),
(176, 247, 51, 2, '3500.00'),
(177, 248, 45, 6, '2000.00'),
(178, 249, 45, 2, '2000.00'),
(179, 250, 45, 2, '2000.00'),
(180, 251, 50, 2, '6000.00'),
(181, 252, 55, 2, '6000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_stok_batch`
--

CREATE TABLE `tb_stok_batch` (
  `batch_id` int(3) NOT NULL,
  `barang_id` int(3) NOT NULL,
  `msk_id` int(3) NOT NULL,
  `jumlah_tersisa` int(5) NOT NULL,
  `harga_beli` decimal(10,2) NOT NULL,
  `tanggal_masuk` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_stok_batch`
--

INSERT INTO `tb_stok_batch` (`batch_id`, `barang_id`, `msk_id`, `jumlah_tersisa`, `harga_beli`, `tanggal_masuk`) VALUES
(45, 58, 49, 33, '2000.00', '2025-01-24'),
(46, 59, 50, 6, '7500.00', '2024-12-01'),
(47, 60, 51, 5, '20000.00', '2024-12-01'),
(48, 61, 52, 2, '2000.00', '2024-10-17'),
(49, 58, 53, 0, '2000.00', '2024-12-01'),
(50, 62, 54, 8, '6000.00', '2024-12-01'),
(51, 63, 55, 6, '3500.00', '2025-12-05'),
(52, 63, 56, 0, '3500.00', '2024-10-11'),
(53, 64, 57, 13, '300.00', '2025-01-07'),
(54, 65, 58, 11, '8000.00', '2024-11-20'),
(55, 66, 59, 6, '6000.00', '2024-12-01'),
(56, 67, 60, 5, '8000.00', '2024-12-01'),
(57, 68, 61, 5, '3800.00', '2024-12-01'),
(58, 69, 62, 16, '9000.00', '2024-12-01'),
(59, 70, 63, 15, '4500.00', '2024-12-01'),
(60, 71, 64, 10, '3800.00', '2024-12-01'),
(61, 72, 65, 40, '2000.00', '2024-12-01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_supplier`
--

CREATE TABLE `tb_supplier` (
  `supplier_id` int(3) NOT NULL,
  `supplier_nama` varchar(50) NOT NULL,
  `supplier_alamat` text NOT NULL,
  `supplier_tlp` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_supplier`
--

INSERT INTO `tb_supplier` (`supplier_id`, `supplier_nama`, `supplier_alamat`, `supplier_tlp`) VALUES
(13, 'PT. ALAM JAYA WIRASENTOSA', 'Jl. Hatta No.23 Ampang Gadang, Kec. Ampek Angkek, Kabupaten Agam, Sumatera Barat', '0813669738'),
(14, 'TOKO ABADI BARU ', 'Jl. Bandar Olo No.53, Olo, Kec. Padang Bar., Kota Padang, Sumatera Barat', '0822135465'),
(15, 'TOKO ELOK PLASTIK', 'Jl. Bandar Olo No.21 PTK 1-2, Olo Kec Padang Barat., Kota Padang, Sumatera Barat ', '075128863'),
(16, 'Indogrosir Jambi', 'Jl. Lingkar Selatan, Kenali Asam Bawah, Kec. Kota Baru, Kota Jambi', '0741444433'),
(17, ' Toko Lam Jaya', 'Jl. TP. Sriwijaya, Beliung, Kec. Kota Baru, Kota Jambi', '0896321041'),
(18, 'Fresh One', 'Jl. Sudirman, Suka Karya, Kec. Kota Baru, Kota Jambi, Jambi', '0821800097'),
(19, 'Toko Kelontong Abok', ' Jl. Hatta, Lb. Bandung, Kec. Jelutung, Kota Jambi, Jambi 36124', '0822678532'),
(20, 'MM Permata', 'Jl. Sumatera, Handil Jaya, Kec. Kota Baru, Kota Jambi', '0761474633'),
(21, 'Toko Fakhira Jaya', 'Jl. Gajah Mada, Kenali Asam Bawah, Kec. Kota Baru, Kota Jambi', '0813665014'),
(22, 'Grosir Mandiri P&D', 'Jl. Jati. Kp. Pinang No.211, Jati, Kec. Padang Tim., Kota Padang, Sumatera Barat ', '0823883918'),
(23, 'PT. BIMA SAKTI', 'Jl. Gatot Subroto, Belati, Kec. Muara Siban,Kota Pagar Alam, Sumatera Utara', '0821787865');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_transaksi` varchar(20) NOT NULL,
  `pelanggan_id` int(3) DEFAULT NULL,
  `tgl_transaksi` date NOT NULL,
  `total_transaksi` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `payment` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending','selesai') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data untuk tabel `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_transaksi`, `pelanggan_id`, `tgl_transaksi`, `total_transaksi`, `discount`, `payment`, `status`) VALUES
('TRX-20250128102558', 18, '2025-01-28', '22800.00', '1200.00', '25000.00', 'selesai'),
('TRX-20250128102628', 16, '2025-01-28', '20425.00', '1075.00', '50000.00', 'selesai'),
('TRX-20250128102711', 0, '2025-01-28', '80500.00', '0.00', '100000.00', 'selesai'),
('TRX-20250128121234', 0, '2025-01-28', '20000.00', '0.00', '20000.00', 'selesai'),
('TRX-20250128142813', 0, '2025-01-28', '65000.00', '0.00', '70000.00', 'selesai'),
('TRX-20250128190526', 0, '2025-01-28', '12000.00', '0.00', '15000.00', 'selesai'),
('TRX-20250205112914', 23, '2025-01-28', '35150.00', '1850.00', '40000.00', 'selesai'),
('TRX-20250205113014', 20, '2025-02-05', '0.00', '0.00', '0.00', ''),
('TRX-20250205113102', 20, '2025-01-28', '109250.00', '5750.00', '120000.00', 'selesai'),
('TRX-20250205113130', 21, '2025-01-28', '80750.00', '4250.00', '100000.00', 'selesai'),
('TRX-20250205205116', 19, '2025-01-29', '103550.00', '5450.00', '120000.00', 'selesai'),
('TRX-20250205205219', 0, '2025-01-29', '16000.00', '0.00', '20000.00', 'selesai'),
('TRX-20250205205246', 0, '2025-01-29', '186000.00', '0.00', '200000.00', 'selesai'),
('TRX-20250205205333', 0, '2025-01-30', '36000.00', '0.00', '50000.00', 'selesai'),
('TRX-20250205205408', 0, '2025-01-30', '43000.00', '0.00', '50000.00', 'selesai'),
('TRX-20250205205436', 0, '2025-01-31', '20000.00', '0.00', '20000.00', 'selesai'),
('TRX-20250205205502', 24, '2025-01-31', '57000.00', '3000.00', '60000.00', 'selesai'),
('TRX-20250205205541', 25, '2025-01-31', '45600.00', '2400.00', '50000.00', 'selesai'),
('TRX-20250205205606', 21, '2025-02-01', '19000.00', '1000.00', '20000.00', 'selesai'),
('TRX-20250205205626', 19, '2025-02-01', '23750.00', '1250.00', '25000.00', 'selesai'),
('TRX-20250205205651', 23, '2025-02-02', '42750.00', '2250.00', '50000.00', 'selesai'),
('TRX-20250205205713', 17, '2025-02-02', '38000.00', '2000.00', '50000.00', 'selesai'),
('TRX-20250205205736', 22, '2025-02-03', '45600.00', '2400.00', '50000.00', 'selesai'),
('TRX-20250205205803', 18, '2025-02-04', '51300.00', '2700.00', '100000.00', 'selesai'),
('TRX-20250205205827', 0, '2025-02-05', '28000.00', '0.00', '30000.00', 'selesai'),
('TRX-20250205210608', 0, '2025-02-05', '52000.00', '0.00', '60000.00', 'selesai'),
('TRX-20250205210726', 0, '2025-02-05', '8000.00', '0.00', '10000.00', 'selesai'),
('TRX-20250205213313', 0, '2025-01-28', '8000.00', '0.00', '10000.00', 'selesai'),
('TRX-20250205221109', 0, '2025-02-05', '20000.00', '0.00', '20000.00', 'selesai'),
('TRX-20250205221324', 0, '2025-02-05', '16000.00', '0.00', '20000.00', 'selesai');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`barang_id`);

--
-- Indeks untuk tabel `tb_barang_msk`
--
ALTER TABLE `tb_barang_msk`
  ADD PRIMARY KEY (`msk_id`);

--
-- Indeks untuk tabel `tb_detail_transaksi`
--
ALTER TABLE `tb_detail_transaksi`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `fk_detail_transaksi_transaksi` (`id_transaksi`);

--
-- Indeks untuk tabel `tb_kategori`
--
ALTER TABLE `tb_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indeks untuk tabel `tb_pelanggan`
--
ALTER TABLE `tb_pelanggan`
  ADD PRIMARY KEY (`pelanggan_id`);

--
-- Indeks untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  ADD PRIMARY KEY (`pengguna_id`);

--
-- Indeks untuk tabel `tb_penggunaan_stok`
--
ALTER TABLE `tb_penggunaan_stok`
  ADD PRIMARY KEY (`penggunaan_id`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `tb_penggunaan_stok_ibfk_1` (`detail_id`);

--
-- Indeks untuk tabel `tb_stok_batch`
--
ALTER TABLE `tb_stok_batch`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `barang_id` (`barang_id`),
  ADD KEY `msk_id` (`msk_id`);

--
-- Indeks untuk tabel `tb_supplier`
--
ALTER TABLE `tb_supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- Indeks untuk tabel `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_transaksi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_barang`
--
ALTER TABLE `tb_barang`
  MODIFY `barang_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT untuk tabel `tb_barang_msk`
--
ALTER TABLE `tb_barang_msk`
  MODIFY `msk_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT untuk tabel `tb_detail_transaksi`
--
ALTER TABLE `tb_detail_transaksi`
  MODIFY `detail_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT untuk tabel `tb_kategori`
--
ALTER TABLE `tb_kategori`
  MODIFY `kategori_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `tb_pelanggan`
--
ALTER TABLE `tb_pelanggan`
  MODIFY `pelanggan_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  MODIFY `pengguna_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tb_penggunaan_stok`
--
ALTER TABLE `tb_penggunaan_stok`
  MODIFY `penggunaan_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT untuk tabel `tb_stok_batch`
--
ALTER TABLE `tb_stok_batch`
  MODIFY `batch_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT untuk tabel `tb_supplier`
--
ALTER TABLE `tb_supplier`
  MODIFY `supplier_id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_detail_transaksi`
--
ALTER TABLE `tb_detail_transaksi`
  ADD CONSTRAINT `fk_detail_transaksi_transaksi` FOREIGN KEY (`id_transaksi`) REFERENCES `tb_transaksi` (`id_transaksi`);

--
-- Ketidakleluasaan untuk tabel `tb_penggunaan_stok`
--
ALTER TABLE `tb_penggunaan_stok`
  ADD CONSTRAINT `tb_penggunaan_stok_ibfk_1` FOREIGN KEY (`detail_id`) REFERENCES `tb_detail_transaksi` (`detail_id`),
  ADD CONSTRAINT `tb_penggunaan_stok_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `tb_stok_batch` (`batch_id`);

--
-- Ketidakleluasaan untuk tabel `tb_stok_batch`
--
ALTER TABLE `tb_stok_batch`
  ADD CONSTRAINT `tb_stok_batch_ibfk_1` FOREIGN KEY (`barang_id`) REFERENCES `tb_barang` (`barang_id`),
  ADD CONSTRAINT `tb_stok_batch_ibfk_2` FOREIGN KEY (`msk_id`) REFERENCES `tb_barang_msk` (`msk_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
