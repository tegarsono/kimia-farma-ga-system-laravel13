-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Jun 2026 pada 18.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kimiafarma`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `atk_katalog`
--

CREATE TABLE `atk_katalog` (
  `id` int(11) UNSIGNED NOT NULL,
  `kategori` varchar(100) NOT NULL COMMENT 'Kategori ATK (e.g., Alat Tulis, Kertas)',
  `nama_barang` varchar(255) NOT NULL COMMENT 'Nama spesifik barang ATK',
  `satuan` varchar(50) NOT NULL COMMENT 'Satuan unit (e.g., Pcs, Rim)',
  `harga` int(11) NOT NULL COMMENT 'Harga jual per unit',
  `spesifikasi` text DEFAULT NULL COMMENT 'Detail spesifikasi atau deskripsi barang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_barang` enum('Masuk','Keluar') NOT NULL,
  `jumlah` int(11) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `kode` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `atk_transaksi`
--

CREATE TABLE `atk_transaksi` (
  `id` int(10) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` varchar(50) NOT NULL COMMENT 'Jenis ATK',
  `jumlah` int(11) NOT NULL COMMENT 'Jumlah transaksi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` longtext NOT NULL,
  `id_barang` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel transaksi ATK untuk rekap bulanan';

-- --------------------------------------------------------

--
-- Struktur dari tabel `dir_aset`
--

CREATE TABLE `dir_aset` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cost_center` varchar(100) NOT NULL,
  `profit_center` varchar(100) NOT NULL,
  `unit_bisnis` varchar(100) NOT NULL,
  `golongan_aset` varchar(100) NOT NULL,
  `kategori_aset` varchar(255) NOT NULL,
  `deskripsi_aset` text NOT NULL,
  `lokasi_pemakai` varchar(255) NOT NULL,
  `kode_aset` varchar(100) NOT NULL,
  `id_aset` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `dir_aset`
--

INSERT INTO `dir_aset` (`id`, `cost_center`, `profit_center`, `unit_bisnis`, `golongan_aset`, `kategori_aset`, `deskripsi_aset`, `lokasi_pemakai`, `kode_aset`, `id_aset`, `keterangan`, `created_at`, `updated_at`) VALUES
(8, 'CC001', 'PC001', 'General Affair', 'Elektronik', 'Laptop', 'Laptop Dell Latitude 5440', 'Divisi IT', 'AST-001', '1.002.3000.002', 'Digunakan untuk operasional staf IT', '2026-06-21 20:37:41', '2026-06-21 20:37:41'),
(9, 'CC002', 'PC002', 'Operasional', 'Kendaraan', 'Sepeda Motor', 'Honda Vario 160', 'Bagian Logistik', 'AST-002', '1.003.3000.003', 'Kendaraan operasional pengiriman dokumen', '2026-06-21 20:37:41', '2026-06-21 20:37:41'),
(10, 'CC003', 'PC003', 'Keuangan', 'Peralatan Kantor', 'Printer', 'Epson L3250', 'Ruang Finance', 'AST-003', '1.004.3000.004', 'Printer untuk kebutuhan administrasi dan laporan', '2026-06-21 20:37:41', '2026-06-21 20:37:41'),
(11, '2000', '3000', 'Unit Bisnis Banjarmasin', 'asdjksa', 'asdas', 'asda', 'asdhsa', 'ahsdoisa', '1.005.3000.005', 'adksaj', '2026-06-22 09:12:22', '2026-06-22 09:12:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `image_settings`
--

CREATE TABLE `image_settings` (
  `id` int(11) NOT NULL,
  `image_key` varchar(100) NOT NULL COMMENT 'Identifier unik gambar, contoh: logo_main',
  `image_value` text NOT NULL COMMENT 'URL atau path file gambar',
  `image_type` enum('url','upload') NOT NULL DEFAULT 'url' COMMENT 'Jenis sumber gambar: url = link eksternal, upload = file lokal',
  `updated_by` int(11) DEFAULT NULL COMMENT 'ID user yang terakhir mengubah (FK ke tabel users)',
  `updated_at` datetime DEFAULT NULL COMMENT 'Waktu terakhir diubah'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pengaturan gambar dinamis untuk seluruh aplikasi KFA';

--
-- Dumping data untuk tabel `image_settings`
--

INSERT INTO `image_settings` (`id`, `image_key`, `image_value`, `image_type`, `updated_by`, `updated_at`) VALUES
(1, 'logo_main', 'settings/logo_main_1781129938.png', 'upload', 1, '2026-06-10 22:18:58'),
(2, 'bg_login', 'settings/bg_login_1779622059.jpeg', 'upload', 1, '2026-05-24 18:27:40'),
(3, 'bg_lupa_password', 'https://www.kimiafarmaapotek.co.id/wp-content/uploads/2021/10/CN-KFA-04-scaled.jpg', 'url', 1, '2026-05-06 02:48:14'),
(4, 'logo_profile_topbar', 'settings/logo_profile_topbar_1779622300.png', 'upload', 1, '2026-05-24 18:31:40'),
(5, 'foto_profil_default', 'settings/foto_profil_default_1781129963.png', 'upload', 1, '2026-06-10 22:19:23'),
(6, 'logo_driver_navbar', 'settings/logo_driver_navbar_1779622316.png', 'upload', 1, '2026-05-24 18:31:56'),
(7, 'logo_driver_footer', 'settings/logo_driver_footer_1780942623.png', 'upload', 1, '2026-06-09 01:17:03'),
(8, 'logo_acmonitoring_header', 'settings/logo_acmonitoring_header_1779622327.png', 'upload', 1, '2026-05-24 18:32:07'),
(9, 'logo_tab', 'settings/logo_tab_1780944725.png', 'upload', 1, '2026-06-09 01:52:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_supir` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `tanggal_tugas` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `penumpang` varchar(200) NOT NULL,
  `tujuan` varchar(200) NOT NULL,
  `keperluan` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan_aset`
--

CREATE TABLE `kendaraan_aset` (
  `id` int(11) UNSIGNED NOT NULL,
  `no_posisi` varchar(50) NOT NULL COMMENT 'Nomor posisi aset di lokasi',
  `branch_manager` varchar(100) NOT NULL COMMENT 'Cabang',
  `jenis_kendaraan` varchar(50) NOT NULL COMMENT 'Jenis: Mobil, Motor.',
  `merk` varchar(50) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `warna` varchar(30) DEFAULT NULL,
  `tahun_pembuatan` year(4) DEFAULT NULL,
  `no_mesin` varchar(100) DEFAULT NULL,
  `no_rangka` varchar(100) DEFAULT NULL,
  `no_bpkb` varchar(100) DEFAULT NULL,
  `no_polisi` varchar(20) DEFAULT NULL,
  `masa_berakhir_1th` date DEFAULT NULL COMMENT 'Tanggal berakhir pajak 1 tahunan',
  `masa_berakhir_5th` date DEFAULT NULL COMMENT 'Tanggal berakhir STNK 5 tahunan',
  `status` varchar(30) NOT NULL COMMENT 'Status Aset: Layak, Tidak Layak, Dilelang, Hilang',
  `tahun_perolehan` year(4) DEFAULT NULL COMMENT 'Tahun aset diperoleh',
  `harga_perolehan` bigint(20) DEFAULT NULL COMMENT 'Harga perolehan aset dalam Rupiah',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel data aset kendaraan operasional Kimia Farma';

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `tipe_mobil` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `merk`, `plat_nomor`, `tipe_mobil`) VALUES
(2, 'Isuzu Elf', 'B 4432 KFB', 'Truck Engkel'),
(3, 'toyota', 'B 21638 F', 'Box');

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'visit.ga.home', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(2, 'visit.ga.kendaraan.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(3, 'visit.ga.tanah_bangunan.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(4, 'visit.ga.atk.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(5, 'visit.ga.atk.barang_keluar', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(6, 'visit.ga.atk.riwayat', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(7, 'visit.ga.biaya.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(8, 'visit.ga.dir.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(9, 'visit.driver.home', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(10, 'visit.driver.jadwal.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(11, 'visit.driver.mobil.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(12, 'visit.driver.supir.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(13, 'visit.ac.monitoring.index', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20'),
(14, 'visit.ac.monitoring.notifikasi', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_jadwal`
--

CREATE TABLE `riwayat_jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_supir` int(11) NOT NULL,
  `id_mobil` int(11) NOT NULL,
  `tanggal_tugas` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `penumpang` varchar(200) NOT NULL,
  `tujuan` varchar(200) NOT NULL,
  `keperluan` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat_jadwal`
--

INSERT INTO `riwayat_jadwal` (`id_jadwal`, `id_supir`, `id_mobil`, `tanggal_tugas`, `created_at`, `jam_mulai`, `jam_selesai`, `penumpang`, `tujuan`, `keperluan`) VALUES
(5, 1, 1, '2026-05-24', '2026-05-24 11:15:25', '18:14:00', '20:00:00', 'mbuh', 'MENARA TERATAI', 'rapat'),
(6, 3, 1, '2026-06-22', '2026-06-22 08:19:54', '22:19:00', '23:00:00', 'adjsakdnsa', 'asdikasdoas', 'kasldasasdsa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-06-17 15:20:20', '2026-06-17 15:20:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `supir`
--

CREATE TABLE `supir` (
  `id_supir` int(11) NOT NULL,
  `nama_supir` varchar(100) NOT NULL,
  `status` enum('aktif','idle','offline') DEFAULT 'offline',
  `nip` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `supir`
--

INSERT INTO `supir` (`id_supir`, `nama_supir`, `status`, `nip`) VALUES
(6, 'asdhsaodiasoi', 'idle', '12938128321'),
(7, 'asdsa', 'idle', '31231242143');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tanah_bangunan_aset`
--

CREATE TABLE `tanah_bangunan_aset` (
  `id` int(11) UNSIGNED NOT NULL,
  `kode_sap` varchar(50) DEFAULT NULL COMMENT 'Kode SAP aset (pengganti kode_sop)',
  `no_asset_tanah` varchar(100) NOT NULL COMMENT 'Nomor unik aset tanah/bangunan',
  `branch_manager` varchar(100) NOT NULL COMMENT 'Penanggung jawab / Branch Manager lokasi',
  `digunakan_sebagai` varchar(100) DEFAULT NULL COMMENT 'Misalnya: Kantor Cabang, Gudang, Apotek',
  `penggunaan` varchar(100) DEFAULT NULL COMMENT 'Misalnya: Operasional & Pelayanan, Investasi',
  `no_posisi_gedung` varchar(50) DEFAULT NULL COMMENT 'Nomor posisi internal gedung/bangunan',
  `alamat` text NOT NULL COMMENT 'Alamat lengkap aset',
  `luas_tanah` decimal(10,2) NOT NULL COMMENT 'Luas tanah dalam meter persegi (m²)',
  `luas_bangunan` decimal(10,2) DEFAULT NULL COMMENT 'Luas bangunan dalam meter persegi (m²)',
  `tahun_perolehan` year(4) DEFAULT NULL COMMENT 'Tahun aset diperoleh',
  `nomor_sertifikat_baru` varchar(100) NOT NULL COMMENT 'Nomor sertifikat SHM/SHGB',
  `masa_berlaku` date DEFAULT NULL COMMENT 'Tanggal berakhir masa berlaku sertifikat (jika SHGB)',
  `status` varchar(50) NOT NULL COMMENT 'Status Aset: Aktif/SHM, Aktif/SHGB, Non-Aktif/Dijual, Sengketa',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel data aset properti tanah dan bangunan Kimia Farma';

--
-- Dumping data untuk tabel `tanah_bangunan_aset`
--

INSERT INTO `tanah_bangunan_aset` (`id`, `kode_sap`, `no_asset_tanah`, `branch_manager`, `digunakan_sebagai`, `penggunaan`, `no_posisi_gedung`, `alamat`, `luas_tanah`, `luas_bangunan`, `tahun_perolehan`, `nomor_sertifikat_baru`, `masa_berlaku`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(122, 'SAP-100293', 'AST-001923', 'Surabaya', 'Kantor Cabang', 'Operasional', 'G-01', 'Jl. Raya Darmo No. 45, Surabaya', 500.00, 350.00, '2015', 'SHM No. 12345/Surabaya', NULL, 'Milik Sendiri', 'Kondisi baik, digunakan penuh.', '2026-06-22 08:54:32', '2026-06-22 08:54:32'),
(123, 'SAP-100294', 'AST-001924', 'Bandung', 'Gudang Logistik', 'Penyimpanan', 'G-02', 'Jl. Soekarno Hatta No. 102, Bandung', 1200.00, 800.00, '2018', 'SHGB No. 6789/Bandung', '2038-12-31', 'Hak Guna Bangunan', 'Atap memerlukan sedikit perbaikan.', '2026-06-22 08:54:32', '2026-06-22 08:54:32'),
(124, 'SAP-100295', 'AST-001925', 'Kantor Pusat', 'Gedung Kantor', 'Utama', 'G-03', 'Jl. MH Thamrin No. 21, Jakarta', 2500.00, 4500.00, '2010', 'SHM No. 98765/Jakarta', NULL, 'Milik Sendiri', 'Gedung Utama Pusat, 5 lantai.', '2026-06-22 08:54:32', '2026-06-22 08:54:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_monitoring`
--

CREATE TABLE `tb_monitoring` (
  `id` int(11) NOT NULL,
  `kode_ga` varchar(50) DEFAULT NULL,
  `lokasi` varchar(100) NOT NULL,
  `tgl_perawatan_terakhir` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Normal','Wajib Service') NOT NULL,
  `nama_barang` text NOT NULL,
  `jenis_barang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_monitoring`
--

INSERT INTO `tb_monitoring` (`id`, `kode_ga`, `lokasi`, `tgl_perawatan_terakhir`, `keterangan`, `status`, `nama_barang`, `jenis_barang`) VALUES
(20, 'AC-101', 'Kantor Admin', '2025-11-13', 'Perawatan rutin ke-1', 'Normal', 'Samsung', 'AC'),
(21, 'AC-102', 'Ruang Meeting', '2026-03-11', 'Perawatan rutin ke-2', 'Wajib Service', 'Panasonic', 'AC'),
(22, 'AC-103', 'Ruang Server', '2025-10-16', 'Perawatan rutin ke-3', 'Wajib Service', 'Daikin', 'AC'),
(23, 'AC-104', 'Lobby', '2025-11-18', 'Perawatan rutin ke-4', 'Normal', 'Sharp', 'AC'),
(24, 'AC-105', 'Ruang Server', '2026-01-17', 'Perawatan rutin ke-5', 'Wajib Service', 'Panasonic', 'AC');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `last_login` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `last_login`, `status`, `created_at`) VALUES
(1, 'adminkfa', 'kimiafarma@gmail.com', '$2y$12$niJpVZoDVRfOZX3RD/rUTu72RFsy2I82cnHKCEtziSuIlOk1Bz7Qe', 'Administrator GA', 'admin', '2026-06-22 15:34:09', 'active', '2026-02-04 10:01:31'),
(3, 'rendi12', 'asdnasndsa@gmail.com', '$2y$12$1s4dX3HF0LUojjrDKD3yAOtvqHBunEAb4U.79xzwXOfLW66RjjRQi', 'aksdbkjsadsnjk', 'user', '2026-06-22 08:21:01', 'active', '2026-06-22 08:11:38');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `atk_katalog`
--
ALTER TABLE `atk_katalog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_atk_katalog_keterangan` (`keterangan`);

--
-- Indeks untuk tabel `atk_transaksi`
--
ALTER TABLE `atk_transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_atk_transaksi_id_barang` (`id_barang`),
  ADD KEY `idx_atk_transaksi_jenis` (`jenis`);

--
-- Indeks untuk tabel `dir_aset`
--
ALTER TABLE `dir_aset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_kode_aset` (`kode_aset`),
  ADD UNIQUE KEY `uniq_id_aset` (`id_aset`);

--
-- Indeks untuk tabel `image_settings`
--
ALTER TABLE `image_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_image_key` (`image_key`),
  ADD KEY `fk_image_settings_user` (`updated_by`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_supir` (`id_supir`),
  ADD KEY `id_mobil` (`id_mobil`);

--
-- Indeks untuk tabel `kendaraan_aset`
--
ALTER TABLE `kendaraan_aset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_mesin` (`no_mesin`),
  ADD UNIQUE KEY `no_rangka` (`no_rangka`),
  ADD UNIQUE KEY `no_bpkb` (`no_bpkb`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD UNIQUE KEY `plat_nomor` (`plat_nomor`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_type`,`model_id`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_type`,`model_id`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_index` (`role_id`);

--
-- Indeks untuk tabel `supir`
--
ALTER TABLE `supir`
  ADD PRIMARY KEY (`id_supir`);

--
-- Indeks untuk tabel `tanah_bangunan_aset`
--
ALTER TABLE `tanah_bangunan_aset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_asset_tanah` (`no_asset_tanah`),
  ADD UNIQUE KEY `nomor_sertifikat_baru` (`nomor_sertifikat_baru`),
  ADD UNIQUE KEY `kode_sap` (`kode_sap`);

--
-- Indeks untuk tabel `tb_monitoring`
--
ALTER TABLE `tb_monitoring`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `atk_katalog`
--
ALTER TABLE `atk_katalog`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT untuk tabel `atk_transaksi`
--
ALTER TABLE `atk_transaksi`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT untuk tabel `dir_aset`
--
ALTER TABLE `dir_aset`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `image_settings`
--
ALTER TABLE `image_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=563;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kendaraan_aset`
--
ALTER TABLE `kendaraan_aset`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `supir`
--
ALTER TABLE `supir`
  MODIFY `id_supir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `tanah_bangunan_aset`
--
ALTER TABLE `tanah_bangunan_aset`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT untuk tabel `tb_monitoring`
--
ALTER TABLE `tb_monitoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `image_settings`
--
ALTER TABLE `image_settings`
  ADD CONSTRAINT `fk_image_settings_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_supir`) REFERENCES `supir` (`id_supir`) ON DELETE CASCADE,
  ADD CONSTRAINT `jadwal_ibfk_2` FOREIGN KEY (`id_mobil`) REFERENCES `mobil` (`id_mobil`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
