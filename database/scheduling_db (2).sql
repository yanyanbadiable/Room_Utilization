-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2024 at 05:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `course_name` varchar(255) NOT NULL,
  `program_id` int(30) NOT NULL,
  `year` varchar(100) NOT NULL,
  `level` varchar(255) NOT NULL,
  `period` varchar(255) NOT NULL,
  `hours` float NOT NULL,
  `units` float DEFAULT NULL,
  `lec` float DEFAULT NULL,
  `lab` float DEFAULT NULL,
  `is_comlab` float NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `program_id`, `year`, `level`, `period`, `hours`, `units`, `lec`, `lab`, `is_comlab`, `created_at`, `updated_at`) VALUES
(71, 'SE1', 'Software Engineering', 2, '2024', '1st Year', '1st Semester', 0, 3, 3, 3, 1, '2024-05-11 20:25:50', '2024-05-11 16:11:16'),
(75, 'CCNA', 'Packet Tracer', 3, '2024', '1st Year', '1st Semester', 0, 2, 3, 2, 0, '2024-05-11 19:24:22', '2024-05-11 19:24:22'),
(76, 'SA', 'System Architecture', 3, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 0, '2024-05-11 19:35:18', '2024-05-11 19:35:18'),
(77, 'IE1', 'IT Electives2', 2, '2024', '1st Year', '1st Semester', 0, 2, 2, 2, 0, '2024-05-11 20:25:04', '2024-05-11 19:41:07'),
(78, 'IA', 'Information Assurance', 3, '2024', '1st Year', '1st Semester', 0, 3, 2, 2, 0, '2024-05-13 00:51:14', '2024-05-11 19:43:26');

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
(15, 71, 14, '2024-05-12 03:14:16', '2024-05-12 03:14:16'),
(16, 75, 16, '2024-05-12 05:58:42', '2024-05-12 05:58:42'),
(17, 76, 16, '2024-05-12 05:58:52', '2024-05-12 05:58:52'),
(20, 71, 12, '2024-05-12 12:22:53', '2024-05-12 12:22:53'),
(21, 77, 14, '2024-05-13 07:52:47', '2024-05-13 07:52:47');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `contact` varchar(100) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `user_id`, `program_id`, `designation`, `contact`, `gender`, `street`, `barangay`, `municipality`, `province`, `email`, `created_at`, `updated_at`) VALUES
(8, 17, 2, 'Full Time', '+639272950588', 'Male', '', 'Baruguhay Norte ', 'Carigara', 'Leyte', 'yanyanbadiable@gmail.com', '2024-05-14 03:25:04', '2024-05-14 03:25:04'),
(9, 18, 2, 'Full Time', '+639272950589', 'Male', '', 'Ponong', 'Carigara', 'Leyte', 'jomarberdejo@gmail.com', '2024-05-14 03:27:13', '2024-05-14 09:02:02'),
(10, 19, 2, 'Part Time', '+639272950589', 'Male', '', 'Macopa', 'Jaro', 'Leyte', 'jersonaballa@gmail.com', '2024-05-14 09:06:03', '2024-05-14 09:06:03');

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
  `description` varchar(255) DEFAULT NULL,
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
  `faculty_id` int(30) DEFAULT NULL,
  `course_offering_info_id` int(30) DEFAULT NULL,
  `room_id` int(30) NOT NULL,
  `is_loaded` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `day` varchar(255) NOT NULL,
  `time_start` varchar(255) NOT NULL,
  `time_end` varchar(255) NOT NULL,
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
(12, 2, '1st Year', 'B', 1, '2024-05-03 15:43:18', '2024-05-11 01:12:21'),
(13, 5, '4th Year', 'alpha', 1, '2024-05-03 17:40:05', '2024-05-03 17:40:05'),
(14, 2, '1st Year', 'A', 1, '2024-05-11 01:12:39', '2024-05-11 01:12:39'),
(15, 2, '1st Year', 'C', 1, '2024-05-11 01:12:51', '2024-05-11 01:12:51'),
(16, 3, '1st Year', 'A', 1, '2024-05-12 05:57:45', '2024-05-12 05:57:45');

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
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `extname` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '0=Admin, 1=Instructor, 2= Super Admin\r\n',
  `program_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `mname`, `lname`, `extname`, `username`, `password`, `type`, `program_id`, `created_at`, `updated_at`) VALUES
(10, 'Admin', NULL, 'Admin', NULL, 'admin', '$2y$10$yQkIoB0cHw3gpN9vigfbPelz2y4qUDN9VQGvX52Kw7IX9uxb.7um2', 0, 2, '2024-04-21 11:52:16', '2024-05-14 23:02:11'),
(17, 'Brand Ian', 'Bacunawa', 'Badiable', '', '2021-1485', '$2y$10$ltMm2BmRLXj90409RyFZV.jv3CH7pBZfBpmDeKD.h4YANbUPfExs6', 1, 2, '2024-05-14 03:25:02', '2024-05-14 03:25:02'),
(18, 'Jomar', 'Macalinao', 'Berdejo', '', '2021-1486', '$2y$10$wA4uwK/HSqujYvWLx3btaOeO0vIRokLkAHVFb7O/oIfR8WU02IUe.', 1, 2, '2024-05-14 03:27:15', '2024-05-14 03:27:15'),
(19, 'Jerson', 'Aure', 'Aballa', '', '2021-25019', '$2y$10$9Lx7JuLYny1rXKj215QFjuFLWgv.6J0unULR0.xWGzEOnTA/joN0O', 1, 2, '2024-05-14 09:06:01', '2024-05-14 09:06:01');

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `program_id` (`program_id`);

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
  ADD KEY `department_id` (`program_id`);

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `course_offering_info`
--
ALTER TABLE `course_offering_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `unit_loads`
--
ALTER TABLE `unit_loads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faculty_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
