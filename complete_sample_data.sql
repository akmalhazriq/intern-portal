-- Intern Portal Database Setup
-- Complete database structure with sample data
-- Generated on July 8, 2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Drop existing tables if they exist (in reverse order of dependencies)
DROP TABLE IF EXISTS user_gift;
DROP TABLE IF EXISTS gift;
DROP TABLE IF EXISTS applicant;
DROP TABLE IF EXISTS vacancy;
DROP TABLE IF EXISTS announcement;
DROP TABLE IF EXISTS company;
DROP TABLE IF EXISTS semester;
DROP TABLE IF EXISTS application_status;
DROP TABLE IF EXISTS offer_status;
DROP TABLE IF EXISTS vacancy_status;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS level;

-- --------------------------------------------------------
-- Table structure for table `level`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `level` (
  `level_id` int(11) NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL,
  PRIMARY KEY (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `user`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `level_id` int(11) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `campus` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `level_id` (`level_id`),
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`level_id`) REFERENCES `level` (`level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `vacancy_status`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vacancy_status` (
  `vacancy_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `vacancy_status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`vacancy_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `application_status`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `application_status` (
  `application_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `application_status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`application_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `offer_status`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `offer_status` (
  `offer_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`offer_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `semester`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `semester` (
  `semester_id` int(11) NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `company`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `description` text,
  `website` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `banner` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `vacancy`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vacancy` (
  `vacancy_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL,
  `elaun` varchar(50) DEFAULT NULL,
  `description` text NOT NULL,
  `vacancy_status_id` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`vacancy_id`),
  KEY `company_id` (`company_id`),
  KEY `semester_id` (`semester_id`),
  KEY `vacancy_status_id` (`vacancy_status_id`),
  CONSTRAINT `vacancy_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`),
  CONSTRAINT `vacancy_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semester` (`semester_id`),
  CONSTRAINT `vacancy_ibfk_3` FOREIGN KEY (`vacancy_status_id`) REFERENCES `vacancy_status` (`vacancy_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `applicant`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `applicant` (
  `user_id` int(11) NOT NULL,
  `vacancy_id` int(11) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `application_status_id` int(11) NOT NULL DEFAULT '1',
  `offer_status_id` int(11) NOT NULL DEFAULT '1',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`vacancy_id`),
  KEY `vacancy_id` (`vacancy_id`),
  KEY `application_status_id` (`application_status_id`),
  KEY `offer_status_id` (`offer_status_id`),
  CONSTRAINT `applicant_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `applicant_ibfk_2` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancy` (`vacancy_id`),
  CONSTRAINT `applicant_ibfk_3` FOREIGN KEY (`application_status_id`) REFERENCES `application_status` (`application_status_id`),
  CONSTRAINT `applicant_ibfk_4` FOREIGN KEY (`offer_status_id`) REFERENCES `offer_status` (`offer_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `announcement`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `announcement` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `announcement_title` varchar(100) NOT NULL,
  `announcement_content` text,
  `company_id` int(11) DEFAULT NULL,
  `created_at` date NOT NULL,
  PRIMARY KEY (`announcement_id`),
  KEY `company_id` (`company_id`),
  CONSTRAINT `announcement_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `company` (`company_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure and data for table `gift`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `gift` (
  `gift_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample gift data
INSERT INTO `gift` (`gift_id`, `name`, `image`, `quantity`) VALUES
(1, 'Shoyuemi', 'shoyuemi.jpg', 7),
(2, 'Notebook', 'notebook.jpg', 2),
(3, 'Corntoz', 'corntoz.jpg', 7),
(4, 'Super Ring', 'superring.jpg', 8),
(5, 'Correction Tape', 'correctiontape.jpg', 15),
(6, 'Cute Lego', 'lego.png', 2),
(7, 'Geometry Ruler', 'geometry.jpg', 2);

-- --------------------------------------------------------
-- Table structure and data for table `user_gift`
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `user_gift` (
  `user_gift_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `gift_id` int(11) NOT NULL,
  `claimed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_gift_id`),
  KEY `user_id` (`user_id`),
  KEY `gift_id` (`gift_id`),
  CONSTRAINT `user_gift_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `user_gift_ibfk_2` FOREIGN KEY (`gift_id`) REFERENCES `gift` (`gift_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample user gift assignments
INSERT INTO `user_gift` (`user_gift_id`, `user_id`, `gift_id`, `claimed_at`, `seen`) VALUES
(1, 2, 1, '2025-06-15 14:30:00', 1),
(2, 3, 3, '2025-06-20 10:15:00', 1),
(3, 4, 6, '2025-06-25 16:45:00', 0);

-- --------------------------------------------------------
-- Sample data for existing tables
-- --------------------------------------------------------

-- Sample user data
INSERT INTO `user` (`user_id`, `full_name`, `email`, `password`, `phone_number`, `profile_picture`, `level_id`, `area`, `state`, `campus`, `course`, `created_at`) VALUES
(1, 'Admin', 'admin', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', NULL, NULL, 1, NULL, NULL, NULL, NULL, NOW()),
(2, 'Akmal Hazriq Bin Haslan', '2023881126@student.uitm.edu.my', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', '0162536411', 'Akmal_Hazriq_Bin_Haslan.jpg', 2, 'Puncak Alam', 'Selangor', 'UiTM Kedah', 'Diploma in Computer Science', NOW()),

-- Sample company data
INSERT INTO `company` (`company_id`, `username`, `password`, `company_name`, `phone`, `email`, `area`, `state`, `description`, `website`, `verified`, `banner`, `logo`, `created_at`) VALUES
(1, 'google_my', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 'Google Malaysia', '03-12345678', 'hr@google.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'Google Malaysia is a leading technology company offering various services and products.', 'https://google.com.my', 1, 'Google_Malaysia_banner.jpg', 'Google_Malaysia_logo.png', '2025-02-01 09:00:00'),
(2, 'beta_corp', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 'Beta Corp', '03-23456789', 'hr@betacorp.com', 'Cyberjaya', 'Selangor', 'Beta Corp is an innovative software development company specializing in AI and machine learning solutions.', 'https://betacorp.com', 1, 'Beta_Corp_banner.jpg', 'Beta_Corp_logo.png', '2025-02-03 11:30:00'),
(3, 'microsoft', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 'Microsoft Malaysia', '03-27778888', 'hr@microsoft.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'Microsoft Malaysia provides cutting-edge software solutions and cloud services.', 'https://microsoft.com.my', 1, NULL, NULL, NOW()),
(4, 'amazon', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 'Amazon Web Services Malaysia', '03-48193182', 'hr@aws.com.my', 'Kuala Lumpur', 'Kuala Lumpur', 'AWS Malaysia provides cloud computing services and solutions.', 'https://aws.amazon.com', 1, NULL, NULL, NOW()),
(5, 'shopee', '$2y$10$DG6UZuthzNfsL7bXViq7m.XqAIxIusQi6/xk4qysAT58OtMqd251a', 'Shopee Malaysia', '03-65432109', 'hr@shopee.com.my', 'Petaling Jaya', 'Selangor', 'Shopee Malaysia is a leading e-commerce platform in Southeast Asia.', 'https://shopee.com.my', 1, NULL, NULL, NOW());

-- Sample vacancy data
INSERT INTO `vacancy` (`vacancy_id`, `company_id`, `semester_id`, `title`, `role`, `elaun`, `description`, `vacancy_status_id`, `created_at`) VALUES
(1, 1, 2, 'Software Engineering Intern', 'Backend Developer', 'RM 1,500', 'As a Software Engineering Intern at Google Malaysia, you will work on real-world projects and gain hands-on experience in backend development using cutting-edge technologies.', 1, '2025-06-01 08:00:00'),
(2, 1, 2, 'Data Science Intern', 'Data Analyst', 'RM 1,500', 'Join Google Malaysia as a Data Science Intern to analyze complex datasets and derive meaningful insights to support business decisions.', 1, '2025-06-01 08:15:00'),
(3, 2, 2, 'Machine Learning Intern', 'ML Engineer', 'RM 1,300', 'Join Beta Corp as a Machine Learning Intern to work on cutting-edge AI projects using TensorFlow and PyTorch.', 1, '2025-06-02 09:15:00'),
(4, 3, 2, 'Cloud Engineering Intern', 'Cloud Architect', 'RM 1,500', 'Join Microsoft as a Cloud Engineering Intern to work with Azure Cloud Platform.', 1, NOW()),
(5, 4, 2, 'DevOps Intern', 'CI/CD Engineer', 'RM 1,300', 'AWS is seeking DevOps Interns to help with our continuous integration and deployment pipelines.', 1, NOW());


-- Sample announcement data
INSERT INTO `announcement` (`announcement_id`, `announcement_title`, `announcement_content`, `company_id`, `created_at`) VALUES
(1, 'Welcome to Intern Portal', 'Welcome to our new Intern Portal! This platform will help students connect with companies for internship opportunities.', 0, '2025-05-15'),
(2, 'Semester 2 2025 Internships Now Available', 'Internship applications for Semester 2 2025 are now open. Please log in to view available positions.', 0, '2025-06-01'),
(3, 'Google Malaysia Information Session', 'Google Malaysia will be hosting an information session for all interested interns on June 10, 2025, at 2:00 PM via Zoom.', 1, '2025-06-05'),
(4, 'Beta Corp Hiring Process Update', 'Beta Corp has updated its hiring process for interns. Please check our vacancy details for more information.', 2, '2025-06-08'),
(5, 'New Gift Program Launch', 'We are excited to announce our new gift program for registered users! Log in to receive your special gift.', 0, CURDATE());

-- Insert reference data into level
INSERT INTO `level` (`level_id`, `level_name`) VALUES
(1, 'Admin'),
(2, 'Student'),
(3, 'Company');

-- Insert reference data into vacancy_status
INSERT INTO `vacancy_status` (`vacancy_status_id`, `vacancy_status_name`) VALUES
(1, 'Open'),
(2, 'Closed');

-- Insert reference data into application_status
INSERT INTO `application_status` (`application_status_id`, `application_status_name`) VALUES
(1, 'Pending'),
(2, 'Reviewed'),
(3, 'Interviewed'),
(4, 'Offered'),
(5, 'Rejected');

-- Insert reference data into offer_status
INSERT INTO `offer_status` (`offer_status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Accepted'),
(3, 'Declined');

-- Insert reference data into semester
INSERT INTO `semester` (`semester_id`, `semester_name`, `start_date`, `end_date`, `is_active`) VALUES
(1, 'ACADEMIC SESSION I 2024/2025', '2024-10-07', '2025-02-23', 0),
(2, 'ACADEMIC SESSION II 2024/2025', '2025-03-24', '2025-08-10', 1),
(3, 'ACADEMIC SESSION I 2025/2026', '2025-10-06', '2026-02-22', 0),
(4, 'ACADEMIC SESSION II 2025/2026', '2026-03-30', '2026-08-09', 0);
