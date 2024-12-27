-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2024 at 06:11 AM
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
-- Database: `medical`
--

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `disease_name` varchar(255) NOT NULL,
  `hospital_recommended` varchar(255) DEFAULT NULL,
  `severity_level` enum('mild','moderate','critical') NOT NULL,
  `medication` text DEFAULT NULL,
  `diagnosis_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`id`, `pid`, `disease_name`, `hospital_recommended`, `severity_level`, `medication`, `diagnosis_date`) VALUES
(1, 1, 'Pneumonia', 'Coast Provincial General Hospital', 'mild', 'Generic Symptom Relief Medication', '2024-12-02 07:38:58'),
(2, 2, 'Bronchitis', 'Kenyatta National Hospital', 'critical', 'Generic Symptom Relief Medication', '2024-12-02 07:41:42'),
(3, 3, 'Pneumonia', 'Aga Khan University Hospital', 'moderate', 'Generic Symptom Relief Medication', '2024-12-02 07:43:56'),
(4, 4, 'Flu', 'Coast Provincial General Hospital', 'mild', 'Oseltamivir, Ibuprofen, Rest', '2024-12-02 07:46:13'),
(6, 7, 'Common Cold', 'Coast Provincial General Hospital', 'mild', 'Paracetamol, Rest, Hydration', '2024-12-03 05:06:44'),
(7, 8, 'Pneumonia', 'Nairobi Hospital', 'moderate', 'Generic Symptom Relief Medication', '2024-12-03 05:42:02'),
(11, 9, 'Common Cold', 'Aga Khan University Hospital', 'moderate', 'Paracetamol, Rest, Hydration', '2024-12-06 18:43:29');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `birth_place` varchar(100) NOT NULL,
  `currentcity` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `fname`, `lname`, `gender`, `email`, `contact`, `password`, `date_of_birth`, `birth_place`, `currentcity`, `age`, `religion`, `registration_date`) VALUES
(1, 'john', 'kiarie', 'male', 'john782@gmail.com', '0717100999', '$2y$10$gd05VZrH/yAHj531d.SRX.rF4M3MjZ6rNxgoYnX8eUO4HiOSbW8vW', '2000-03-20', 'nakuru', 'Murang\\\'a', 24, 'Muslim', '2024-12-02 07:37:26'),
(2, 'Mercy', 'Wambui', 'male', 'johnmercy7877@gmail.com', '0717100999', '$2y$10$kwlcTLFs4.7aiYvfDi.PvuMPwodCUJI40cRnYX8qmBmQ57Z0OOqqW', '1988-12-12', 'Nairobi', 'Mombasa', 35, 'Christian', '2024-12-02 07:40:53'),
(3, 'james', 'kamau', 'male', 'saimonmercy7877@gmail.com', '0717100999', '$2y$10$bY5Wzj1tLLtz.9x2IVzg8eJMONosiLUWAOV.Hzgz0Bqc0d7GqUE76', '1988-12-12', 'Kiambu', 'Murang\\\'a', 35, 'Christian', '2024-12-02 07:43:18'),
(4, 'john', 'k', 'male', 'johnjgh782@gmail.com', '0717100996', '$2y$10$St7TilcYv.4CdnWgGkQCPuzZJUna0vKDbmWOeJrfEBf0HXac/q/De', '2000-03-20', 'Nakuru', 'Murang\\\'a', 24, 'Other', '2024-12-02 07:45:37'),
(6, 'hjhj', 'mwangi', 'female', 'john253@gmail.com', '0758085920', '$2y$10$PjxFGCyNDedsLbNv9iYfxeM.vggbuYDvgG3f6CNTMCUsz3e6kK8aG', '2002-06-12', 'Kajiado', 'Kisumu', 22, 'Muslim', '2024-12-03 01:18:35'),
(7, 'Grace', 'kiarie', 'male', 'ghf@gmail.com', '0758085920', '$2y$10$iICNwDvAQ7q.KgRGQiumFe5hWwcXcrOOg2j9EmTx0bFsyFYiUIjnm', '1978-09-12', 'Nairobi', 'Nairobi', 46, 'Christian', '2024-12-03 05:03:39'),
(8, 'Beth', 'Wanjiku', 'female', 'shiks@gmail.com', '0180567434', '$2y$10$ik5FzvWya3abKUBtm2toVOSykfo5HV9RBsuf0M64OjWzqVXmmMQVi', '2005-08-20', 'Kilifi', 'Kiambu', 19, 'Other', '2024-12-03 05:40:59'),
(9, 'Alice', 'Wanjiru', 'male', 'alice#@gmail.com', '0758085920', '$2y$10$pw.4zjL5HteZ2RL9LNGpiuBG16X0bTy4TPzOEiDOt2SPAStvXuWSK', '2001-06-12', 'Kisumu', 'Kitui', 23, 'Christian', '2024-12-03 08:00:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `diseases`
--
ALTER TABLE `diseases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `diseases`
--
ALTER TABLE `diseases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diseases`
--
ALTER TABLE `diseases`
  ADD CONSTRAINT `diseases_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
