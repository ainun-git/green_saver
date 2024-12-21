-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 21, 2024 at 05:08 AM
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
-- Database: `greensaver_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategorisampah`
--

CREATE TABLE `kategorisampah` (
  `kodeKategori` varchar(5) NOT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `namaKategori` varchar(100) DEFAULT NULL,
  `contohBarang` text DEFAULT NULL,
  `harga` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategorisampah`
--

INSERT INTO `kategorisampah` (`kodeKategori`, `kategori`, `namaKategori`, `contohBarang`, `harga`) VALUES
('B01', 'Botol Kaca', 'Botol Markisa Bensin', 'Botol leher pendek bening', 1000),
('B02', 'Botol Kaca', 'Botol Kecap/Bir', 'Botol leher panjang tebal', 600),
('B03', 'Botol Kaca', 'Botol Marjan', 'Botol Tebal, leher pendek', 500),
('B05', 'Botol Kaca', 'Botol Soda', 'Botol Tebal', 300),
('B06', 'Botol Kaca', 'Botol Bir Guinness', 'Botol Tebal', 250),
('K01', 'Kertas', 'Kertas Putih', 'Buku Tulis, kertas foto copy', 1400),
('K02', 'Kertas', 'Kertas Campur/warna', 'Majalah, karton warna', 1500),
('K03', 'Kertas', 'Kertas Buram', 'Kertas kelabu/buram', 1100),
('K04', 'Kertas', 'Karton (dos)', 'Karton Coklat Box', 1300),
('K05A', 'Kertas', 'Kertas Semen A', 'Kertas Semen Tonasa', 1100),
('K05B', 'Kertas', 'Kertas Mikel', 'Kertas Pembungkus Coklat', 1200),
('K06', 'Kertas', 'Koran', 'Koran Berita', 1100),
('K07', 'Kertas', 'Karton Rak Telor', 'Rak untuk susun telur', 400),
('K08', 'Kertas', 'Cones', 'Kertas Gulungan', 400),
('L01', 'Logam', 'Besi Tebal', 'Besi Cor, Besi Plat', 3500),
('L02', 'Logam', 'Besi Tipis', 'Drum, Rak pintu', 2900),
('L03', 'Logam', 'Kaleng', 'Kaleng Makanan, Kaleng Susu, dsb di press', 2000),
('L04', 'Logam', 'Kaleng Campur', 'Kaleng minuman dengan Label', 2400),
('L05', 'Logam', 'Aluminium Tebal', 'Box Mesin Motor', 15000),
('L06', 'Logam', 'Aluminium Tipis', 'Kaleng minuman, Panda, Sprite, Wajan', 7000),
('L09', 'Logam', 'Aluminium Campur', 'Aluminium campur besi', 6000),
('L10', 'Logam', 'Besi Seng', 'Seng Bekas', 1500),
('L11', 'Logam', 'Perunggu', 'Kran Air, kepala regulator', 7000),
('M01', 'Minyak Jelantah', 'Minyak Jelantah', 'Minyak goreng bekas memasak/goreng', 7000),
('P01B', 'Plastik', 'PP Gelas Bening Bersih', 'Aqua, Club, JS tanpa label', 6000),
('P01K', 'Plastik', 'PP Gelas Bening Kotor', 'Ale-Ale, Montea, Teh gelas, tanpa label', 2800),
('P02C', 'Plastik', 'PP Cincin Gelas', 'Cincin atau potongan bibir gelas', 1600),
('P02K', 'Plastik', 'PP Gelas Warna, Kotor', 'Ale-Ale, Montea, Teh gelas, Tanpa Label, Dan Dissusun', 2200),
('P03B', 'Plastik', 'PET Bening Bersih', 'Botol ades, prima tanpa penutup dan label', 5500),
('P04B', 'Plastik', 'PET Biru Muda Bersih', 'Botol Air Mineral Aqua, J5, Club tanpa penutup dan label', 4000),
('P04C', 'Plastik', 'PET Campur', 'PET Tanpa penutup dan label', 1000),
('P04K', 'Plastik', 'PET Kotor', 'Segala jenis PET yang masih ada label dan penutup', 1000),
('P05B', 'Plastik', 'PET Warna Bersih/Pisah', 'PET Tanpa penutup dan label, warna hijau atau biru (campur)', 2000),
('P07C', 'Plastik', 'Plastik HD Campur', 'Kantong plastik warna campur', 1000),
('P08C', 'Plastik', 'Plastik HD Map Campur', 'Bascom, gelas, piring, tanpa campur', 1800),
('P09C', 'Plastik', 'HD Tutup Galon', 'Tutup botol campur', 3500),
('P09K', 'Plastik', 'HD Tutup Galon Campur', 'Tutup Galon warna campur', 2800),
('P10K', 'Plastik', 'Plastik PP Campur', 'Kresek campur, Bungkus Sampur, Kresek Warna, Bening', 1800),
('P11K', 'Plastik', 'Plastik PP Cetak', 'Kemasan Plastik Mie instan dan Roti', 1800),
('P12K', 'Plastik', 'Plastik HD (Blow) campur', 'Kemasan Plastik Bergaris tengah', 2300);

-- --------------------------------------------------------

--
-- Table structure for table `nasabah`
--

CREATE TABLE `nasabah` (
  `IdNasabah` varchar(5) NOT NULL,
  `nama` varchar(50) DEFAULT NULL,
  `nomorInduk` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `saldo` float DEFAULT NULL,
  `poin` int(11) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nasabah`
--

INSERT INTO `nasabah` (`IdNasabah`, `nama`, `nomorInduk`, `alamat`, `saldo`, `poin`, `username`, `password`) VALUES
('NS002', 'Muhlis', '0803102023', 'BTN Timurama A8/3', 20000, 100, 'Muhlis', '$2y$10$/ki2QeqcL.KYSL9BKGfPDO2rnv6F6/bEgoWD2K8IwgzfYNzzZzM/S'),
('NS003', 'Irfan', '0803152023', 'BTN Timurama A8/4', 15000, 75, 'Irfan', '$2y$10$L/4DZYda6HkQhRA9BhvtMe9K0gyX1o0F2FIwNE1spvc8zMulBojh2'),
('NS004', 'Hawani', '0803122024', 'BTN Timurama A11/7', 7000, 35, 'Hawani', '$2y$10$fyBWhjVEa/2DB9pyKcH4j.V/YdSdL17b9.W6K0lJdkK5uy.rH/bJy'),
('NS005', 'Solihin', '0803012023', 'Jl. S. Saddang Baru A8/2', 60000, 150, 'Solihin', '$2y$10$Fgyv1MCc21hu4d1gGtw0k.y6Z.xosfqbKzepMxXLqFAsrkXkWA6Xm'),
('NS006', 'Anto', '0803172023', 'BTN Timurama A11/12', 7000, 35, 'Anto', '$2y$10$B4hATyIZmpE7UTNhWg0qyOTsuAdEbIlnbrdRYZ/2cJ.DV0.36NHh.'),
('NS007', 'Iron', '0803102024', 'BTN Timurama A8/6', 68000, 340, 'Iron', '$2y$10$WFXzI5hM1Hzopw/nitKyeO2xjWIF8FtcYeoff0pzHuL6yMLXUsqPG');

-- --------------------------------------------------------

--
-- Table structure for table `penarikan`
--

CREATE TABLE `penarikan` (
  `IdPenarikan` varchar(5) NOT NULL,
  `IdNasabah` varchar(5) DEFAULT NULL,
  `jumlah` float DEFAULT NULL,
  `tanggalPenarikan` date DEFAULT NULL,
  `jenisPenarikan` enum('PenukaranPoin','PenarikanSaldo') DEFAULT NULL,
  `status` enum('Pending','Diterima','Ditolak') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penarikan`
--

INSERT INTO `penarikan` (`IdPenarikan`, `IdNasabah`, `jumlah`, `tanggalPenarikan`, `jenisPenarikan`, `status`) VALUES
('PR001', 'NS005', 25000, '2024-12-18', 'PenarikanSaldo', 'Pending'),
('PR002', 'NS005', 100, '2024-12-19', 'PenukaranPoin', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `IdPengurus` varchar(5) NOT NULL,
  `namaPengurus` varchar(50) DEFAULT NULL,
  `jabatan` varchar(50) DEFAULT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengurus`
--

INSERT INTO `pengurus` (`IdPengurus`, `namaPengurus`, `jabatan`, `username`, `password`) VALUES
('P001', 'Solihin', 'Ketua', 'PengurusBSU', 'BSUcm24');

-- --------------------------------------------------------

--
-- Table structure for table `penjualansampah`
--

CREATE TABLE `penjualansampah` (
  `IdPenjualan` varchar(5) NOT NULL,
  `IdPengurus` varchar(5) DEFAULT NULL,
  `tanggalPenjualan` date DEFAULT NULL,
  `kodeKategori` varchar(5) DEFAULT NULL,
  `totalBerat` float DEFAULT NULL,
  `totalHarga` float DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualansampah`
--

INSERT INTO `penjualansampah` (`IdPenjualan`, `IdPengurus`, `tanggalPenjualan`, `kodeKategori`, `totalBerat`, `totalHarga`, `keterangan`) VALUES
('PJ001', 'P001', '2024-12-18', 'P04C', 20, 20000, 'Sampah Plastik');

-- --------------------------------------------------------

--
-- Table structure for table `riwayattransaksi`
--

CREATE TABLE `riwayattransaksi` (
  `IdTransaksi` varchar(5) NOT NULL,
  `IdNasabah` varchar(5) DEFAULT NULL,
  `jenisTransaksi` enum('Setoran','Penarikan') DEFAULT NULL,
  `jumlah` float DEFAULT NULL,
  `tanggalTransaksi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `riwayattransaksi`
--

INSERT INTO `riwayattransaksi` (`IdTransaksi`, `IdNasabah`, `jenisTransaksi`, `jumlah`, `tanggalTransaksi`) VALUES
('T001', 'NS005', 'Setoran', 50000, '2024-01-12'),
('T002', 'NS002', 'Setoran', 20000, '2024-01-25'),
('T003', 'NS003', 'Setoran', 15000, '2024-02-16'),
('T004', 'NS004', 'Setoran', 7000, '2024-03-31'),
('T005', 'NS006', 'Setoran', 7000, '2024-04-09'),
('T006', 'NS007', 'Setoran', 68000, '2024-10-17'),
('T007', 'NS005', 'Penarikan', 25000, '2024-12-18'),
('T008', 'NS005', 'Penarikan', 10000, '2024-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `setoransampah`
--

CREATE TABLE `setoransampah` (
  `IdSetoran` varchar(5) NOT NULL,
  `IdNasabah` varchar(5) DEFAULT NULL,
  `kodeKategori` varchar(5) DEFAULT NULL,
  `berat` float DEFAULT NULL,
  `totalHarga` float DEFAULT NULL,
  `tanggalSetor` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setoransampah`
--

INSERT INTO `setoransampah` (`IdSetoran`, `IdNasabah`, `kodeKategori`, `berat`, `totalHarga`, `tanggalSetor`) VALUES
('S001', 'NS005', 'P04C', 50, 50000, '2024-01-12'),
('S002', 'NS002', 'P04C', 20, 20000, '2024-01-25'),
('S003', 'NS003', 'P04C', 15, 15000, '2024-02-16'),
('S004', 'NS004', 'P04C', 7, 7000, '2024-03-31'),
('S005', 'NS006', 'P04C', 7, 7000, '2024-04-09'),
('S006', 'NS007', 'P04C', 68, 68000, '2024-10-17');

-- --------------------------------------------------------

--
-- Table structure for table `tabungan`
--

CREATE TABLE `tabungan` (
  `id_tabungan` int(11) NOT NULL,
  `IdNasabah` varchar(5) NOT NULL,
  `jenis_tabungan` varchar(255) NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `tanggal` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategorisampah`
--
ALTER TABLE `kategorisampah`
  ADD PRIMARY KEY (`kodeKategori`);

--
-- Indexes for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`IdNasabah`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `penarikan`
--
ALTER TABLE `penarikan`
  ADD PRIMARY KEY (`IdPenarikan`),
  ADD KEY `IdNasabah` (`IdNasabah`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`IdPengurus`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `penjualansampah`
--
ALTER TABLE `penjualansampah`
  ADD PRIMARY KEY (`IdPenjualan`),
  ADD KEY `IdPengurus` (`IdPengurus`),
  ADD KEY `kodeKategori` (`kodeKategori`);

--
-- Indexes for table `riwayattransaksi`
--
ALTER TABLE `riwayattransaksi`
  ADD PRIMARY KEY (`IdTransaksi`),
  ADD KEY `IdNasabah` (`IdNasabah`);

--
-- Indexes for table `setoransampah`
--
ALTER TABLE `setoransampah`
  ADD PRIMARY KEY (`IdSetoran`),
  ADD KEY `IdNasabah` (`IdNasabah`),
  ADD KEY `kodeKategori` (`kodeKategori`);

--
-- Indexes for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD PRIMARY KEY (`id_tabungan`),
  ADD KEY `IdNasabah` (`IdNasabah`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tabungan`
--
ALTER TABLE `tabungan`
  MODIFY `id_tabungan` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penarikan`
--
ALTER TABLE `penarikan`
  ADD CONSTRAINT `penarikan_ibfk_1` FOREIGN KEY (`IdNasabah`) REFERENCES `nasabah` (`IdNasabah`);

--
-- Constraints for table `penjualansampah`
--
ALTER TABLE `penjualansampah`
  ADD CONSTRAINT `penjualansampah_ibfk_1` FOREIGN KEY (`IdPengurus`) REFERENCES `pengurus` (`IdPengurus`),
  ADD CONSTRAINT `penjualansampah_ibfk_2` FOREIGN KEY (`kodeKategori`) REFERENCES `kategorisampah` (`kodeKategori`);

--
-- Constraints for table `riwayattransaksi`
--
ALTER TABLE `riwayattransaksi`
  ADD CONSTRAINT `riwayattransaksi_ibfk_1` FOREIGN KEY (`IdNasabah`) REFERENCES `nasabah` (`IdNasabah`);

--
-- Constraints for table `setoransampah`
--
ALTER TABLE `setoransampah`
  ADD CONSTRAINT `setoransampah_ibfk_1` FOREIGN KEY (`IdNasabah`) REFERENCES `nasabah` (`IdNasabah`),
  ADD CONSTRAINT `setoransampah_ibfk_2` FOREIGN KEY (`kodeKategori`) REFERENCES `kategorisampah` (`kodeKategori`);

--
-- Constraints for table `tabungan`
--
ALTER TABLE `tabungan`
  ADD CONSTRAINT `tabungan_ibfk_1` FOREIGN KEY (`IdNasabah`) REFERENCES `nasabah` (`IdNasabah`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
