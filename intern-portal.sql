-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 08, 2025 at 04:59 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `intern-portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `announcement_id` int NOT NULL,
  `announcement_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `announcement_content` text NOT NULL,
  `company_id` int NOT NULL,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`announcement_id`, `announcement_title`, `announcement_content`, `company_id`, `created_at`) VALUES
(13, 'test', 'etsdfdsfsdf\r\n', 5, '2025-07-04');

-- --------------------------------------------------------

--
-- Table structure for table `applicant`
--

CREATE TABLE `applicant` (
  `applicant_id` int NOT NULL,
  `user_id` int NOT NULL,
  `vacancy_id` int NOT NULL,
  `application_status_id` int NOT NULL,
  `offer_status_id` int NOT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `applied_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applicant`
--

INSERT INTO `applicant` (`applicant_id`, `user_id`, `vacancy_id`, `application_status_id`, `offer_status_id`, `resume_url`, `applied_at`) VALUES
(12, 14, 5, 4, 1, 'Nur_Aisyah_Binti_Ahmad_Resume.pdf', '2025-06-22 11:45:00'),
(13, 15, 6, 1, 1, 'Lim_Wei_Jie_Resume.pdf', '2025-06-23 10:30:00'),
(14, 18, 7, 1, 1, 'Tan_Jia_Wei_Resume.pdf', '2025-06-23 13:00:00'),
(15, 19, 8, 2, 2, 'Siti_Nur_Hidayah_Resume.pdf', '2025-06-23 13:10:00'),
(16, 20, 9, 2, 1, 'Zulkifli_Bin_Omar_Resume.pdf', '2025-06-23 13:20:00'),
(17, 21, 10, 3, 3, 'Amira_Afiqah_Resume.pdf', '2025-06-23 13:30:00'),
(18, 22, 9, 4, 2, 'Daniel_Tan_Hong_Resume.pdf', '2025-06-23 13:40:00'),
(19, 14, 10, 1, 1, 'Nur_Aisyah_Binti_Ahmad_20250626034110.pdf', '2025-06-26 03:41:10');

-- --------------------------------------------------------

--
-- Table structure for table `application_status`
--

CREATE TABLE `application_status` (
  `application_status_id` int NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `application_status`
--

INSERT INTO `application_status` (`application_status_id`, `status_name`) VALUES
(1, 'Applied'),
(2, 'Interview'),
(3, 'Rejected'),
(4, 'Offered');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `company_id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `company_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `description` text,
  `website` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `banner` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `username`, `password`, `company_name`, `phone`, `email`, `area`, `state`, `description`, `website`, `verified`, `banner`, `logo`) VALUES
(5, 'google', '$2y$10$ZlpjHmWcovhYIIYe3254keEFAmo0BYuNsKjysP4N7HZ69V/a9o6mW', 'Google Malaysia', '0123456788', 'contact@google.com', 'Cyberjaya', 'Selangor', 'Tech giant specializing in AI and web services', 'https://google.com', 1, NULL, 'Google_Malaysia_logo.png'),
(6, 'amazon', '$2y$10$458GQX8oGXfsw14Xi5hYZu9.dAtF5.kBpo6z0I.CowJ7RuYJ.DW6y', 'Amazon Web Services', '0198887776', 'aws@amazon.com', 'KL Sentral', 'Kuala Lumpur', 'Cloud computing division of Amazon', 'https://aws.amazon.com', 1, NULL, NULL),
(7, '', '', 'Shopee Malaysia', '0145556667', 'info@shopee.com', 'Kuala Lumpur', 'Kuala Lumpur', 'E-commerce platform', 'https://shopee.com.my', 1, NULL, NULL),
(8, '', '', 'Petronas', '0157778888', 'info@petronas.com', 'KLCC', 'Kuala Lumpur', 'Oil and Gas Company', 'https://petronas.com', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `level_id` int NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`level_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Student'),
(3, 'Company');

-- --------------------------------------------------------

--
-- Table structure for table `offer_status`
--

CREATE TABLE `offer_status` (
  `offer_status_id` int NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `offer_status`
--

INSERT INTO `offer_status` (`offer_status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Accepted'),
(3, 'Declined');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `semester_id` int NOT NULL,
  `semester_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`semester_id`, `semester_name`, `start_date`, `end_date`, `is_active`, `created_at`) VALUES
(1, 'Semester 1 2024/2025', '2024-09-01', '2025-01-31', 0, '2024-08-01 10:00:00'),
(2, 'Semester 2 2024/2025', '2025-02-01', '2025-06-30', 1, '2025-01-15 10:00:00'),
(3, 'Semester 1 2025/2026', '2025-09-02', '2026-01-31', 0, '2025-07-01 10:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level_id` int NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `campus` varchar(255) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `full_name`, `email`, `password`, `level_id`, `area`, `state`, `phone_number`, `campus`, `course`, `profile_picture`) VALUES
(1, 'Admin', 'admin', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 1, NULL, NULL, NULL, NULL, NULL, ''),
(13, 'Microsoft HR', 'hr@microsoft.com', 'microsoft123', 3, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Nur Aisyah Binti Ahmad', '2023123456@student.uitm.edu.my', 'pass123', 2, 'Kota Bharu', 'Kelantan', '0123456789', 'UiTM Kelantan', 'Bachelor of Information Technology', 'Nur_Aisyah_Binti_Ahmad.jpeg'),
(15, 'Lim Wei Jie', '2023567890@student.uitm.edu.my', 'pass456', 2, 'Johor Bahru', 'Johor', '0179876543', 'UiTM Johor', 'Diploma in Business Studies', ''),
(16, 'Google HR', 'hr@google.com', 'google123', 3, NULL, NULL, '0121112223', NULL, NULL, NULL),
(17, 'Amazon HR', 'hr@amazon.com', 'amazon123', 3, NULL, NULL, '0193334445', NULL, NULL, NULL),
(18, 'Tan Jia Wei', '2023123001@student.uitm.edu.my', 'pass789', 2, 'Melaka', 'Melaka', '0138765432', 'UiTM Melaka', 'Diploma in Accounting', ''),
(19, 'Siti Nur Hidayah', '2023111122@student.uitm.edu.my', 'siti123', 2, 'Alor Setar', 'Kedah', '0112233445', 'UiTM Kedah', 'Bachelor of Business Administration', ''),
(20, 'Zulkifli Bin Omar', '2023555555@student.uitm.edu.my', 'zulkifli456', 2, 'Ipoh', 'Perak', '0171122334', 'UiTM Perak', 'Diploma in Mechanical Engineering', ''),
(21, 'Amira Afiqah', '2023009988@student.uitm.edu.my', 'amira789', 2, 'Kuantan', 'Pahang', '0195544332', 'UiTM Pahang', 'Bachelor of Mass Communication', ''),
(22, 'Daniel Tan Hong', '2023777888@student.uitm.edu.my', 'daniel321', 2, 'Seremban', 'Negeri Sembilan', '0129988776', 'UiTM Negeri Sembilan', 'Diploma in Finance', ''),
(23, 'Shopee HR', 'hr@shopee.com', 'shopee123', 3, NULL, NULL, '0111233211', NULL, NULL, NULL),
(24, 'Petronas HR', 'hr@petronas.com', 'petronas123', 3, NULL, NULL, '0131112223', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vacancy`
--

CREATE TABLE `vacancy` (
  `vacancy_id` int NOT NULL,
  `company_id` int NOT NULL,
  `semester_id` int DEFAULT NULL,
  `vacancy_status_id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  `elaun` decimal(10,2) DEFAULT NULL,
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vacancy`
--

INSERT INTO `vacancy` (`vacancy_id`, `company_id`, `semester_id`, `vacancy_status_id`, `title`, `role`, `elaun`, `description`, `created_at`) VALUES
(5, 5, 2, 1, 'AI Internship', 'Machine Learning Intern', 1200.00, 'Assist in developing and testing ML models.', '2025-06-20 14:00:00'),
(6, 6, 2, 1, 'Cloud Support Intern', 'Cloud Support Engineer Intern', 1000.00, 'Provide technical support and deployment for AWS solutions.', '2025-06-21 09:30:00'),
(7, 7, 2, 1, 'UI/UX Designer Intern', 'UI/UX Designer', 850.00, 'Assist with app and web UI/UX projects.', '2025-06-22 10:00:00'),
(8, 7, 2, 1, 'Software Engineer Intern', 'Frontend Developer', 950.00, 'Work with React and TypeScript.', '2025-06-22 10:30:00'),
(9, 8, 2, 1, 'Chemical Engineer Intern', 'Chemical Engineer', 1000.00, 'Support refinery team in operations.', '2025-06-23 11:15:00'),
(10, 8, 2, 1, 'Mechanical Engineer Intern', 'Mechanical Engineer', 1050.00, 'Design and simulate plant equipment.', '2025-06-23 12:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `vacancy_status`
--

CREATE TABLE `vacancy_status` (
  `vacancy_status_id` int NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vacancy_status`
--

INSERT INTO `vacancy_status` (`vacancy_status_id`, `status_name`) VALUES
(1, 'Open'),
(2, 'Closed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `applicant`
--
ALTER TABLE `applicant`
  ADD PRIMARY KEY (`applicant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vacancy_id` (`vacancy_id`),
  ADD KEY `application_status_id` (`application_status_id`),
  ADD KEY `offer_status_id` (`offer_status_id`);

--
-- Indexes for table `application_status`
--
ALTER TABLE `application_status`
  ADD PRIMARY KEY (`application_status_id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`level_id`);

--
-- Indexes for table `offer_status`
--
ALTER TABLE `offer_status`
  ADD PRIMARY KEY (`offer_status_id`);

--
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`semester_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `level_id` (`level_id`);

--
-- Indexes for table `vacancy`
--
ALTER TABLE `vacancy`
  ADD PRIMARY KEY (`vacancy_id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `vacancy_status_id` (`vacancy_status_id`),
  ADD KEY `semester_id` (`semester_id`);

--
-- Indexes for table `vacancy_status`
--
ALTER TABLE `vacancy_status`
  ADD PRIMARY KEY (`vacancy_status_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `announcement_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `applicant`
--
ALTER TABLE `applicant`
  MODIFY `applicant_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `company_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `semester_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `vacancy`
--
ALTER TABLE `vacancy`
  MODIFY `vacancy_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcement`
--
ALTER TABLE `announcement`
  ADD CONSTRAINT `company_fk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `applicant`
--
ALTER TABLE `applicant`
  ADD CONSTRAINT `applicant_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  ADD CONSTRAINT `applicant_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancy` (`vacancy_id`),
  ADD CONSTRAINT `applicant_ibfk_3` FOREIGN KEY (`application_status_id`) REFERENCES `application_status` (`application_status_id`),
  ADD CONSTRAINT `applicant_ibfk_4` FOREIGN KEY (`offer_status_id`) REFERENCES `offer_status` (`offer_status_id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `level` (`level_id`);

--
-- Constraints for table `vacancy`
--
ALTER TABLE `vacancy`
  ADD CONSTRAINT `vacancy_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  ADD CONSTRAINT `vacancy_ibfk_2` FOREIGN KEY (`vacancy_status_id`) REFERENCES `vacancy_status` (`vacancy_status_id`),
  ADD CONSTRAINT `vacancy_ibfk_semester` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
