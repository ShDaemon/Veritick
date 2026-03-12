-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 07:05 AM
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
-- Database: `veritick`
--

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE `checkins` (
  `checkin_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `scanner_id` int(11) NOT NULL,
  `checkin_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `result` enum('OK','failed') NOT NULL,
  `raw_payload` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `location` varchar(255) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `organizer_id`, `title`, `description`, `date`, `location`, `total_seats`, `created_at`) VALUES
(1, 1, 'Tech Conference', 'GOOD', '2026-02-28 12:47:00', 'Atlantic', 500, '2026-02-26 03:11:29'),
(2, 1, 'Event 2', 'Generic Test Event', '2026-03-27 13:20:00', 'Atlantic Ocean', 500, '2026-03-05 05:48:39'),
(3, 1, 'qwe', 'qwe', '2026-03-25 16:53:00', 'Atlantic Ocean', 1000, '2026-03-05 06:19:55');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'USD',
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `provider_txn_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `qr_signature` varchar(255) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `used_at` datetime DEFAULT NULL,
  `used_by_scanner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `user_id`, `event_id`, `qr_code`, `qr_signature`, `issued_at`, `used`, `used_at`, `used_by_scanner_id`) VALUES
(1, 2, 1, 'VT-2-1-1772075674-30548bb3', '327158e8b70246bd6e6dba9b25e0815664a0b5d91ecaea6d3571ea5082f67bd6', '2026-02-26 03:14:34', 0, NULL, NULL),
(2, 1, 1, 'VT-1-1-1772075731-1ca9e463', '5483d7a97fb34f86d02ec8fdde2b16bfd9951dd7fb5b45e126875cfe8638fec7', '2026-02-26 03:15:31', 0, NULL, NULL),
(3, 1, 1, 'VT-1-1-1772076509-0aa70ff0', '87f5da6633f6a9120af178fd50de5652bb0c1aaaa10c136dbf79d6a7656c6374', '2026-02-26 03:28:29', 0, NULL, NULL),
(4, 1, 1, 'VT-1-1-1772689729-8e575cbb', '5530b273a687656b5cc59ca9661b8156111137938744bec17c7d23ed75f09a72', '2026-03-05 05:48:49', 0, NULL, NULL),
(5, 1, 2, 'VT-1-2-1772690329-df00790c', 'df5e581fd794d33df8b320ceab8d67d0b9f96b791557cec7e4c13c87400307a9', '2026-03-05 05:58:49', 1, '2026-03-05 11:29:14', 1),
(6, 1, 2, 'VT-1-2-1772690548-eff51450', '6bbbedcfd0247f3073b3a41f5566730dd7d3c2a1b8f2a98b7347dcc59b077e4b', '2026-03-05 06:02:28', 0, NULL, NULL),
(7, 3, 2, 'VT-3-2-1772691023-f739140f', '261ab746a52c517987394acbbd6f48c94c2e2ba0ca020b495fa030176b6f3870', '2026-03-05 06:10:23', 1, '2026-03-05 11:42:20', 1),
(8, 3, 2, 'VT-3-2-1772691087-8a425b4b', 'ab5af7373926833fba18db6f4e85a312a428f4833c581f9b03f11af44946d3ec', '2026-03-05 06:11:27', 0, NULL, NULL),
(9, 1, 3, 'VT-1-3-1772691621-23d78a43', 'fcfb3f3e76076f4c683f328a0deef895eb25092bf6392b874c5475c46b2d0c24', '2026-03-05 06:20:21', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'Anoop Kumar', 'kuganoop2005@gmail.com', '$2y$10$dB8dKpq2d8oyUkZ7dZrZueZKOsRWOuNeKdEiXZaQ7Ob9SpBza.4Ji', 'admin'),
(2, 'Conan', 'anoop2005ak@gmail.com', '$2y$10$TPjRF6a7EFGQ22rJbCfEpebuaACBiMmlRAbc6CkibmKpXhtZugxnm', 'user'),
(3, 'Anoop Kumar', '23051410@kiit.ac.in', '$2y$10$vk6LbWeSNWq7pNfsUGnOUu1C1G5NMipOUv.u5fcMPYH7BMEYWyPcu', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `checkins`
--
ALTER TABLE `checkins`
  ADD PRIMARY KEY (`checkin_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `provider_txn_id` (`provider_txn_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `checkins`
--
ALTER TABLE `checkins`
  MODIFY `checkin_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `checkins`
--
ALTER TABLE `checkins`
  ADD CONSTRAINT `checkins_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
