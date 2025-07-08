-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 08, 2025 at 07:50 PM
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
-- Database: `intern_portal`
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
  `created_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`announcement_id`, `announcement_title`, `announcement_content`, `company_id`, `created_at`) VALUES
(1, 'Google Malaysia Information Session', 'Google Malaysia will be hosting an information session for all interested interns on June 10, 2025, at 2:00 PM via Zoom.', 1, '2025-07-08 19:41:53');

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
(1, 'google', '$2y$10$yea/VqWbIr6..z65w2czXe3Nth3DEnAvlABbt9dzz5qS4Xt1Zrjwu', 'Google Malaysia', '03-22616400', 'hr@google.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'Google Malaysia is a leading technology company offering various services and products.', 'https://google.com.my', 1, NULL, NULL),
(2, 'microsoft', 'microsoft123', 'Microsoft Malaysia', '03-27778888', 'hr@microsoft.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'Microsoft Malaysia provides cutting-edge software solutions and cloud services.', 'https://microsoft.com.my', 1, NULL, NULL),
(3, 'amazon', 'amazon123', 'Amazon Web Services Malaysia', '03-48193182', 'hr@aws.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'AWS Malaysia provides cloud computing services and solutions.', 'https://aws.amazon.com', 1, NULL, NULL),
(4, 'shopee', 'shopee123', 'Shopee Malaysia', '03-65432109', 'hr@shopee.com.my', 'Petaling Jaya', 'Selangor', 'Shopee Malaysia is a leading e-commerce platform in Southeast Asia.', 'https://shopee.com.my', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gift`
--

CREATE TABLE `gift` (
  `gift_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `gift`
--

INSERT INTO `gift` (`gift_id`, `name`, `image`, `quantity`, `created_at`) VALUES
(1, 'Shoyuemi', 'shoyuemi.jpg', 7, '2025-07-08 14:59:54'),
(2, 'Notebook', 'notebook.jpg', 2, '2025-07-08 14:59:54'),
(3, 'Corntoz', 'corntoz.jpg', 7, '2025-07-08 14:59:54'),
(4, 'Super Ring', 'superring.jpg', 8, '2025-07-08 14:59:54'),
(5, 'Correction Tape', 'correctiontape.jpg', 4, '2025-07-08 14:59:54'),
(6, 'Cute Lego', 'lego.png', 2, '2025-07-08 14:59:54'),
(7, 'Geometry Ruler', 'geometry.jpg', 2, '2025-07-08 14:59:54');

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
(1, 'ACADEMIC SESSION I 2024/2025', '2024-10-07', '2025-02-23', 0, '2025-07-09 03:19:02'),
(2, 'ACADEMIC SESSION II 2024/2025', '2025-03-24', '2025-08-10', 1, '2025-07-09 03:19:02'),
(3, 'ACADEMIC SESSION I 2025/2026', '2025-10-06', '2026-02-22', 0, '2025-07-09 03:19:02'),
(4, 'ACADEMIC SESSION II 2025/2026', '2026-03-30', '2026-08-09', 0, '2025-07-09 03:19:02');

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
(1, 'Admin', 'admin', '$2y$10$ouAw/VI5PWIJxEFBGK9O3.aOjcgHOcZ9jv22Ym0yaeWZ76eQ044Zy', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Akmal Hazriq Bin Haslan', '2023881126@student.uitm.edu.my', '$2y$10$XICFvGpyO29bdkdJqPMPYecfu2BE.YsoqZgfKMg8aOnTQKh/uxh8u', 2, 'Puncak Alam', 'Selangor', '0162536411', 'UiTM Kedah', 'Diploma in Computer Science', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_gift`
--

CREATE TABLE `user_gift` (
  `user_gift_id` int NOT NULL,
  `user_id` int NOT NULL,
  `gift_id` int NOT NULL,
  `claimed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seen` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_gift`
--

INSERT INTO `user_gift` (`user_gift_id`, `user_id`, `gift_id`, `claimed_at`, `seen`) VALUES
(2, 1, 3, '2025-07-08 23:08:02', 1),
(3, 2, 2, '2025-07-08 23:09:29', 1);

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
(1, 1, 2, 1, 'Software Engineering Intern', 'Backend Developer', 1500.00, 'As a Software Engineering Intern at Google Malaysia, you will work on real-world projects and gain hands-on experience in backend development using cutting-edge technologies.', '2025-07-09 03:22:03'),
(2, 1, 2, 1, 'Data Science Intern', 'Data Analyst', 1450.00, 'Join Google Malaysia as a Data Science Intern to analyze complex datasets and derive meaningful insights to support business decisions.', '2025-07-09 03:22:03'),
(3, 2, 2, 1, 'Cloud Engineering Intern', 'Cloud Architect', 1500.00, 'Join Microsoft as a Cloud Engineering Intern to work with Azure Cloud Platform.', '2025-07-09 03:23:50'),
(4, 3, 2, 1, 'DevOps Intern', 'CI/CD Engineer', 1300.00, 'AWS is seeking DevOps Interns to help with our continuous integration and deployment pipelines.', '2025-07-09 03:23:50');

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
-- Indexes for table `gift`
--
ALTER TABLE `gift`
  ADD PRIMARY KEY (`gift_id`);

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
-- Indexes for table `user_gift`
--
ALTER TABLE `user_gift`
  ADD PRIMARY KEY (`user_gift_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `gift_id` (`gift_id`);

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
-- AUTO_INCREMENT for table `gift`
--
ALTER TABLE `gift`
  MODIFY `gift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `semester_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user_gift`
--
ALTER TABLE `user_gift`
  MODIFY `user_gift_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `user_gift`
--
ALTER TABLE `user_gift`
  ADD CONSTRAINT `user_gift_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_gift_ibfk_2` FOREIGN KEY (`gift_id`) REFERENCES `gift` (`gift_id`) ON DELETE CASCADE;

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
