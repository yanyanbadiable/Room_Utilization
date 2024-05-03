-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2024 at 08:16 PM
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
-- Database: `scheduling_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `building`
--

CREATE TABLE `building` (
  `id` int(30) NOT NULL,
  `building` varchar(255) NOT NULL,
  `department_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `building`
--

INSERT INTO `building` (`id`, `building`, `department_id`, `created_at`, `updated_at`) VALUES
(2, 'FI Building', 2, '2024-04-21 11:55:31', '2024-04-21 11:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(30) NOT NULL,
  `course_code` varchar(100) NOT NULL,
  `course_name` text NOT NULL,
  `program_id` int(30) NOT NULL,
  `year` varchar(100) NOT NULL,
  `level` varchar(255) NOT NULL,
  `period` varchar(255) NOT NULL,
  `hours` float NOT NULL,
  `units` float NOT NULL,
  `lec` float NOT NULL,
  `lab` float NOT NULL,
  `is_comlab` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `program_id`, `year`, `level`, `period`, `hours`, `units`, `lec`, `lab`, `is_comlab`, `created_at`, `updated_at`) VALUES
(62, '2', '2', 2, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 0, '2024-05-02 16:48:49', '2024-05-02 16:48:49'),
(63, '2', '2', 2, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 0, '2024-05-02 16:49:15', '2024-05-02 16:49:15'),
(64, '2', '2', 2, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 1, '2024-05-02 16:49:15', '2024-05-02 16:49:15'),
(65, '3', '3', 2, '2024', '1st Year', '1st Semester', 0, 3, 3, 3, 0, '2024-05-02 16:51:42', '2024-05-02 16:51:42'),
(66, '3', '3', 2, '2024', '1st Year', '1st Semester', 0, 3, 3, 3, 0, '2024-05-02 16:51:42', '2024-05-02 16:51:42'),
(67, '2', '2', 2, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 0, '2024-05-02 16:51:42', '2024-05-02 16:51:42'),
(68, 'test', 'test', 2, '2024', '4th Year', '1st Semester', 0, 5, 5, 5, 0, '2024-05-03 17:39:49', '2024-05-03 17:39:49');

-- --------------------------------------------------------

--
-- Table structure for table `course_offering_info`
--

CREATE TABLE `course_offering_info` (
  `id` int(30) NOT NULL,
  `courses_id` int(30) NOT NULL,
  `section_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_offering_info`
--

INSERT INTO `course_offering_info` (`id`, `courses_id`, `section_id`, `created_at`, `updated_at`) VALUES
(1, 62, 12, '2024-05-03 17:11:30', '2024-05-03 17:11:30'),
(2, 63, 12, '2024-05-03 17:38:49', '2024-05-03 17:38:49'),
(3, 65, 12, '2024-05-03 17:38:53', '2024-05-03 17:38:53');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(30) NOT NULL,
  `id_no` varchar(100) NOT NULL,
  `designation` enum('Full Faculty','Coordinator','Head') NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `id_no`, `designation`, `firstname`, `middlename`, `lastname`, `contact`, `gender`, `address`, `email`, `created_at`, `updated_at`) VALUES
(1, '06232014', 'Full Faculty', 'John', 'C', 'Smith', '+18456-5455-55', 'Male', 'Sample Address', 'jsmith@sample.com', '2024-04-21 11:53:28', '2024-04-21 11:53:28'),
(2, '37362629', 'Full Faculty', 'Claire', 'C', 'Blake', '+12345687923', 'Female', 'Sample Address', 'cblake@sample.com', '2024-04-21 11:53:28', '2024-04-21 11:53:28');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(30) NOT NULL,
  `program_name` varchar(250) NOT NULL,
  `program_code` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`id`, `program_name`, `program_code`, `created_at`, `updated_at`) VALUES
(2, 'Information Technology Department', 'IT Dept', '2024-04-21 11:53:59', '2024-04-21 11:53:59'),
(3, 'Entrepreneurship Department', 'ENTREP Dept', '2024-04-21 11:53:59', '2024-04-21 11:53:59'),
(5, 'Fishery Department', 'FI Dept', '2024-04-21 11:53:59', '2024-04-21 11:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(30) NOT NULL,
  `room` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `building_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room`, `description`, `is_available`, `building_id`, `created_at`, `updated_at`) VALUES
(16, 'Room-197', 'Near the SASO Offices', 1, 2, '2024-04-24 11:19:12', '2024-04-27 13:33:26'),
(18, 'test', 'test', 1, 2, '2024-04-27 13:38:12', '2024-04-27 13:38:12'),
(19, 'yanyan', 'test', 1, 2, '2024-04-27 14:56:43', '2024-04-27 14:56:43');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `course_offering_info_id` int(30) NOT NULL,
  `schedule_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1= Lecture, 2= Lab,3=others',
  `description` text NOT NULL,
  `room_id` int(30) NOT NULL,
  `is_repeating` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `repeating_data` text NOT NULL,
  `day` date NOT NULL,
  `time_from` time NOT NULL,
  `time_to` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(30) NOT NULL,
  `program_id` int(30) NOT NULL,
  `level` varchar(255) NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `program_id`, `level`, `section_name`, `is_active`, `created_at`, `updated_at`) VALUES
(12, 2, '2nd Year', 'A', 1, '2024-05-03 15:43:18', '2024-05-03 15:43:18'),
(13, 5, '4th Year', 'alpha', 1, '2024-05-03 17:40:05', '2024-05-03 17:40:05');

-- --------------------------------------------------------

--
-- Table structure for table `unit_loads`
--

CREATE TABLE `unit_loads` (
  `id` int(30) NOT NULL,
  `faculty_id` int(30) NOT NULL,
  `units` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 3 COMMENT '1=Admin,2=Staff, 3= subscriber',
  `department_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `department_id`, `created_at`, `updated_at`) VALUES
(10, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 1, 2, '2024-04-21 11:52:16', '2024-04-21 11:52:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_ibfk_1` (`program_id`);

--
-- Indexes for table `course_offering_info`
--
ALTER TABLE `course_offering_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_id` (`courses_id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `program`
--
ALTER TABLE `program`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `schedules_ibfk_2` (`course_offering_info_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `unit_loads`
--
ALTER TABLE `unit_loads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `building`
--
ALTER TABLE `building`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `course_offering_info`
--
ALTER TABLE `course_offering_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `unit_loads`
--
ALTER TABLE `unit_loads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `building`
--
ALTER TABLE `building`
  ADD CONSTRAINT `building_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `program` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course_offering_info`
--
ALTER TABLE `course_offering_info`
  ADD CONSTRAINT `course_offering_info_ibfk_1` FOREIGN KEY (`courses_id`) REFERENCES `courses` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `course_offering_info_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`course_offering_info_id`) REFERENCES `course_offering_info` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `unit_loads`
--
ALTER TABLE `unit_loads`
  ADD CONSTRAINT `unit_loads_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
