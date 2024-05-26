-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 26, 2024 at 10:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `loancalci`
--

-- --------------------------------------------------------

--
-- Table structure for table `loanaff`
--

CREATE TABLE `loanaff` (
  `id` int(11) NOT NULL,
  `token` mediumtext NOT NULL,
  `loan_amount` int(11) NOT NULL,
  `i_rate` float NOT NULL,
  `tenure` int(11) NOT NULL,
  `s_date` varchar(20) NOT NULL,
  `emi` float NOT NULL,
  `total_inter` float NOT NULL,
  `total_amount` float NOT NULL,
  `e_date` varchar(20) NOT NULL,
  `pdf_link` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loanaff`
--

INSERT INTO `loanaff` (`id`, `token`, `loan_amount`, `i_rate`, `tenure`, `s_date`, `emi`, `total_inter`, `total_amount`, `e_date`, `pdf_link`, `created_at`, `updated_at`) VALUES
(206, 'jhgvikhgvughb', 50000, 1.25, 24, '2024-05-22', 2111, 654, 50654, '2024-04-22', 'LoanEmi_1716621825.pdf', '2024-05-25 07:23:45', '2024-05-25 07:23:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loanaff`
--
ALTER TABLE `loanaff`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loanaff`
--
ALTER TABLE `loanaff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=207;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
