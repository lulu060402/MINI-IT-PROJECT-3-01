-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2025 at 12:33 PM
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
-- Database: `server_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE `machines` (
  `id` int(11) NOT NULL,
  `hostel_block` int(11) NOT NULL DEFAULT 1,
  `name` varchar(50) NOT NULL,
  `status` enum('available','in_use','paused','ready_to_collect') DEFAULT 'available',
  `timer_end` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT 30,
  `paused_time` datetime DEFAULT NULL,
  `remaining_time` int(11) DEFAULT NULL,
  `collection_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `hostel_block`, `name`, `status`, `timer_end`, `duration`, `paused_time`, `remaining_time`, `collection_photo`) VALUES
(1, 1, 'HB1 Washer 1', 'available', NULL, 2, NULL, NULL, 'uploads/collection_photos/collection_1_1750069939.jpg'),
(2, 1, 'HB1 Washer 2', 'available', NULL, 5, NULL, NULL, NULL),
(3, 1, 'HB1 Dryer 1', 'available', NULL, 5, NULL, NULL, NULL),
(4, 1, 'HB1 Dryer 2', 'available', NULL, 5, NULL, NULL, NULL),
(6, 1, 'HB1 Premium Elite Pro Washer ', 'available', NULL, 2, NULL, NULL, NULL),
(7, 2, 'HB2 Washer 1', 'in_use', '2025-05-15 05:07:08', 45, NULL, NULL, NULL),
(8, 2, 'HB2 Dryer 1', 'in_use', '2025-05-14 22:05:40', 45, NULL, NULL, NULL),
(9, 3, 'HB3 Washer 1', 'in_use', '2025-05-15 05:22:22', 60, NULL, NULL, NULL),
(10, 3, 'HB3 Dryer 1', 'in_use', '2025-05-15 05:03:50', 5, NULL, NULL, NULL),
(11, 4, 'HB4 Washer 1', 'available', NULL, 2, NULL, NULL, NULL),
(12, 4, 'HB4 Dryer 1', 'in_use', '2025-05-15 04:28:43', 5, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `points_transactions`
--

CREATE TABLE `points_transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `points_change` int(11) NOT NULL,
  `transaction_type` enum('earn','redeem','adjustment','expiry') NOT NULL,
  `transaction_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID of related redemption'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_date` datetime DEFAULT current_timestamp(),
  `problem_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `urgency` enum('low','medium','high') DEFAULT 'low',
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `screenshot_path` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `machine_id` int(11) DEFAULT NULL,
  `collection_photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_date`, `problem_type`, `description`, `urgency`, `status`, `screenshot_path`, `username`, `email`, `machine_id`, `collection_photo`) VALUES
(1, '2025-05-22 05:11:02', 'Web Application Bugs', 'qw', 'low', 'in_progress', NULL, '', '', NULL, NULL),
(2, '2025-05-22 05:12:24', 'Washing Machine Issues', 'ee', 'medium', 'resolved', NULL, '', '', NULL, NULL),
(3, '2025-05-22 05:21:04', 'Web Application Bugs', 'i am dying ', 'high', 'resolved', 'uploads/682e43c03fd42_u3e2l1jt0ar61.jpg', '', '', NULL, NULL),
(4, '2025-05-22 05:23:05', 'Other', 'teest', 'medium', 'resolved', 'uploads/682e44390066f_Screenshot 2025-02-04 161030.png', '', '', NULL, NULL),
(5, '2025-05-22 05:46:21', 'Washing Machine Issues', 'qwqq', 'high', 'pending', 'uploads/682e49ad8553e_xi-jinping-pooh.gif', '', '', NULL, NULL),
(6, '2025-05-22 10:25:35', 'Web Application Bugs', 'explode', 'high', 'in_progress', 'uploads/682e8b1f7419d_a1775f8e3f198023d57477c41203c2ad_746102184942528154.jpg', '', '', NULL, NULL),
(7, '2025-05-28 14:52:51', 'Account Problem', 'big big', 'medium', 'resolved', 'uploads/6836b2c3bb1f5_artworks-000161913473-5mwrx1-t500x500.jpg', 'user', 'user@gmail.com', NULL, NULL),
(8, '2025-05-28 15:03:52', 'Washing Machine Issues', 'big bigb igbig ', 'high', 'resolved', 'uploads/6836b55875fba_112331231212332452342324532refd5tyrrfbd 356ybdv s5t3edfry6afdv.png', 'user', 'user@gmail.com', NULL, NULL),
(9, '2025-06-16 18:29:57', 'Collection Proof', 'Collection photo uploaded for machine HB1 Washer 1', 'low', 'resolved', 'uploads/collection_photos/collection_1_1750069797.jpg', NULL, NULL, 1, NULL),
(10, '2025-06-16 18:32:19', 'Collection Proof', 'Collection photo uploaded for machine HB1 Washer 1', 'low', 'resolved', 'uploads/collection_photos/collection_1_1750069939.jpg', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rewards`
--

CREATE TABLE `rewards` (
  `reward_id` int(11) NOT NULL,
  `reward_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `points_required` int(11) NOT NULL,
  `stock_quantity` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rewards`
--

INSERT INTO `rewards` (`reward_id`, `reward_name`, `description`, `points_required`, `stock_quantity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Free maggie hot cup', '1x MAGGIE KARI, 1x MAGGIE TOMYAM', 5000, 100, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22'),
(2, 'GARDENIA BREAD', '1x CHOCOLATE, 1x CORN', 500, 50, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22'),
(3, 'FREE TOWEL', '1x TOWEL (random colour)', 1000, 30, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22'),
(4, 'Free 100 PLUS', '1x 100 PLUS (can choose any flavour)', 500, 200, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22'),
(5, 'FREE ICE CREAM', '1x CUP ICE CREAM', 350, 150, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22'),
(6, 'DIY MILO/COFFEE PACKETS', '1x MILO PACKET, 1x COFFEE PACKET', 800, 80, 1, '2025-06-09 08:47:22', '2025-06-09 08:47:22');

-- --------------------------------------------------------

--
-- Table structure for table `reward_redemptions`
--

CREATE TABLE `reward_redemptions` (
  `redemption_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reward_id` int(11) NOT NULL,
  `redemption_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `points_used` int(11) NOT NULL,
  `status` enum('pending','claimed','expired','cancelled') DEFAULT 'pending',
  `details` text DEFAULT NULL,
  `claim_code` varchar(20) DEFAULT NULL,
  `claimed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `points`, `created_at`, `updated_at`) VALUES
(1, 'user', 'user@gmail.com', '$2y$10$ub8fQjHZuWEEzaNR0YAyGOzxiJ8IKyXS9wE76jruSF4T9fALnRo7a', 160, '2025-06-09 08:48:24', '2025-06-09 09:20:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `points_transactions`
--
ALTER TABLE `points_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `transaction_time` (`transaction_time`),
  ADD KEY `transaction_type` (`transaction_type`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rewards`
--
ALTER TABLE `rewards`
  ADD PRIMARY KEY (`reward_id`),
  ADD UNIQUE KEY `reward_name` (`reward_name`);

--
-- Indexes for table `reward_redemptions`
--
ALTER TABLE `reward_redemptions`
  ADD PRIMARY KEY (`redemption_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reward_id` (`reward_id`),
  ADD KEY `status` (`status`),
  ADD KEY `claim_code` (`claim_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `points_transactions`
--
ALTER TABLE `points_transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rewards`
--
ALTER TABLE `rewards`
  MODIFY `reward_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reward_redemptions`
--
ALTER TABLE `reward_redemptions`
  MODIFY `redemption_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `points_transactions`
--
ALTER TABLE `points_transactions`
  ADD CONSTRAINT `points_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reward_redemptions`
--
ALTER TABLE `reward_redemptions`
  ADD CONSTRAINT `reward_redemptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reward_redemptions_ibfk_2` FOREIGN KEY (`reward_id`) REFERENCES `rewards` (`reward_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
