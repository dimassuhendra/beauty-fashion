-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 06:38 AM
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
-- Database: `beauty`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin@beautyfashion.com', '$2y$10$Ftz16CAGKAQclPpuZZZL8OyFGtFwk1LWANyso6coD6E8I.Y5bCJ0u', 'admin@beautyfashion.com', '2025-10-21 08:42:38');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `created_at`) VALUES
(1, 'Pakaian Wanita', 'pakaian-wanita', 'Berbagai jenis pakaian wanita muslimah, seperti gamis, tunik, dan blouse.', '2025-10-21 09:33:15'),
(2, 'Mukena', 'mukena', 'Peralatan sholat khusus untuk wanita dengan berbagai model dan bahan.', '2025-10-21 09:33:15'),
(3, 'Kerudung & Hijab', 'kerudung-hijab', 'Koleksi kerudung, hijab instan, dan pashmina.', '2025-10-21 09:33:15'),
(4, 'Alat Sholat Pria', 'alat-sholat-pria', 'Sajadah, peci, dan sarung untuk pria.', '2025-10-21 09:33:15'),
(5, 'Aksesori Muslim', 'aksesori-muslim', 'Berbagai aksesori penunjang busana muslim.', '2025-10-21 09:33:15');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `discount_type` enum('fixed','percent') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `minimum_purchase` decimal(10,2) DEFAULT 0.00,
  `valid_from` date NOT NULL,
  `valid_until` date NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address_id` int(11) NOT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_status` enum('Pending Payment','Processing','Shipped','Completed','Cancelled') NOT NULL DEFAULT 'Pending Payment',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `slug`, `description`, `price`, `stock`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'GMS-ELEG-001', 'Gamis Rayon Elegant Motif Bunga', 'gamis-rayon-elegant-motif-bunga', 'Gamis berbahan rayon premium dengan motif bunga elegan. Nyaman dan adem untuk dipakai sehari-hari.', 189000.00, 150, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(2, 1, 'TNK-STRIP-005', 'Tunik Panjang Casual Garis', 'tunik-panjang-casual-garis', 'Tunik dengan desain kasual garis-garis, cocok untuk kegiatan santai.', 95000.00, 80, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(3, 1, 'BLS-KNTG-010', 'Blouse Kerja Kantong Depan', 'blouse-kerja-kantong-depan', 'Blouse formal dengan dua kantong di bagian depan. Bahan tidak mudah kusut.', 135000.00, 45, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(4, 2, 'MK-PRM-SUT-15', 'Mukena Sutra Premium Bordir Manual', 'mukena-sutra-premium-bordir-manual', 'Mukena eksklusif dari bahan sutra dengan hiasan bordir tangan.', 450000.00, 20, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(5, 2, 'MK-BALI-102', 'Mukena Bali Rayon Tie Dye', 'mukena-bali-rayon-tie-dye', 'Mukena motif Bali yang adem dan ringan, cocok untuk traveling.', 125000.00, 210, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(6, 3, 'KRD-PASH-M08', 'Pashmina Ceruty Babydoll Hitam', 'pashmina-ceruty-babydoll-hitam', 'Pashmina instan bahan ceruty yang mudah dibentuk dan tidak licin.', 65000.00, 300, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(7, 3, 'HJ-INST-033', 'Hijab Segi Empat Voal Laser Cut', 'hijab-segi-empat-voal-laser-cut', 'Hijab segi empat berbahan voal dengan finishing laser cut di pinggiran.', 79000.00, 55, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(8, 4, 'SJD-TURK-044', 'Sajadah Tebal Motif Turki', 'sajadah-tebal-motif-turki', 'Sajadah impor dengan ketebalan ekstra dan motif khas Turki.', 155000.00, 35, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(9, 4, 'PC-BLCK-002', 'Peci Rajut Hitam Polos', 'peci-rajut-hitam-polos', 'Peci bahan rajut nyaman untuk sholat sehari-hari.', 25000.00, 99, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(10, 4, 'SRG-BHS-099', 'Sarung Tenun Motif Songket', 'sarung-tenun-motif-songket', 'Sarung tenun berkualitas dengan motif Songket eksklusif.', 220000.00, 15, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(11, 1, 'GMS-JNSY-200', 'Gamis Jeans Casual Kancing Depan', 'gamis-jeans-casual-kancing-depan', 'Gamis berbahan jeans ringan dengan kancing hidup di bagian depan (busui friendly).', 240000.00, 75, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(12, 1, 'TNK-OVER-301', 'Tunik Oversize Katun Twill', 'tunik-oversize-katun-twill', 'Tunik longgar dari bahan katun twill yang nyaman dan jatuh.', 175000.00, 62, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(13, 3, 'KRD-INST-111', 'Kerudung Instan Bergo Tali', 'kerudung-instan-bergo-tali', 'Kerudung instan bergo praktis dengan aksen tali di belakang.', 45000.00, 180, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(14, 5, 'AKS-BROS-012', 'Bros Hijab Mutiara Gold', 'bros-hijab-mutiara-gold', 'Aksesori bros elegan dengan hiasan mutiara sintetis.', 39000.00, 110, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(15, 5, 'AKS-MASK-022', 'Masker Kain Tali Sambung', 'masker-kain-tali-sambung', 'Masker kain premium dengan tali sambung untuk hijab.', 15000.00, 450, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(16, 2, 'MK-ANAK-500', 'Mukena Anak Karakter Unicorn', 'mukena-anak-karakter-unicorn', 'Mukena setelan untuk anak dengan motif kartun lucu.', 110000.00, 88, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 13:57:17'),
(17, 1, 'GMS-PLSK-601', 'Gamis Plisket Lengan Balon', 'gamis-plisket-lengan-balon', 'Gamis dengan bahan plisket premium, modis dan modern.', 265000.00, 30, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(18, 3, 'HJ-POLY-707', 'Hijab Paris Premium Polycotton', 'hijab-paris-premium-polycotton', 'Hijab paris bahan polycotton yang mudah diatur.', 50000.00, 160, NULL, 1, '2025-10-21 09:38:58', '2025-10-21 09:38:58'),
(19, 4, 'SRG-STD-810', 'Sarung Standar Motif Kotak-Kotak', 'sarung-standar-motif-kotak-kotak', 'Sarung tenun standar dengan motif kotak klasik.', 80000.00, 120, '', 1, '2025-10-21 09:38:58', '2025-10-21 14:07:09'),
(20, 1, 'BLS-SIMPLE-901', 'Blouse Simpel Lengan Pendek', 'blouse-simpel-lengan-pendek', 'Blouse simpel yang cocok dipadukan dengan celana kulot.', 70000.00, 40, '', 0, '2025-10-21 09:38:58', '2025-10-21 10:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `street_address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone_number`, `created_at`, `updated_at`) VALUES
(0, 'Dimas Suhendra', 'dimassuhendra0104@gmail.com', '$2y$10$P0d9UUT7XdX6Nt8XCH8syuLOtbxVhDnsj00YJ6XW.skNIl2yXknc.', '085780809099', '2025-10-21 13:20:27', '2025-10-21 13:20:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD UNIQUE KEY `coupon_code` (`coupon_code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD UNIQUE KEY `order_code` (`order_code`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
