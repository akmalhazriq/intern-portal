-- Add semester_id field to vacancy table
ALTER TABLE `vacancy` ADD `semester_id` INT NULL AFTER `vacancy_status_id`;

-- Add foreign key constraint
ALTER TABLE `vacancy` ADD CONSTRAINT `vacancy_semester_fk` FOREIGN KEY (`semester_id`) REFERENCES `semester`(`semester_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Create semester table if it doesn't exist
CREATE TABLE IF NOT EXISTS `semester` (
  `semester_id` int NOT NULL AUTO_INCREMENT,
  `semester_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insert default semesters if none exist
INSERT IGNORE INTO `semester` (`semester_name`, `start_date`, `end_date`, `is_active`) VALUES
('Semester 1 2024/2025', '2024-09-01', '2025-01-31', 0),
('Semester 2 2024/2025', '2025-02-01', '2025-06-30', 0),
('Semester 1 2025/2026', '2025-09-01', '2026-01-31', 1),
('Semester 2 2025/2026', '2026-02-01', '2026-06-30', 0);
