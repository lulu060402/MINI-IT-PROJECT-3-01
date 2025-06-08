-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2025 at 11:34 AM
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
-- Database: `laundry_db`
--

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
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_date`, `problem_type`, `description`, `urgency`, `status`, `screenshot_path`, `username`, `email`) VALUES
(1, '2025-05-22 05:11:02', 'Web Application Bugs', 'qw', 'low', 'in_progress', NULL, '', ''),
(2, '2025-05-22 05:12:24', 'Washing Machine Issues', 'ee', 'medium', 'pending', NULL, '', ''),
(3, '2025-05-22 05:21:04', 'Web Application Bugs', 'i am dying ', 'high', 'resolved', 'uploads/682e43c03fd42_u3e2l1jt0ar61.jpg', '', ''),
(4, '2025-05-22 05:23:05', 'Other', 'teest', 'medium', 'resolved', 'uploads/682e44390066f_Screenshot 2025-02-04 161030.png', '', ''),
(5, '2025-05-22 05:46:21', 'Washing Machine Issues', 'qwqq', 'high', 'pending', 'uploads/682e49ad8553e_xi-jinping-pooh.gif', '', ''),
(6, '2025-05-22 10:25:35', 'Web Application Bugs', 'explode', 'high', 'resolved', 'uploads/682e8b1f7419d_a1775f8e3f198023d57477c41203c2ad_746102184942528154.jpg', '', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
