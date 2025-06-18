-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 02:39 PM
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
  `remaining_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`id`, `hostel_block`, `name`, `status`, `timer_end`, `duration`, `paused_time`, `remaining_time`) VALUES
(1, 1, 'HB1 Washer 1', 'available', NULL, 60, NULL, NULL),
(2, 1, 'HB1 Washer 2', 'available', NULL, 45, NULL, NULL),
(3, 1, 'HB1 Dryer 1', 'available', NULL, 2, NULL, NULL),
(4, 1, 'HB1 Dryer 2', 'available', NULL, 60, NULL, NULL),
(6, 1, 'HB1 Premium Elite Pro Washer ', 'available', NULL, 60, NULL, NULL),
(7, 2, 'HB2 Washer 1', 'in_use', '2025-05-15 05:07:08', 45, NULL, NULL),
(8, 2, 'HB2 Dryer 1', 'in_use', '2025-05-14 22:05:40', 45, NULL, NULL),
(9, 3, 'HB3 Washer 1', 'in_use', '2025-05-15 05:22:22', 60, NULL, NULL),
(10, 3, 'HB3 Dryer 1', 'in_use', '2025-05-15 05:03:50', 5, NULL, NULL),
(11, 4, 'HB4 Washer 1', 'available', NULL, 2, NULL, NULL),
(12, 4, 'HB4 Dryer 1', 'in_use', '2025-05-15 04:28:43', 5, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `machines`
--
ALTER TABLE `machines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `machines`
--
ALTER TABLE `machines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
