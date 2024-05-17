-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2024 at 07:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `routing`
--

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `documentId` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `document` varchar(255) DEFAULT NULL,
  `recipient` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `documentType` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `actionsNeeded` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`documentId`, `title`, `document`, `recipient`, `department`, `documentType`, `note`, `purpose`, `actionsNeeded`) VALUES
(8, '', '', 'John Doe', 'HR Training Unit', 'Training Attendance Records', 'This is a note for the document.', 'For internal communication', 'Review and provide feedback');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ipAddress` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ipAddress`, `timestamp`) VALUES
(1, '::1', '2024-04-01 08:17:07'),
(2, '::1', '2024-04-01 08:17:08'),
(3, '::1', '2024-04-01 08:17:08'),
(4, '::1', '2024-04-01 08:17:42'),
(5, '::1', '2024-04-01 08:17:43'),
(6, '::1', '2024-04-01 08:17:59'),
(7, '::1', '2024-04-01 08:18:13'),
(8, '::1', '2024-04-01 08:18:13'),
(9, '::1', '2024-04-01 08:18:14'),
(10, '::1', '2024-04-01 08:18:14'),
(11, '::1', '2024-04-01 08:18:22'),
(12, '::1', '2024-04-01 08:19:02'),
(13, '::1', '2024-04-01 08:19:02'),
(14, '::1', '2024-04-01 08:19:03'),
(15, '::1', '2024-04-01 08:19:13'),
(16, '::1', '2024-04-01 08:19:13'),
(17, '::1', '2024-04-01 08:19:35'),
(18, '::1', '2024-04-01 08:19:36'),
(19, '::1', '2024-04-01 08:20:38'),
(20, '::1', '2024-04-01 08:20:39'),
(21, '::1', '2024-04-01 08:20:44'),
(22, '::1', '2024-04-01 08:20:44'),
(23, '::1', '2024-04-01 08:20:46'),
(24, '::1', '2024-04-01 08:20:51'),
(25, '::1', '2024-04-01 08:20:51'),
(26, '::1', '2024-04-01 08:20:52'),
(27, '::1', '2024-04-01 08:21:06'),
(28, '::1', '2024-04-01 08:21:06'),
(29, '::1', '2024-04-01 08:24:47'),
(30, '::1', '2024-04-01 08:30:44'),
(31, '::1', '2024-04-01 08:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `request_documents`
--

CREATE TABLE `request_documents` (
  `requestId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `documentId` int(11) NOT NULL,
  `trackingNumber` varchar(255) NOT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `releaseDate` timestamp NULL DEFAULT NULL,
  `location` varchar(255) NOT NULL,
  `status` enum('APPROVED','REJECTED','RELEASED','PENDING') NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_documents`
--

INSERT INTO `request_documents` (`requestId`, `userId`, `documentId`, `trackingNumber`, `remarks`, `releaseDate`, `location`, `status`, `createdAt`, `updatedAt`) VALUES
(2, 1, 8, 'TN-John-WTBI-1711986360-5311', 'this is a remark', '2024-04-01 15:46:00', 'Release', 'RELEASED', '2024-04-01 15:46:00', '2024-04-01 15:46:00');

-- --------------------------------------------------------

--
-- Table structure for table `user_cred`
--

CREATE TABLE `user_cred` (
  `userCredId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `accessLevel` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_cred`
--

INSERT INTO `user_cred` (`userCredId`, `userId`, `username`, `password`, `accessLevel`, `createdAt`, `updatedAt`) VALUES
(1, 1, 'test', '$2y$10$9IRB3i4vO4mwi8kLKgVTtOXlg7MVKR1emNO2pEjuYXAhLh04rgL7y', '3', '2024-04-01 14:30:29', '2024-04-01 14:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `userId` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `birthday` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `department` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`userId`, `name`, `email`, `address`, `birthday`, `department`) VALUES
(1, 'test', 'test@gmail.com', 'dito sa test', '2023-02-16 04:00:00', 'hr');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`documentId`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `request_documents`
--
ALTER TABLE `request_documents`
  ADD PRIMARY KEY (`requestId`),
  ADD KEY `fk_user` (`userId`),
  ADD KEY `fk_document` (`documentId`);

--
-- Indexes for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD PRIMARY KEY (`userCredId`),
  ADD KEY `fk_user_cred` (`userId`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `documentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `request_documents`
--
ALTER TABLE `request_documents`
  MODIFY `requestId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_cred`
--
ALTER TABLE `user_cred`
  MODIFY `userCredId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `request_documents`
--
ALTER TABLE `request_documents`
  ADD CONSTRAINT `fk_document` FOREIGN KEY (`documentId`) REFERENCES `documents` (`documentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`userId`) REFERENCES `user_info` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_cred`
--
ALTER TABLE `user_cred`
  ADD CONSTRAINT `fk_user_cred` FOREIGN KEY (`userId`) REFERENCES `user_info` (`userId`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
