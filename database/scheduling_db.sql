-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2024 at 03:40 AM
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
  `program_id` int(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `building`
--

INSERT INTO `building` (`id`, `building`, `program_id`, `created_at`, `updated_at`) VALUES
(2, 'FI Building', 5, '2024-04-21 11:55:31', '2024-07-18 04:54:50'),
(3, 'IT Building', 2, '2024-07-18 04:54:34', '2024-07-18 04:54:34');

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
  `hours` varchar(255) NOT NULL,
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
(100, 'IT 113', 'Introduction to Computing', 2, '2024', '1st Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(101, 'IT 134', 'Computer Programming 1', 2, '2024', '1st Year', '1st Semester', '6:00', 4, 3, 3, 1, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(102, 'GEN. ED. 001', 'Purposive Communication', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(103, 'GEN. ED. 002', 'Understanding the Self', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(104, 'GEN. ED. 004', 'Mathematics in the Modern World', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(105, 'FIL 001', 'Akademiko sa Wikang Filipino', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(106, 'DRR 113', 'Disaster Risk Reduction and Education in Emergencies', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(107, 'MATH ENHANCE 1', 'College Algebra & Trigonometry', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(108, 'PATHFIT 112', 'Movement Competency Training', 2, '2024', '1st Year', '1st Semester', '2:00', 2, 2, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(109, 'NSTP 113', 'CWTS, LTS, MTS(Naval or Air Force)', 2, '2024', '1st Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:20:01', '2024-08-09 01:20:01'),
(110, 'IT 123', 'Introduction to Human Computer Interaction', 2, '2024', '1st Year', '2nd Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(111, 'IT 143', 'Discrete Mathematics', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(112, 'IT 163', 'Computer Programming 2', 2, '2024', '1st Year', '2nd Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(113, 'GEN. ED. 003', 'Readings in Philippine History', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(114, 'GEN. ED. 006', 'Ethics', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(115, 'GEN. ED. 007', 'The Contemporary World', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(116, 'FIL 002', 'Pagbasa at Pagsulat sa Iba\'t-Ibang Disiplina', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(117, 'PATHFIT 112', 'Fitness Training', 2, '2024', '1st Year', '2nd Semester', '2:00', 2, 2, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(118, 'NSTP 123', 'CWTS, LTS, MTS(Naval or Air Force)', 2, '2024', '1st Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(119, 'IT 213', 'Data Structures and Algorithm', 2, '2024', '2nd Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(120, 'IT 233', 'Object Oriented Programming', 2, '2024', '2nd Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(121, 'IT 253', 'Platform Technologies', 2, '2024', '2nd Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(122, 'IT 273', 'Web Systems and Technologies 1', 2, '2024', '2nd Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(123, 'IT 293', 'Statistics and Probability', 2, '2024', '2nd Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(124, 'CCNA213', 'Introduction to Network', 2, '2024', '2nd Year', '1st Semester', '5:00', 3, 2, 3, 1, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(125, 'RIZAL 001', 'Rizal\'s Life and Works', 2, '2024', '2nd Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(126, 'PATHFIT 212', 'Dance, Sport, and etc.', 2, '2024', '2nd Year', '1st Semester', '2:00', 2, 2, 0, 0, '2024-08-09 01:37:32', '2024-08-09 01:37:32'),
(127, 'IT 223', 'Information Management', 2, '2024', '2nd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(128, 'IT 243', 'Quantitative Methods ', 2, '2024', '2nd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(129, 'IT 263', 'Integrative Programming and Technology 1', 2, '2024', '2nd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(130, 'CCNA 223', 'Routing and Switching Essentials', 2, '2024', '2nd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(131, 'GEN. ED. 005', 'Art Appreciation', 2, '2024', '2nd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(132, 'GEN. ED. 008', 'Science, Technology and Society', 2, '2024', '2nd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(133, 'LIT 001', 'Panitikang Filipino', 2, '2024', '2nd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(134, 'PATHFIT', 'Dance, Sports, Group Exercise, Outdoor and Adventure Activities', 2, '2024', '2nd Year', '2nd Semester', '2:00', 2, 2, 0, 0, '2024-08-09 02:05:34', '2024-08-09 02:05:34'),
(135, 'IT 313', 'Advance Database System', 2, '2024', '3rd Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(136, 'IT 333', 'System Analysis Design', 2, '2024', '3rd Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(137, 'IT 353', 'Data Mining and Architecture 1', 2, '2024', '3rd Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(138, 'IT 353A', 'System Integration and Architecture', 2, '2024', '3rd Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(139, 'IT 373', 'Web System and Technology 2', 2, '2024', '3rd Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(140, 'IT 373A', 'Event-Driven Programming', 2, '2024', '3rd Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(141, 'IT 393', 'Social and Professional Issues ', 2, '2024', '3rd Year', '1st Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(142, 'CCNA  313', 'Scaling Network', 2, '2024', '3rd Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:13:57', '2024-08-09 02:13:57'),
(143, 'IT 323', 'Software Engineering', 2, '2024', '3rd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(144, 'IT 343', 'Multimedia System', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(145, 'IT 343A', 'IT Electives', 2, '2024', '3rd Year', '2nd Semester', '3:00', 3, 3, 0, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(146, 'IT 363', 'Information Assurance and Security 1', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(147, 'IT 363A', 'Application Development and Emerging Technologies', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(148, 'CCNA 323', 'Connecting Networks', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(149, 'IT 383', 'Interactive Programming and Technologies 2', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(150, 'IT 383A', 'System Integration and Architecture 2', 2, '2024', '3rd Year', '2nd Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:21:20', '2024-08-09 02:21:20'),
(151, 'IT 303', 'Information Assurance and Security 2', 2, '2024', '3rd Year', 'Mid Year', '5:00', 3, 2, 3, 0, '2024-08-09 02:23:14', '2024-08-09 02:23:14'),
(152, 'IT 303A', 'Capstone Project and Research 1', 2, '2024', '3rd Year', 'Mid Year', '4:00', 3, 2, 3, 0, '2024-08-09 02:31:22', '2024-08-09 02:23:14'),
(153, 'IT 413', 'System Administration and Maintenance', 2, '2024', '4th Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:25:12', '2024-08-09 02:25:12'),
(154, 'IT 433', 'Capstone 2', 2, '2024', '4th Year', '1st Semester', '5:00', 3, 2, 3, 0, '2024-08-09 02:25:12', '2024-08-09 02:25:12'),
(155, 'IT 429', 'Practicum (min. 486 hrs)', 2, '2024', '4th Year', '2nd Semester', '', 0, 0, 0, 0, '2024-08-09 02:27:03', '2024-08-09 02:27:03');

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
(36, 100, 14, '2024-08-09 03:15:25', '2024-08-09 03:15:25'),
(37, 101, 14, '2024-08-09 03:15:27', '2024-08-09 03:15:27'),
(38, 102, 14, '2024-08-09 03:15:33', '2024-08-09 03:15:33'),
(39, 103, 14, '2024-08-09 03:15:35', '2024-08-09 03:15:35'),
(40, 104, 14, '2024-08-09 03:15:37', '2024-08-09 03:15:37'),
(41, 106, 14, '2024-08-09 03:15:39', '2024-08-09 03:15:39'),
(42, 107, 14, '2024-08-09 03:15:41', '2024-08-09 03:15:41'),
(43, 105, 14, '2024-08-09 03:15:45', '2024-08-09 03:15:45'),
(44, 108, 14, '2024-08-09 03:15:47', '2024-08-09 03:15:47'),
(45, 109, 14, '2024-08-09 03:15:49', '2024-08-09 03:15:49'),
(46, 100, 12, '2024-08-09 06:42:40', '2024-08-09 06:42:40'),
(47, 101, 12, '2024-08-09 06:42:42', '2024-08-09 06:42:42'),
(48, 102, 12, '2024-08-09 06:42:44', '2024-08-09 06:42:44'),
(49, 103, 12, '2024-08-09 06:42:46', '2024-08-09 06:42:46'),
(50, 104, 12, '2024-08-09 06:42:48', '2024-08-09 06:42:48'),
(51, 107, 12, '2024-08-09 06:42:50', '2024-08-09 06:42:50'),
(52, 108, 12, '2024-08-09 06:42:52', '2024-08-09 06:42:52'),
(53, 109, 12, '2024-08-09 06:42:54', '2024-08-09 06:42:54'),
(54, 105, 12, '2024-08-09 06:42:58', '2024-08-09 06:42:58'),
(55, 106, 12, '2024-08-09 06:43:00', '2024-08-09 06:43:00'),
(56, 119, 19, '2024-08-11 10:12:55', '2024-08-11 10:12:55'),
(57, 120, 19, '2024-08-11 10:12:57', '2024-08-11 10:12:57'),
(58, 121, 19, '2024-08-11 10:12:59', '2024-08-11 10:12:59'),
(59, 122, 19, '2024-08-11 10:13:01', '2024-08-11 10:13:01'),
(60, 123, 19, '2024-08-11 10:13:03', '2024-08-11 10:13:03'),
(61, 124, 19, '2024-08-11 10:13:05', '2024-08-11 10:13:05'),
(62, 125, 19, '2024-08-11 10:13:07', '2024-08-11 10:13:07'),
(63, 126, 19, '2024-08-11 10:13:09', '2024-08-11 10:13:09');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(30) NOT NULL,
  `id_number` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mname` varchar(255) DEFAULT NULL,
  `lname` varchar(255) NOT NULL,
  `extname` varchar(255) DEFAULT NULL,
  `program_id` int(11) NOT NULL,
  `designation` int(20) NOT NULL,
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

INSERT INTO `faculty` (`id`, `id_number`, `fname`, `mname`, `lname`, `extname`, `program_id`, `designation`, `contact`, `gender`, `street`, `barangay`, `municipality`, `province`, `email`, `created_at`, `updated_at`) VALUES
(37, '12345', 'Arlene', '', 'Cebu', '', 2, 10, '+639123456789', 'Female', '', 'Test', 'Catbalogan', 'Eastern Samar', 'test@gmail.com', '2024-08-01 13:19:11', '2024-08-11 08:02:36'),
(38, '654321', 'Anthony', '', 'Cotoner', '', 2, 10, '9987654321', 'Male', '', 'testing', 'Tunga', 'Leyte', 'anthonytest@gmail.com', '2024-08-11 10:07:52', '2024-08-11 10:07:52'),
(39, '988765', 'Ryan', '', 'Aguilos', '', 2, 10, '9988325475', 'Male', '', 'Ponong', 'Carigara', 'Leyte', 'ryantest@gmail.com', '2024-08-11 10:09:36', '2024-08-11 10:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE `program` (
  `id` int(30) NOT NULL,
  `program_name` varchar(250) NOT NULL,
  `program_code` varchar(100) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`id`, `program_name`, `program_code`, `department`, `created_at`, `updated_at`) VALUES
(2, 'Bachelor of Science in Information Technology', 'BSIT', 'Information Technology Department', '2024-04-21 11:53:59', '2024-05-22 02:15:51'),
(3, 'Bachelor of Science in Entrepreneurship', 'BSEntrep', 'Entrep Departmment', '2024-04-21 11:53:59', '2024-05-22 02:19:10'),
(5, 'Bachelor of Science in Fisheries', 'BSFi', 'Fishery Department', '2024-04-21 11:53:59', '2024-05-22 02:20:36'),
(8, 'Bachelor of Secondary Education', 'BSEd', 'Education Department', '2024-07-18 04:12:17', '2024-07-18 04:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(30) NOT NULL,
  `room` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT 1,
  `is_lab` int(11) NOT NULL COMMENT '1 - Yes\r\n0 - No',
  `building_id` int(30) NOT NULL,
  `program_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room`, `description`, `is_available`, `is_lab`, `building_id`, `program_id`, `created_at`, `updated_at`) VALUES
(24, 'Speechlab', '', 1, 1, 3, 2, '2024-07-25 04:42:45', '2024-07-25 04:42:45'),
(25, 'Room-197', '', 1, 0, 2, 5, '2024-07-25 05:03:24', '2024-07-25 05:03:24'),
(26, 'Room-200', '', 1, 0, 2, 5, '2024-07-25 08:52:10', '2024-07-25 08:52:10'),
(28, 'Complab', '', 1, 1, 3, 2, '2024-08-08 04:14:56', '2024-08-09 13:26:06');

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

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `faculty_id`, `course_offering_info_id`, `room_id`, `is_loaded`, `is_active`, `day`, `time_start`, `time_end`, `created_at`, `updated_at`) VALUES
(69, NULL, NULL, 24, 0, 0, 'M', '15:00', '18:00', '2024-08-09 03:19:17', '2024-08-11 07:55:03'),
(71, 37, 36, 24, 0, 1, 'M', '14:00', '17:00', '2024-08-09 03:21:29', '2024-08-09 07:07:44'),
(74, NULL, 36, 24, 0, 1, 'M', '08:00', '10:00', '2024-08-12 02:06:20', '2024-08-12 02:06:20'),
(75, NULL, 37, 24, 0, 1, 'T', '10:00', '12:00', '2024-08-12 03:26:39', '2024-08-12 03:26:39');

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
(16, 3, '1st Year', 'A', 1, '2024-05-12 05:57:45', '2024-05-12 05:57:45'),
(19, 2, '2nd Year', 'A', 1, '2024-05-22 12:28:07', '2024-05-22 12:28:07'),
(20, 2, '3rd Year', 'A', 1, '2024-05-23 07:54:00', '2024-05-23 07:54:00'),
(21, 2, '4th Year', 'A', 1, '2024-07-06 03:08:38', '2024-07-06 03:08:38'),
(24, 5, '1st Year', 'A', 1, '2024-07-25 05:16:43', '2024-07-25 05:16:43'),
(25, 2, '4th Year', 'B', 1, '2024-08-08 04:13:58', '2024-08-08 04:13:58'),
(26, 2, '2nd Year', 'B', 1, '2024-08-09 13:32:21', '2024-08-09 13:32:21'),
(27, 2, '3rd Year', 'B', 1, '2024-08-09 13:32:42', '2024-08-09 13:32:42');

-- --------------------------------------------------------

--
-- Table structure for table `semester`
--

CREATE TABLE `semester` (
  `id` int(11) NOT NULL,
  `sem_name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semester`
--

INSERT INTO `semester` (`id`, `sem_name`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, '1st Semester', '2024-08-19', '2024-12-22', '2024-07-19 06:07:08', '2024-08-08 05:21:59'),
(2, '2nd Semester', '2024-01-22', '2024-06-29', '2024-07-19 06:09:51', '2024-07-19 06:09:51'),
(3, 'Mid Year', '2024-06-10', '2024-08-19', '2024-07-19 06:11:23', '2024-08-08 05:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `unit_loads`
--

CREATE TABLE `unit_loads` (
  `id` int(30) NOT NULL,
  `units` int(30) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `unit_loads`
--

INSERT INTO `unit_loads` (`id`, `units`, `designation`, `created_at`, `updated_at`) VALUES
(10, 24, 'Full Time', '2024-07-19 03:20:38', '2024-07-19 03:20:38'),
(11, 15, 'Part Time', '2024-07-19 03:21:00', '2024-07-19 03:21:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
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

INSERT INTO `users` (`id`, `username`, `password`, `type`, `program_id`, `created_at`, `updated_at`) VALUES
(10, 'admin', '$2y$10$FhH3MIIaa1sRtjtJeNKmOe2Jhm5c.sxCLHF6UPESNov6AFZ4Iniji', 0, 2, '2024-04-21 11:52:16', '2024-05-22 06:20:50'),
(44, 'superAdmin', '$2y$10$2PLOlKWcwG8IVK.eP72a5OBBkcQS4Lk.y0auxQv3NhQi3iD7ic/3y', 2, 2, '2024-07-17 13:15:59', '2024-07-17 13:15:59'),
(47, 'yanyan', '$2y$10$ahUv2MvXL6kG7bPIS0p7Ke.XzBNyX/jK7F2VovXtXrx5K2IajLQWy', 0, 5, '2024-07-18 11:55:30', '2024-07-18 11:55:30'),
(49, 'entrep', '$2y$10$hn5FGoXOZtOepPg5OB3TsOq1zBXv9Zvdliy/iV0jtbyW7tEXM54v.', 0, 3, '2024-08-01 12:25:06', '2024-08-01 14:36:49'),
(52, 'educ', '$2y$10$9q2HOQzg/9TphgA0VocB6.Pgp6yzPdXdVuUB5lHiZKHDr2qyLuWfK', 0, 8, '2024-08-01 14:37:38', '2024-08-01 14:37:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`program_id`);

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
  ADD UNIQUE KEY `id_number` (`id_number`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `designation` (`designation`);

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
  ADD KEY `building_id` (`building_id`),
  ADD KEY `program_id` (`program_id`);

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
-- Indexes for table `semester`
--
ALTER TABLE `semester`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unit_loads`
--
ALTER TABLE `unit_loads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `department_id` (`program_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `building`
--
ALTER TABLE `building`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `course_offering_info`
--
ALTER TABLE `course_offering_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `program`
--
ALTER TABLE `program`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `semester`
--
ALTER TABLE `semester`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `unit_loads`
--
ALTER TABLE `unit_loads`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `building`
--
ALTER TABLE `building`
  ADD CONSTRAINT `building_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `faculty_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `faculty_ibfk_3` FOREIGN KEY (`designation`) REFERENCES `unit_loads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
