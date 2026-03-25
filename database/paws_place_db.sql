-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 25, 2026 at 04:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paws_place_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_role` enum('Admin','Cashier','Barista') NOT NULL,
  `activity_type` enum('LOGIN','LOGOUT','MENU_CREATE','MENU_UPDATE','MENU_DELETE','MENU_RESTORE','INVENTORY_ADJUST','ORDER_STATUS_CHANGE') NOT NULL,
  `description` text NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`log_id`, `user_id`, `user_role`, `activity_type`, `description`, `metadata`, `created_at`) VALUES
(1, 1, 'Admin', 'LOGIN', 'Admin logged in', '{\"ip\": \"127.0.0.1\"}', '2026-02-11 07:14:09'),
(2, 2, 'Cashier', 'LOGIN', 'Cashier logged in', '{\"ip\": \"127.0.0.1\"}', '2026-02-11 07:14:09'),
(3, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-16 05:00:24'),
(4, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-16 05:00:34'),
(5, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-16 05:00:57'),
(6, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-16 05:01:07'),
(7, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-16 05:01:26'),
(8, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-16 07:40:37'),
(9, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-16 07:42:41'),
(10, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-16 07:48:35'),
(11, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-16 07:49:16'),
(12, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-18 05:18:50'),
(13, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-18 05:24:46'),
(14, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 05:25:14'),
(15, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 05:26:37'),
(16, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 05:28:01'),
(17, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 05:28:28'),
(20, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-18 05:29:01'),
(21, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-18 05:33:00'),
(22, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 06:42:17'),
(23, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 06:42:47'),
(24, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 06:47:33'),
(25, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 06:48:51'),
(26, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 06:49:02'),
(27, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 06:54:12'),
(28, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-18 07:05:52'),
(29, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-18 07:09:02'),
(30, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-18 07:09:12'),
(31, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-18 07:10:41'),
(32, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-19 02:16:01'),
(33, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-19 02:16:11'),
(34, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-19 02:21:04'),
(35, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-19 02:22:53'),
(36, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-20 00:47:16'),
(37, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-20 00:48:23'),
(38, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-20 00:50:15'),
(39, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-20 00:51:04'),
(40, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-20 00:56:12'),
(41, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-20 00:56:24'),
(42, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-20 01:14:14'),
(43, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-20 01:14:36'),
(44, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-20 01:14:51'),
(45, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-20 01:15:28'),
(46, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-20 01:18:54'),
(47, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-20 01:19:48'),
(48, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-20 02:51:43'),
(49, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-20 03:11:35'),
(50, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-20 03:11:42'),
(51, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-20 03:11:57'),
(52, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-20 03:13:41'),
(53, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-20 03:25:31'),
(54, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-20 05:00:32'),
(55, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-20 05:01:47'),
(56, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-24 06:52:59'),
(57, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-24 06:53:06'),
(58, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-24 11:55:17'),
(59, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-24 11:59:05'),
(60, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-24 11:59:12'),
(61, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-24 11:59:59'),
(62, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-24 12:01:28'),
(63, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-24 12:03:36'),
(64, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-24 16:17:34'),
(65, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-24 16:25:02'),
(66, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-24 21:05:15'),
(67, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-24 21:05:27'),
(68, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-24 21:05:34'),
(69, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-24 21:05:51'),
(70, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 01:25:17'),
(71, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 01:49:37'),
(72, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 01:52:56'),
(73, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 01:53:12'),
(74, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 01:53:24'),
(75, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 01:53:29'),
(76, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 03:49:40'),
(77, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 03:49:53'),
(78, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 04:08:25'),
(79, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 04:08:32'),
(80, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 05:50:14'),
(81, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 05:54:02'),
(82, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 05:57:41'),
(83, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 05:59:22'),
(84, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 06:11:11'),
(85, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 06:13:15'),
(86, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 06:16:28'),
(87, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 06:21:34'),
(88, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 06:23:53'),
(89, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-25 08:22:41'),
(90, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-25 08:22:49'),
(91, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-25 08:22:59'),
(92, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-25 08:23:40'),
(93, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-25 09:02:17'),
(94, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-25 09:03:06'),
(95, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-25 09:03:37'),
(96, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-25 09:03:55'),
(97, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-26 20:18:22'),
(98, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-26 20:46:35'),
(99, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-26 21:06:48'),
(100, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-26 21:22:18'),
(101, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-26 21:22:31'),
(102, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 01:29:31'),
(103, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 01:30:25'),
(104, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 02:54:22'),
(105, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:00:59'),
(106, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:10:37'),
(107, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 03:12:45'),
(108, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:21:31'),
(109, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 03:21:33'),
(110, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:21:40'),
(111, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-27 03:21:47'),
(112, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:21:55'),
(113, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 03:32:08'),
(114, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 03:32:45'),
(115, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:02:42'),
(116, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:03:06'),
(117, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:11:39'),
(118, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:14:48'),
(119, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:15:07'),
(120, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:15:29'),
(121, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:15:53'),
(122, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:16:18'),
(123, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:16:25'),
(124, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:17:00'),
(125, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:20:27'),
(126, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:20:53'),
(127, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:21:01'),
(128, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:27:46'),
(129, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:27:58'),
(130, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:28:37'),
(131, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:29:01'),
(132, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:32:20'),
(133, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:34:50'),
(134, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:41:14'),
(139, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:43:48'),
(140, 4, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:44:18'),
(141, 5, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:44:37'),
(142, 5, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:48:38'),
(143, 3, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:48:57'),
(144, 3, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:57:11'),
(145, 1, 'Admin', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:57:27'),
(146, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-27 06:58:26'),
(147, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 06:58:32'),
(148, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 07:05:23'),
(149, 3, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 07:05:38'),
(150, 3, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 07:05:58'),
(151, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 07:06:07'),
(152, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 07:20:52'),
(153, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 07:21:05'),
(154, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 07:25:47'),
(155, 3, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 07:25:55'),
(156, 3, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 07:27:24'),
(157, 3, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 07:27:52'),
(158, 3, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-27 08:18:43'),
(159, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 08:35:30'),
(160, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-27 09:03:51'),
(161, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-28 01:15:48'),
(162, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-28 03:22:36'),
(163, 5, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-02-28 03:27:01'),
(164, 5, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-02-28 03:30:45'),
(165, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-02 01:04:25'),
(166, 4, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-02 01:10:53'),
(167, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-02 01:11:07'),
(168, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-02 01:17:11'),
(169, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-02 02:13:46'),
(170, 4, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-02 02:30:50'),
(171, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-02 21:11:05'),
(172, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-02 21:21:03'),
(173, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-02 21:21:20'),
(174, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-02 21:56:10'),
(175, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-02 21:56:59'),
(176, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 00:34:33'),
(177, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 00:39:34'),
(178, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 00:39:58'),
(179, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 00:46:08'),
(180, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 01:06:44'),
(181, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 01:07:15'),
(182, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 01:43:00'),
(183, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 01:44:40'),
(184, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 02:59:37'),
(185, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 02:59:57'),
(186, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 03:03:11'),
(187, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-03 03:32:31'),
(188, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-03 03:33:03'),
(189, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-03 03:33:21'),
(190, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:26:18'),
(192, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:29:08'),
(193, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:29:49'),
(194, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:30:00'),
(195, 4, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-18 03:32:03'),
(196, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:32:15'),
(197, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-18 03:32:33'),
(198, 4, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:32:43'),
(199, 4, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-18 03:32:50'),
(200, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 03:36:16'),
(201, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 05:13:56'),
(202, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-18 05:17:26'),
(205, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-18 05:21:16'),
(206, 6, '', 'LOGIN', 'User logged in', NULL, '2026-03-18 05:28:01'),
(207, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-18 05:36:44'),
(208, 2, 'Cashier', 'LOGIN', 'User logged in', NULL, '2026-03-19 05:04:08'),
(209, 2, 'Cashier', 'LOGOUT', 'User logged out', NULL, '2026-03-19 05:16:14');

-- --------------------------------------------------------

--
-- Table structure for table `api_cache_categories`
--

CREATE TABLE `api_cache_categories` (
  `id` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `raw_name` varchar(100) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `last_synced` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_cache_categories`
--

INSERT INTO `api_cache_categories` (`id`, `name`, `raw_name`, `icon`, `location_id`, `last_synced`) VALUES
('Bread', 'Bread', 'Bread', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:41'),
('Bread', 'Bread', 'Bread', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:45'),
('Bread', 'Bread', 'Bread', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:47'),
('Candy', 'Candy', 'Candy', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:42'),
('Candy', 'Candy', 'Candy', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:45'),
('Candy', 'Candy', 'Candy', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:48'),
('Consignment', 'Consignment', 'Consignment', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:48'),
('Drinks', 'Drinks', 'Drinks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:42'),
('Drinks', 'Drinks', 'Drinks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:45'),
('Drinks', 'Drinks', 'Drinks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:48'),
('Food', 'Food', 'Food', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:43'),
('Food', 'Food', 'Food', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:45'),
('Food', 'Food', 'Food', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:49'),
('Fruits', 'Fruits', 'Fruits', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:43'),
('Fruits', 'Fruits', 'Fruits', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:46'),
('Fruits', 'Fruits', 'Fruits', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:49'),
('Ice Cream', 'Ice Cream', 'Ice Cream', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:43'),
('Ice Cream', 'Ice Cream', 'Ice Cream', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:46'),
('Snacks', 'Snacks', 'Snacks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:43'),
('Snacks', 'Snacks', 'Snacks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:46'),
('Snacks', 'Snacks', 'Snacks', '<i class=\"ph-duotone ph-fork-knife\"></i>', 13, '2026-03-19 14:57:49'),
('Supply', 'Supply', 'Supply', '<i class=\"ph-duotone ph-fork-knife\"></i>', 1, '2026-03-19 14:57:44'),
('Supply', 'Supply', 'Supply', '<i class=\"ph-duotone ph-fork-knife\"></i>', 2, '2026-03-19 14:57:47');

-- --------------------------------------------------------

--
-- Table structure for table `api_cache_items`
--

CREATE TABLE `api_cache_items` (
  `item_id` varchar(100) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category_name` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `last_synced` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_cache_items`
--

INSERT INTO `api_cache_items` (`item_id`, `name`, `category_name`, `price`, `location_id`, `last_synced`) VALUES
('17827', 'Pretzels', 'Snacks', 12.00, 1, '2026-03-19 14:57:44'),
('17827', 'Pretzels', 'Snacks', 12.00, 2, '2026-03-19 14:57:47'),
('17832', 'Quake- Overload', 'Snacks', 10.00, 1, '2026-03-19 14:57:44'),
('17832', 'Quake- Overload', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('17832', 'Quake- Overload', 'Snacks', 10.00, 13, '2026-03-19 14:57:51'),
('17835', 'Presto, Cream-o Small', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('17835', 'Presto, Cream-o Small', 'Snacks', 10.00, 13, '2026-03-19 14:57:51'),
('17843', ' Nips Small', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('17845', 'Max,xo,dynamite', 'Candy', 1.00, 1, '2026-03-19 14:57:42'),
('17845', 'Max,xo,dynamite', 'Snacks', 1.00, 1, '2026-03-19 14:57:44'),
('17868', 'Dynamite', 'Snacks', 10.00, 1, '2026-03-19 14:57:44'),
('17868', 'Dynamite', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('17869', 'Cheese Sticks', 'Snacks', 8.00, 1, '2026-03-19 14:57:44'),
('17869', 'Cheese Sticks', 'Snacks', 8.00, 2, '2026-03-19 14:57:47'),
('17871', 'Aroscaldo', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('17871', 'Aroscaldo', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('17882', 'Paper Cup', 'Supply', 3.00, 1, '2026-03-19 14:57:44'),
('17882', 'Paper Cup', 'Supply', 3.00, 2, '2026-03-19 14:57:47'),
('17885', 'Coke Swakto', 'Drinks', 18.00, 1, '2026-03-19 14:57:43'),
('17885', 'Coke Swakto', 'Drinks', 18.00, 2, '2026-03-19 14:57:45'),
('17885', 'Coke Swakto', 'Drinks', 18.00, 13, '2026-03-19 14:57:49'),
('17888', 'Banana Q, Camote Q ', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('17888', 'Banana Q, Camote Q ', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('17888', 'Banana Q, Camote Q ', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('17891', 'Great Taste', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('17891', 'Great Taste', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('17891', 'Great Taste', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('17899', 'Milo', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('17899', 'Milo', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('17899', 'Milo', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('17905', 'Toron (banana And Camote)', 'Snacks', 10.00, 1, '2026-03-19 14:57:44'),
('17905', 'Toron (banana And Camote)', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('17928', 'Cloud 9 Classic', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('17928', 'Cloud 9 Classic', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('17929', 'Cloud 9 Overload', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('17931', 'Chiz Culrz Big', 'Snacks', 30.00, 1, '2026-03-19 14:57:44'),
('17931', 'Chiz Culrz Big', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('17943', 'Cup Noodles', 'Snacks', 30.00, 1, '2026-03-19 14:57:44'),
('17943', 'Cup Noodles', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('17943', 'Cup Noodles', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('17947', ' Nutriboost Small', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('17952', 'Combo Banana', 'Snacks', 10.00, 1, '2026-03-19 14:57:44'),
('17952', 'Combo Banana', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('17955', 'Sari Sari Veges Guisado', 'Food', 30.00, 1, '2026-03-19 14:57:43'),
('17955', 'Sari Sari Veges Guisado', 'Food', 30.00, 2, '2026-03-19 14:57:46'),
('17955', 'Sari Sari Veges Guisado', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('17963', 'Fish Flat', 'Snacks', 5.00, 1, '2026-03-19 14:57:44'),
('17963', 'Fish Flat', 'Snacks', 5.00, 2, '2026-03-19 14:57:47'),
('17963', 'Fish Flat', 'Snacks', 5.00, 13, '2026-03-19 14:57:50'),
('17970', 'Garlic Rice Half', 'Food', 10.00, 1, '2026-03-19 14:57:43'),
('17970', 'Garlic Rice Half', 'Food', 10.00, 2, '2026-03-19 14:57:46'),
('17970', 'Garlic Rice Half', 'Food', 10.00, 13, '2026-03-19 14:57:49'),
('17995', 'Chuckie 250 Ml', 'Drinks', 35.00, 1, '2026-03-19 14:57:43'),
('17995', 'Chuckie 250 Ml', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('17995', 'Chuckie 250 Ml', 'Drinks', 35.00, 13, '2026-03-19 14:57:49'),
('17998', 'Milk Sterilized Fortified', 'Drinks', 30.00, 1, '2026-03-19 14:57:43'),
('17998', 'Milk Sterilized Fortified', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('17999', 'Champorado', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('17999', 'Champorado', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('18009', 'Soft Serve Ice Cream @ 20.00', 'Ice Cream', 25.00, 2, '2026-03-19 14:57:46'),
('18010', 'Ice Cream @ 15.00', 'Ice Cream', 15.00, 2, '2026-03-19 14:57:46'),
('18018', 'Souper Meal Noodles', 'Snacks', 40.00, 1, '2026-03-19 14:57:44'),
('18018', 'Souper Meal Noodles', 'Snacks', 40.00, 2, '2026-03-19 14:57:47'),
('18030', 'Sari Sari Veges With Pork', 'Food', 35.00, 2, '2026-03-19 14:57:46'),
('18030', 'Sari Sari Veges With Pork', 'Food', 35.00, 13, '2026-03-19 14:57:49'),
('18050', 'Sweet Ham', 'Food', 15.00, 1, '2026-03-19 14:57:43'),
('18050', 'Sweet Ham', 'Food', 15.00, 2, '2026-03-19 14:57:46'),
('18068', ' Chicken Small', 'Food', 40.00, 1, '2026-03-19 14:57:43'),
('18068', ' Chicken Small', 'Food', 40.00, 2, '2026-03-19 14:57:45'),
('18068', ' Chicken Small', 'Food', 40.00, 13, '2026-03-19 14:57:49'),
('18070', 'Pork Barbecue', 'Food', 20.00, 2, '2026-03-19 14:57:46'),
('18104', 'Beng Beng', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('18109', 'Plain Rice Half', 'Food', 5.00, 1, '2026-03-19 14:57:43'),
('18109', 'Plain Rice Half', 'Food', 5.00, 2, '2026-03-19 14:57:46'),
('18109', 'Plain Rice Half', 'Food', 5.00, 13, '2026-03-19 14:57:49'),
('18124', 'Nestle Milo', 'Drinks', 30.00, 1, '2026-03-19 14:57:43'),
('18124', 'Nestle Milo', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('18124', 'Nestle Milo', 'Drinks', 30.00, 13, '2026-03-19 14:57:49'),
('18126', 'Bread Pie', 'Bread', 10.00, 1, '2026-03-19 14:57:42'),
('18126', 'Bread Pie', 'Bread', 10.00, 2, '2026-03-19 14:57:45'),
('18126', 'Bread Pie', 'Bread', 10.00, 13, '2026-03-19 14:57:48'),
('18146', 'Nova Small', 'Snacks', 25.00, 1, '2026-03-19 14:57:44'),
('18155', 'Minute Maid Bottle', 'Drinks', 20.00, 1, '2026-03-19 14:57:43'),
('18155', 'Minute Maid Bottle', 'Drinks', 20.00, 2, '2026-03-19 14:57:45'),
('18155', 'Minute Maid Bottle', 'Drinks', 20.00, 13, '2026-03-19 14:57:49'),
('18210', 'Minatamis', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('18210', 'Minatamis', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('18211', 'Mini Pizza', 'Snacks', 60.00, 2, '2026-03-19 14:57:47'),
('18248', 'Kopiko Black, Blanca And Brown', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('18248', 'Kopiko Black, Blanca And Brown', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('18248', 'Kopiko Black, Blanca And Brown', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('18250', 'Kopiko Lucky Day', 'Drinks', 25.00, 1, '2026-03-19 14:57:43'),
('18250', 'Kopiko Lucky Day', 'Drinks', 25.00, 2, '2026-03-19 14:57:45'),
('18311', 'Paddle Pop Spider', 'Ice Cream', 25.00, 1, '2026-03-19 14:57:43'),
('18312', 'Paddlepop Ube', 'Ice Cream', 20.00, 1, '2026-03-19 14:57:43'),
('18313', 'Corneto ', 'Ice Cream', 30.00, 1, '2026-03-19 14:57:43'),
('18315', 'Boom Boom ', 'Ice Cream', 20.00, 1, '2026-03-19 14:57:43'),
('18315', 'Boom Boom ', 'Ice Cream', 20.00, 2, '2026-03-19 14:57:46'),
('18316', 'Creadaestick Choco Mallow', 'Ice Cream', 15.00, 1, '2026-03-19 14:57:43'),
('18387', 'Carbonara', 'Food', 35.00, 1, '2026-03-19 14:57:43'),
('18387', 'Carbonara', 'Food', 35.00, 2, '2026-03-19 14:57:46'),
('18387', 'Carbonara', 'Food', 35.00, 13, '2026-03-19 14:57:49'),
('18390', 'Coffee Stick', 'Drinks', 10.00, 2, '2026-03-19 14:57:45'),
('18390', 'Coffee Stick', 'Drinks', 10.00, 13, '2026-03-19 14:57:49'),
('18396', 'Ice Cream Cup', 'Ice Cream', 25.00, 1, '2026-03-19 14:57:43'),
('18396', 'Ice Cream Cup', 'Ice Cream', 25.00, 2, '2026-03-19 14:57:46'),
('18397', 'Watermelon Stick', 'Ice Cream', 15.00, 1, '2026-03-19 14:57:43'),
('18407', 'Wilkins Small', 'Drinks', 12.00, 1, '2026-03-19 14:57:43'),
('18407', 'Wilkins Small', 'Drinks', 12.00, 2, '2026-03-19 14:57:45'),
('18407', 'Wilkins Small', 'Drinks', 12.00, 13, '2026-03-19 14:57:49'),
('18430', 'Baby Wipes', 'Supply', 25.00, 1, '2026-03-19 14:57:44'),
('18430', 'Baby Wipes', 'Supply', 25.00, 2, '2026-03-19 14:57:47'),
('18443', 'Koko Crunch Big', 'Snacks', 12.00, 2, '2026-03-19 14:57:47'),
('18444', 'Nescafe', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('18444', 'Nescafe', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('18445', 'Vitasoy Small', 'Drinks', 25.00, 1, '2026-03-19 14:57:43'),
('18445', 'Vitasoy Small', 'Drinks', 25.00, 2, '2026-03-19 14:57:45'),
('18445', 'Vitasoy Small', 'Drinks', 25.00, 13, '2026-03-19 14:57:49'),
('18449', 'Nova Small,piattos', 'Snacks', 25.00, 13, '2026-03-19 14:57:51'),
('18450', 'Piattos Small', 'Snacks', 25.00, 1, '2026-03-19 14:57:44'),
('18451', 'Mang Juan Small', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('18451', 'Mang Juan Small', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('18451', 'Mang Juan Small', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('18452', 'Chippy Small', 'Snacks', 12.00, 1, '2026-03-19 14:57:44'),
('18454', 'Chiz Curls Small', 'Snacks', 12.00, 1, '2026-03-19 14:57:44'),
('18461', 'Kwek Kwek', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('18467', 'Graphing Paper', 'Supply', 1.00, 2, '2026-03-19 14:57:47'),
('18477', 'Pretzel Stick', 'Snacks', 20.00, 1, '2026-03-19 14:57:44'),
('18477', 'Pretzel Stick', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('18477', 'Pretzel Stick', 'Snacks', 20.00, 13, '2026-03-19 14:57:51'),
('18480', 'Minute Maid Tetra', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('18480', 'Minute Maid Tetra', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('18482', 'Boiled Sweet Corn Big', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('18499', 'Regular Hotdog', 'Food', 20.00, 1, '2026-03-19 14:57:43'),
('18499', 'Regular Hotdog', 'Food', 20.00, 2, '2026-03-19 14:57:46'),
('18499', 'Regular Hotdog', 'Food', 20.00, 13, '2026-03-19 14:57:49'),
('18524', 'Nuggets', 'Snacks', 2.00, 1, '2026-03-19 14:57:44'),
('18524', 'Nuggets', 'Snacks', 2.00, 2, '2026-03-19 14:57:47'),
('18543', 'Cream O Vanilla Big', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('18543', 'Cream O Vanilla Big', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('18555', 'Assorted Candy', 'Candy', 1.00, 2, '2026-03-19 14:57:45'),
('18555', 'Assorted Candy', 'Snacks', 1.00, 2, '2026-03-19 14:57:47'),
('18583', 'Green Banana', 'Fruits', 5.00, 1, '2026-03-19 14:57:43'),
('18583', 'Green Banana', 'Fruits', 5.00, 2, '2026-03-19 14:57:46'),
('18588', 'Cheese Bread ', 'Bread', 7.00, 1, '2026-03-19 14:57:42'),
('18588', 'Cheese Bread ', 'Bread', 7.00, 2, '2026-03-19 14:57:45'),
('18591', 'Cal Cheese Small', 'Snacks', 10.00, 1, '2026-03-19 14:57:44'),
('18591', 'Cal Cheese Small', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('18609', 'Vcut Small', 'Snacks', 25.00, 1, '2026-03-19 14:57:44'),
('18610', 'Dinuldog', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('18610', 'Dinuldog', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('18623', ' Spoon And Fork', 'Consignment', 5.00, 13, '2026-03-19 14:57:48'),
('18626', ' Consignment @ 15.00', 'Consignment', 15.00, 13, '2026-03-19 14:57:48'),
('18634', ' Consignment @ 20.00', 'Consignment', 20.00, 13, '2026-03-19 14:57:48'),
('18651', 'Consignment @ 10.00', 'Consignment', 10.00, 13, '2026-03-19 14:57:48'),
('18659', 'Squid Ball', 'Snacks', 3.00, 1, '2026-03-19 14:57:44'),
('18659', 'Squid Ball', 'Snacks', 3.00, 2, '2026-03-19 14:57:47'),
('18671', 'Mini Pan Cake', 'Snacks', 5.00, 1, '2026-03-19 14:57:44'),
('18671', 'Mini Pan Cake', 'Snacks', 5.00, 2, '2026-03-19 14:57:47'),
('18686', 'Coffeemate', 'Drinks', 5.00, 2, '2026-03-19 14:57:45'),
('18686', 'Coffeemate', 'Drinks', 5.00, 13, '2026-03-19 14:57:49'),
('18814', 'Amlan Small ', 'Drinks', 12.00, 1, '2026-03-19 14:57:43'),
('18825', 'Koko Krunch Small', 'Snacks', 12.00, 1, '2026-03-19 14:57:44'),
('18825', 'Koko Krunch Small', 'Snacks', 12.00, 2, '2026-03-19 14:57:47'),
('18833', 'Consignment @ 25.00', 'Consignment', 25.00, 13, '2026-03-19 14:57:48'),
('18888', 'Ice Cream 3 N 1', 'Ice Cream', 100.00, 1, '2026-03-19 14:57:43'),
('18890', 'Ponkan', 'Fruits', 25.00, 1, '2026-03-19 14:57:43'),
('18890', 'Ponkan', 'Fruits', 25.00, 2, '2026-03-19 14:57:46'),
('18959', 'Sardines With Egg', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('18972', 'Banana Small', 'Fruits', 3.00, 2, '2026-03-19 14:57:46'),
('19053', 'Presto Big', 'Snacks', 25.00, 2, '2026-03-19 14:57:47'),
('19053', 'Presto Big', 'Snacks', 25.00, 13, '2026-03-19 14:57:51'),
('19072', 'Cobra 350 Ml', 'Drinks', 27.00, 2, '2026-03-19 14:57:45'),
('19104', 'Tuna Sandwhich', 'Snacks', 40.00, 2, '2026-03-19 14:57:47'),
('19116', 'Vitamilk Pouch', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('19116', 'Vitamilk Pouch', 'Drinks', 30.00, 13, '2026-03-19 14:57:49'),
('19135', 'Big Chorizo ', 'Food', 25.00, 1, '2026-03-19 14:57:43'),
('19135', 'Big Chorizo ', 'Food', 25.00, 2, '2026-03-19 14:57:45'),
('19230', 'Toasted Bread', 'Bread', 5.00, 1, '2026-03-19 14:57:42'),
('19230', 'Toasted Bread', 'Bread', 5.00, 2, '2026-03-19 14:57:45'),
('19230', 'Toasted Bread', 'Bread', 5.00, 13, '2026-03-19 14:57:48'),
('19273', 'Vegetables Menu', 'Food', 30.00, 1, '2026-03-19 14:57:43'),
('19273', 'Vegetables Menu', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('19276', 'Salad Menu', 'Food', 30.00, 1, '2026-03-19 14:57:43'),
('19293', 'Fresh Milk', 'Drinks', 37.00, 1, '2026-03-19 14:57:43'),
('19293', 'Fresh Milk', 'Drinks', 37.00, 2, '2026-03-19 14:57:45'),
('19293', 'Fresh Milk', 'Drinks', 37.00, 13, '2026-03-19 14:57:49'),
('19296', 'C2 ', 'Drinks', 16.00, 2, '2026-03-19 14:57:45'),
('19296', 'C2 ', 'Drinks', 16.00, 13, '2026-03-19 14:57:49'),
('19299', 'Minute Maid', 'Drinks', 20.00, 1, '2026-03-19 14:57:43'),
('19299', 'Minute Maid', 'Drinks', 20.00, 2, '2026-03-19 14:57:45'),
('19301', 'Bread @ 7.00', 'Bread', 7.00, 1, '2026-03-19 14:57:42'),
('19301', 'Bread @ 7.00', 'Bread', 7.00, 2, '2026-03-19 14:57:45'),
('19301', 'Bread @ 7.00', 'Bread', 7.00, 13, '2026-03-19 14:57:48'),
('19302', 'Bread @ 10.00', 'Bread', 10.00, 1, '2026-03-19 14:57:42'),
('19302', 'Bread @ 10.00', 'Bread', 10.00, 2, '2026-03-19 14:57:45'),
('19315', 'Paper Bowl', 'Supply', 5.00, 1, '2026-03-19 14:57:44'),
('19315', 'Paper Bowl', 'Supply', 5.00, 2, '2026-03-19 14:57:47'),
('19317', 'Pasta @ 30.00', 'Food', 30.00, 1, '2026-03-19 14:57:43'),
('19317', 'Pasta @ 30.00', 'Food', 30.00, 2, '2026-03-19 14:57:46'),
('19421', 'Pizza', 'Snacks', 60.00, 1, '2026-03-19 14:57:44'),
('19421', 'Pizza', 'Snacks', 60.00, 2, '2026-03-19 14:57:47'),
('19517', 'Rite And Lite', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('19622', 'Nestea', 'Drinks', 25.00, 1, '2026-03-19 14:57:43'),
('19622', 'Nestea', 'Drinks', 25.00, 2, '2026-03-19 14:57:45'),
('19624', 'Cobra', 'Drinks', 27.00, 2, '2026-03-19 14:57:45'),
('19625', 'Amlan Water', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('19625', 'Amlan Water', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('19625', 'Amlan Water', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('19626', 'Coffee Cup', 'Supply', 8.00, 1, '2026-03-19 14:57:44'),
('19627', 'Paper Cup 16 Oz', 'Supply', 10.00, 1, '2026-03-19 14:57:44'),
('19628', 'Paper Cup Lids 16oz', 'Supply', 5.00, 1, '2026-03-19 14:57:44'),
('19629', 'Boba Straw', 'Supply', 80.00, 1, '2026-03-19 14:57:44'),
('19631', 'Coffee Stick With Creamer', 'Drinks', 12.00, 1, '2026-03-19 14:57:43'),
('19631', 'Coffee Stick With Creamer', 'Drinks', 12.00, 2, '2026-03-19 14:57:45'),
('19634', 'Veggies With Pork', 'Food', 35.00, 1, '2026-03-19 14:57:43'),
('19634', 'Veggies With Pork', 'Food', 35.00, 2, '2026-03-19 14:57:46'),
('19661', 'Ice Cream Soft Serve', 'Ice Cream', 20.00, 2, '2026-03-19 14:57:46'),
('19723', 'Tempura', 'Snacks', 5.00, 1, '2026-03-19 14:57:44'),
('19723', 'Tempura', 'Snacks', 5.00, 2, '2026-03-19 14:57:47'),
('19730', 'Lunch Box', 'Supply', 12.00, 1, '2026-03-19 14:57:44'),
('19730', 'Lunch Box', 'Supply', 12.00, 2, '2026-03-19 14:57:47'),
('19732', 'Del Monte Juice', 'Drinks', 35.00, 1, '2026-03-19 14:57:43'),
('19732', 'Del Monte Juice', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('19732', 'Del Monte Juice', 'Drinks', 35.00, 13, '2026-03-19 14:57:49'),
('19733', 'Fit And Right', 'Drinks', 30.00, 1, '2026-03-19 14:57:43'),
('19733', 'Fit And Right', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('19733', 'Fit And Right', 'Drinks', 30.00, 13, '2026-03-19 14:57:49'),
('19745', 'Fish Ball', 'Snacks', 2.00, 1, '2026-03-19 14:57:44'),
('19772', 'Lift', 'Drinks', 25.00, 2, '2026-03-19 14:57:45'),
('19773', 'Lift Drinks', 'Drinks', 25.00, 1, '2026-03-19 14:57:43'),
('19773', 'Lift Drinks', 'Drinks', 25.00, 2, '2026-03-19 14:57:45'),
('19773', 'Lift Drinks', 'Drinks', 25.00, 13, '2026-03-19 14:57:49'),
('19775', 'Roller Coaster Small', 'Snacks', 12.00, 1, '2026-03-19 14:57:44'),
('19778', 'Swiss Miss', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('19778', 'Swiss Miss', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('19779', 'Sweetcorn Mr.chips', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('19779', 'Sweetcorn Mr.chips', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('19839', 'Coke In Can', 'Drinks', 45.00, 1, '2026-03-19 14:57:43'),
('19839', 'Coke In Can', 'Drinks', 45.00, 13, '2026-03-19 14:57:49'),
('19856', 'Nescafé Drinks', 'Drinks', 45.00, 1, '2026-03-19 14:57:43'),
('19856', 'Nescafé Drinks', 'Drinks', 45.00, 2, '2026-03-19 14:57:45'),
('19856', 'Nescafé Drinks', 'Drinks', 45.00, 13, '2026-03-19 14:57:49'),
('19879', 'Consignment @ 12.00', 'Consignment', 12.00, 13, '2026-03-19 14:57:48'),
('19892', 'Consignment @ 40.00', 'Consignment', 40.00, 13, '2026-03-19 14:57:48'),
('19913', 'Pascual Yogurt', 'Snacks', 30.00, 1, '2026-03-19 14:57:44'),
('19913', 'Pascual Yogurt', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('19913', 'Pascual Yogurt', 'Snacks', 30.00, 13, '2026-03-19 14:57:51'),
('19915', 'Cal Cheese', 'Snacks', 20.00, 1, '2026-03-19 14:57:44'),
('19915', 'Cal Cheese', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('19915', 'Cal Cheese', 'Snacks', 20.00, 13, '2026-03-19 14:57:50'),
('19936', 'Mixed Fruits', 'Fruits', 25.00, 1, '2026-03-19 14:57:43'),
('19936', 'Mixed Fruits', 'Fruits', 25.00, 2, '2026-03-19 14:57:46'),
('19936', 'Mixed Fruits', 'Fruits', 25.00, 13, '2026-03-19 14:57:49'),
('19972', 'Magic Chips Small', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('19972', 'Magic Chips Small', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('20059', 'Garden Salad', 'Food', 50.00, 1, '2026-03-19 14:57:43'),
('20059', 'Garden Salad', 'Food', 50.00, 2, '2026-03-19 14:57:46'),
('20074', 'Summit Big', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('20075', 'Blue', 'Drinks', 30.00, 1, '2026-03-19 14:57:43'),
('20075', 'Blue', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('20117', 'Choco Knot Big', 'Snacks', 20.00, 1, '2026-03-19 14:57:44'),
('20117', 'Choco Knot Big', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('20117', 'Choco Knot Big', 'Snacks', 20.00, 13, '2026-03-19 14:57:50'),
('20149', 'Del Monte Bottles ', 'Drinks', 30.00, 1, '2026-03-19 14:57:43'),
('20149', 'Del Monte Bottles ', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('20190', 'Pocari Sweat', 'Drinks', 45.00, 1, '2026-03-19 14:57:43'),
('20190', 'Pocari Sweat', 'Drinks', 45.00, 2, '2026-03-19 14:57:45'),
('20191', 'Vitamin Boost', 'Drinks', 50.00, 2, '2026-03-19 14:57:45'),
('20191', 'Vitamin Boost', 'Drinks', 50.00, 13, '2026-03-19 14:57:49'),
('20192', 'Elisha Sparkling ', 'Drinks', 55.00, 1, '2026-03-19 14:57:43'),
('20192', 'Elisha Sparkling ', 'Drinks', 55.00, 2, '2026-03-19 14:57:45'),
('20193', 'Gatorade ', 'Drinks', 45.00, 2, '2026-03-19 14:57:45'),
('20196', 'Bing Bong', 'Drinks', 45.00, 1, '2026-03-19 14:57:43'),
('20196', 'Bing Bong', 'Drinks', 45.00, 2, '2026-03-19 14:57:45'),
('20243', 'Healthtea Drinks', 'Drinks', 20.00, 1, '2026-03-19 14:57:43'),
('20243', 'Healthtea Drinks', 'Drinks', 20.00, 2, '2026-03-19 14:57:45'),
('20243', 'Healthtea Drinks', 'Drinks', 20.00, 13, '2026-03-19 14:57:49'),
('20263', 'Vitamilk Small', 'Drinks', 20.00, 1, '2026-03-19 14:57:43'),
('20263', 'Vitamilk Small', 'Drinks', 20.00, 2, '2026-03-19 14:57:45'),
('20336', 'Dewberry Yougurt Cake ', 'Snacks', 15.00, 1, '2026-03-19 14:57:44'),
('20336', 'Dewberry Yougurt Cake ', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('20336', 'Dewberry Yougurt Cake ', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('20348', 'Refreshing Drinks And Milktea', 'Drinks', 35.00, 1, '2026-03-19 14:57:43'),
('20348', 'Refreshing Drinks And Milktea', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('20348', 'Refreshing Drinks And Milktea', 'Drinks', 35.00, 13, '2026-03-19 14:57:49'),
('20350', 'Amlan Water S', 'Drinks', 12.00, 2, '2026-03-19 14:57:45'),
('20350', 'Amlan Water S', 'Drinks', 12.00, 13, '2026-03-19 14:57:49'),
('20351', 'Consignment @25', 'Consignment', 25.00, 13, '2026-03-19 14:57:48'),
('20352', 'Consignment @35', 'Consignment', 35.00, 13, '2026-03-19 14:57:48'),
('20355', 'Consignment @8', 'Consignment', 8.00, 13, '2026-03-19 14:57:48'),
('20358', 'Consignment @30', 'Consignment', 30.00, 13, '2026-03-19 14:57:48'),
('20359', 'Consignment @12', 'Consignment', 12.00, 13, '2026-03-19 14:57:48'),
('20360', 'Consignment @50', 'Consignment', 50.00, 13, '2026-03-19 14:57:48'),
('20390', 'Kopiko ', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('20390', 'Kopiko ', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('20392', 'Nestea', 'Drinks', 25.00, 13, '2026-03-19 14:57:49'),
('20394', 'Cobra ', 'Drinks', 27.00, 1, '2026-03-19 14:57:43'),
('20394', 'Cobra ', 'Drinks', 27.00, 13, '2026-03-19 14:57:49'),
('20395', 'Street Food', 'Snacks', 2.00, 2, '2026-03-19 14:57:47'),
('20395', 'Street Food', 'Snacks', 2.00, 13, '2026-03-19 14:57:51'),
('20396', 'Tempura', 'Snacks', 5.00, 2, '2026-03-19 14:57:47'),
('20396', 'Tempura', 'Snacks', 5.00, 13, '2026-03-19 14:57:51'),
('20397', 'Toron,combo', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('20397', 'Toron,combo', 'Snacks', 10.00, 13, '2026-03-19 14:57:51'),
('20399', 'Pasta', 'Food', 30.00, 2, '2026-03-19 14:57:46'),
('20399', 'Pasta', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('20401', 'Soup Menu', 'Food', 35.00, 1, '2026-03-19 14:57:43'),
('20401', 'Soup Menu', 'Food', 35.00, 2, '2026-03-19 14:57:46'),
('20401', 'Soup Menu', 'Food', 35.00, 13, '2026-03-19 14:57:49'),
('20402', 'Garlic Rice', 'Food', 15.00, 1, '2026-03-19 14:57:43'),
('20402', 'Garlic Rice', 'Food', 15.00, 2, '2026-03-19 14:57:46'),
('20402', 'Garlic Rice', 'Food', 15.00, 13, '2026-03-19 14:57:49'),
('20405', 'Egg', 'Food', 15.00, 1, '2026-03-19 14:57:43'),
('20405', 'Egg', 'Food', 15.00, 2, '2026-03-19 14:57:46'),
('20405', 'Egg', 'Food', 15.00, 13, '2026-03-19 14:57:49'),
('20407', 'Candy', 'Candy', 1.00, 13, '2026-03-19 14:57:48'),
('20407', 'Candy', 'Snacks', 1.00, 13, '2026-03-19 14:57:51'),
('20408', 'Souper Meal Noodles ', 'Snacks', 40.00, 2, '2026-03-19 14:57:47'),
('20408', 'Souper Meal Noodles ', 'Snacks', 40.00, 13, '2026-03-19 14:57:51'),
('20409', 'Cheese Stick 2', 'Snacks', 8.00, 13, '2026-03-19 14:57:50'),
('20410', 'Pretzels,magic Chips', 'Snacks', 12.00, 2, '2026-03-19 14:57:47'),
('20410', 'Pretzels,magic Chips', 'Snacks', 12.00, 13, '2026-03-19 14:57:51'),
('20411', 'Cup Noodles', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('20413', 'Nuggsilog', 'Food', 65.00, 2, '2026-03-19 14:57:46'),
('20413', 'Nuggsilog', 'Food', 65.00, 13, '2026-03-19 14:57:49'),
('20418', 'Lingsilog', 'Food', 55.00, 13, '2026-03-19 14:57:49'),
('20420', 'C2', 'Drinks', 16.00, 1, '2026-03-19 14:57:43'),
('20420', 'C2', 'Drinks', 16.00, 2, '2026-03-19 14:57:45'),
('20422', 'Quake Overload 2', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('20423', 'Consignment @45', 'Consignment', 45.00, 13, '2026-03-19 14:57:48'),
('20424', 'Longganisa', 'Food', 15.00, 1, '2026-03-19 14:57:43'),
('20424', 'Longganisa', 'Food', 15.00, 2, '2026-03-19 14:57:46'),
('20424', 'Longganisa', 'Food', 15.00, 13, '2026-03-19 14:57:49'),
('20426', 'Longsilog', 'Food', 60.00, 13, '2026-03-19 14:57:49'),
('20427', 'Bacsilog', 'Food', 75.00, 13, '2026-03-19 14:57:49'),
('20430', 'Rite And Lite', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('20430', 'Rite And Lite', 'Drinks', 35.00, 13, '2026-03-19 14:57:49'),
('20432', 'Bacon', 'Food', 15.00, 13, '2026-03-19 14:57:49'),
('20433', 'Sweet Ham', 'Food', 15.00, 2, '2026-03-19 14:57:46'),
('20433', 'Sweet Ham', 'Food', 15.00, 13, '2026-03-19 14:57:49'),
('20434', 'Consignment @3', 'Consignment', 3.00, 13, '2026-03-19 14:57:48'),
('20437', 'Coffee Stick', 'Drinks', 10.00, 1, '2026-03-19 14:57:43'),
('20437', 'Coffee Stick', 'Drinks', 10.00, 2, '2026-03-19 14:57:45'),
('20440', 'Champorado', 'Snacks', 15.00, 2, '2026-03-19 14:57:47'),
('20440', 'Champorado', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('20448', 'Presto Small', 'Snacks', 10.00, 2, '2026-03-19 14:57:47'),
('20449', 'Sterilized Milk', 'Drinks', 30.00, 2, '2026-03-19 14:57:45'),
('20449', 'Sterilized Milk', 'Drinks', 30.00, 13, '2026-03-19 14:57:49'),
('20450', 'Plain Rice ', 'Food', 10.00, 1, '2026-03-19 14:57:43'),
('20450', 'Plain Rice ', 'Food', 10.00, 2, '2026-03-19 14:57:46'),
('20450', 'Plain Rice ', 'Food', 10.00, 13, '2026-03-19 14:57:49'),
('20453', 'Fish Menu 2', 'Food', 50.00, 1, '2026-03-19 14:57:43'),
('20453', 'Fish Menu 2', 'Food', 50.00, 2, '2026-03-19 14:57:46'),
('20453', 'Fish Menu 2', 'Food', 50.00, 13, '2026-03-19 14:57:49'),
('20454', 'French Fries ', 'Snacks', 20.00, 2, '2026-03-19 14:57:47'),
('20454', 'French Fries ', 'Snacks', 20.00, 13, '2026-03-19 14:57:50'),
('20458', 'Pancake', 'Snacks', 5.00, 13, '2026-03-19 14:57:51'),
('20459', 'Energen', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('20459', 'Energen', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('20459', 'Energen', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('20460', 'Tocilog', 'Food', 70.00, 13, '2026-03-19 14:57:49'),
('20467', 'Chosilog', 'Food', 60.00, 13, '2026-03-19 14:57:49'),
('20468', 'Cantonsilog', 'Food', 65.00, 13, '2026-03-19 14:57:49'),
('20469', 'Monsilog', 'Food', 70.00, 13, '2026-03-19 14:57:49'),
('20471', 'Pork Menu', 'Food', 60.00, 1, '2026-03-19 14:57:43'),
('20471', 'Pork Menu', 'Food', 60.00, 2, '2026-03-19 14:57:46'),
('20471', 'Pork Menu', 'Food', 60.00, 13, '2026-03-19 14:57:49'),
('20473', 'Chicken Menu', 'Food', 50.00, 1, '2026-03-19 14:57:43'),
('20473', 'Chicken Menu', 'Food', 50.00, 2, '2026-03-19 14:57:46'),
('20473', 'Chicken Menu', 'Food', 50.00, 13, '2026-03-19 14:57:49'),
('20474', 'Swak Bearbrand', 'Drinks', 15.00, 1, '2026-03-19 14:57:43'),
('20474', 'Swak Bearbrand', 'Drinks', 15.00, 2, '2026-03-19 14:57:45'),
('20474', 'Swak Bearbrand', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('20475', 'Dynamite', 'Snacks', 10.00, 13, '2026-03-19 14:57:50'),
('20479', 'Nescafe 2', 'Drinks', 15.00, 13, '2026-03-19 14:57:49'),
('20489', 'Egg Sandwich ', 'Snacks', 25.00, 2, '2026-03-19 14:57:47'),
('20489', 'Egg Sandwich ', 'Snacks', 25.00, 13, '2026-03-19 14:57:50'),
('20508', 'Banana 2', 'Fruits', 5.00, 13, '2026-03-19 14:57:49'),
('20510', 'Pizza 2', 'Snacks', 60.00, 13, '2026-03-19 14:57:51'),
('20514', 'Boom Boom Max', 'Ice Cream', 25.00, 1, '2026-03-19 14:57:43'),
('20529', 'French Toast Bread', 'Snacks', 15.00, 13, '2026-03-19 14:57:50'),
('20533', 'Redbull', 'Drinks', 82.00, 1, '2026-03-19 14:57:43'),
('20548', 'Pancit Canton Jumbo', 'Food', 30.00, 2, '2026-03-19 14:57:46'),
('20548', 'Pancit Canton Jumbo', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('20552', 'Big Mr Chips, Rcoaster,chiz Curlz', 'Snacks', 30.00, 1, '2026-03-19 14:57:44'),
('20552', 'Big Mr Chips, Rcoaster,chiz Curlz', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('20556', 'Chicken Nuggets', 'Food', 10.00, 1, '2026-03-19 14:57:43'),
('20556', 'Chicken Nuggets', 'Food', 10.00, 2, '2026-03-19 14:57:46'),
('20556', 'Chicken Nuggets', 'Food', 10.00, 13, '2026-03-19 14:57:49'),
('20557', 'Luncheon Meat', 'Food', 20.00, 1, '2026-03-19 14:57:43'),
('20557', 'Luncheon Meat', 'Food', 20.00, 2, '2026-03-19 14:57:46'),
('20557', 'Luncheon Meat', 'Food', 20.00, 13, '2026-03-19 14:57:49'),
('20558', 'Corned Beef', 'Food', 30.00, 1, '2026-03-19 14:57:43'),
('20558', 'Corned Beef', 'Food', 30.00, 2, '2026-03-19 14:57:46'),
('20558', 'Corned Beef', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('20574', 'Cornsilog', 'Food', 65.00, 13, '2026-03-19 14:57:49'),
('20575', 'Hamonado', 'Food', 30.00, 13, '2026-03-19 14:57:49'),
('20579', 'Hamsilog', 'Food', 50.00, 13, '2026-03-19 14:57:49'),
('20580', 'Hotsilog', 'Food', 55.00, 13, '2026-03-19 14:57:49'),
('20607', 'Shrimp Menu', 'Food', 50.00, 1, '2026-03-19 14:57:43'),
('20607', 'Shrimp Menu', 'Food', 50.00, 2, '2026-03-19 14:57:46'),
('20607', 'Shrimp Menu', 'Food', 50.00, 13, '2026-03-19 14:57:49'),
('20632', 'Waffle Time ', 'Ice Cream', 20.00, 1, '2026-03-19 14:57:43'),
('20647', 'Clover', 'Snacks', 40.00, 1, '2026-03-19 14:57:44'),
('20647', 'Clover', 'Snacks', 40.00, 2, '2026-03-19 14:57:47'),
('20647', 'Clover', 'Snacks', 40.00, 13, '2026-03-19 14:57:50'),
('20649', 'Nagaraya', 'Snacks', 30.00, 1, '2026-03-19 14:57:44'),
('20649', 'Nagaraya', 'Snacks', 30.00, 2, '2026-03-19 14:57:47'),
('20649', 'Nagaraya', 'Snacks', 30.00, 13, '2026-03-19 14:57:50'),
('20667', 'Cali', 'Drinks', 35.00, 1, '2026-03-19 14:57:43'),
('20667', 'Cali', 'Drinks', 35.00, 2, '2026-03-19 14:57:45'),
('20667', 'Cali', 'Drinks', 35.00, 13, '2026-03-19 14:57:49'),
('20720', 'Sneakers', 'Snacks', 19.00, 1, '2026-03-19 14:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `icon` varchar(255) DEFAULT '<i class="ph-duotone ph-fork-knife"></i>'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `is_active`, `sort_order`, `icon`) VALUES
(1, 'Hot Coffee', 1, 1, '<i class=\"ph-duotone ph-coffee\"></i>'),
(2, 'Cold Coffee', 1, 2, '<i class=\"ph-duotone ph-coffee\"></i>'),
(3, 'Specialty Drinks (Hot/Cold)', 1, 3, '<i class=\"ph-duotone ph-star\"></i>'),
(4, 'Milk Tea', 1, 4, '<i class=\"ph-duotone ph-coffee\"></i>'),
(5, 'Fruity Soda', 1, 5, '<svg viewBox=\"0 0 256 256\" style=\"width:1em;height:1em;display:inline-block;vertical-align:middle;\"><path d=\"M192,104H64a8,8,0,0,1-8-8V80a8,8,0,0,1,8-8H192a8,8,0,0,1,8,8V96A8,8,0,0,1,192,104Z\" fill=\"currentColor\" opacity=\"0.2\"/><path d=\"M192,104H64a8,8,0,'),
(6, 'Add Ons', 1, 6, '<i class=\"ph-duotone ph-plus-circle\"></i>'),
(7, 'Ice Cream in Cups (100g)', 1, 7, '<i class=\"ph-duotone ph-ice-cream\"></i>'),
(8, 'Ice Cream Bar (95g)', 1, 8, '<i class=\"ph-duotone ph-popsicle\"></i>'),
(9, 'Milk Drink (350ml)', 1, 9, '<i class=\"ph-duotone ph-beer-bottle\"></i>');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL,
  `raw_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `change_amount` decimal(10,3) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_logs`
--

INSERT INTO `inventory_logs` (`log_id`, `raw_id`, `user_id`, `change_amount`, `reason`, `log_date`) VALUES
(1, 1, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(2, 2, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(3, 3, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(4, 4, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(5, 5, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(6, 6, 1, 0.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(7, 23, 1, 50.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(8, 24, 1, 10.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(9, 25, 1, 10.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(10, 26, 1, 20.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(11, 27, 1, 20.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(12, 28, 1, 15.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(13, 29, 1, 5.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(14, 30, 1, 3.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(15, 31, 1, 30.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(16, 32, 1, 5.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(17, 33, 1, 5.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(18, 34, 1, 500.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(19, 35, 1, 500.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(20, 36, 1, 1000.000, 'Initial Stock In', '2025-12-15 14:26:53'),
(32, 2, NULL, -0.010, 'Order sale', '2026-02-10 07:21:49'),
(33, 28, NULL, -0.050, 'Order sale', '2026-02-10 07:21:49'),
(34, 34, NULL, -1.000, 'Order sale', '2026-02-10 07:21:49'),
(35, 4, NULL, -0.018, 'Order sale', '2026-02-10 11:14:39'),
(36, 23, NULL, -0.200, 'Order sale', '2026-02-10 11:14:39'),
(37, 34, NULL, -1.000, 'Order sale', '2026-02-10 11:14:39'),
(38, 4, NULL, -0.018, 'Order sale', '2026-02-11 09:18:32'),
(39, 34, NULL, -1.000, 'Order sale', '2026-02-11 09:18:32'),
(40, 4, NULL, -0.018, 'Order sale', '2026-02-11 10:58:40'),
(41, 23, NULL, -0.200, 'Order sale', '2026-02-11 10:58:40'),
(42, 34, NULL, -1.000, 'Order sale', '2026-02-11 10:58:40'),
(43, 4, NULL, -0.018, 'Order sale', '2026-02-11 11:01:53'),
(44, 23, NULL, -0.200, 'Order sale', '2026-02-11 11:01:53'),
(45, 34, NULL, -1.000, 'Order sale', '2026-02-11 11:01:53'),
(46, 4, NULL, -0.036, 'Order sale', '2026-02-20 02:08:32'),
(47, 23, NULL, -0.400, 'Order sale', '2026-02-20 02:08:32'),
(48, 34, NULL, -2.000, 'Order sale', '2026-02-20 02:08:32'),
(49, 31, NULL, -1.250, 'Order sale', '2026-02-24 09:25:52'),
(50, 32, NULL, -0.150, 'Order sale', '2026-02-24 09:25:52'),
(51, 34, NULL, -5.000, 'Order sale', '2026-02-24 09:25:52'),
(52, 4, NULL, -0.080, 'Order sale', '2026-02-25 00:36:21'),
(53, 1, NULL, -0.160, 'Order sale', '2026-02-25 00:36:21'),
(54, 4, NULL, -0.018, 'Order sale', '2026-02-25 05:52:24'),
(55, 23, NULL, -0.200, 'Order sale', '2026-02-25 05:52:24'),
(56, 34, NULL, -1.000, 'Order sale', '2026-02-25 05:52:24'),
(57, 4, NULL, -0.018, 'Order sale', '2026-02-27 07:10:10'),
(58, 23, NULL, -0.200, 'Order sale', '2026-02-27 07:10:10'),
(59, 34, NULL, -1.000, 'Order sale', '2026-02-27 07:10:10'),
(60, 31, NULL, -0.250, 'Order sale', '2026-02-27 07:10:10'),
(61, 32, NULL, -0.030, 'Order sale', '2026-02-27 07:10:10'),
(62, 34, NULL, -1.000, 'Order sale', '2026-02-27 07:10:10'),
(63, 4, 5, -0.018, 'Order sale', '2026-02-28 03:30:36'),
(64, 23, 5, -0.200, 'Order sale', '2026-02-28 03:30:36'),
(65, 34, 5, -1.000, 'Order sale', '2026-02-28 03:30:36');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_raw`
--

CREATE TABLE `inventory_raw` (
  `raw_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `unit_of_measure` varchar(20) NOT NULL,
  `quantity_on_hand` decimal(10,3) DEFAULT 0.000,
  `reorder_point` decimal(10,3) DEFAULT 10.000,
  `cost_per_unit` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_raw`
--

INSERT INTO `inventory_raw` (`raw_id`, `name`, `unit_of_measure`, `quantity_on_hand`, `reorder_point`, `cost_per_unit`) VALUES
(1, 'Milk Powder (Full Cream)', 'kg', -0.160, 5.000, 0.00),
(2, 'Black Tea Leaves', 'kg', -0.010, 3.000, 0.00),
(3, 'Sugar Syrup Base', 'L', 0.000, 10.000, 0.00),
(4, 'Espresso Coffee Beans', 'kg', -0.242, 5.000, 0.00),
(5, 'Pearl Tapioca', 'kg', 0.000, 5.000, 0.00),
(6, 'Caramel Syrup Concentrate', 'L', 0.000, 2.000, 0.00),
(23, 'Full Cream Milk', 'L', 48.400, 10.000, 90.00),
(24, 'Chocolate Syrup', 'L', 10.000, 2.000, 250.00),
(25, 'Caramel Syrup', 'L', 10.000, 2.000, 250.00),
(26, 'Milk Tea Creamer', 'kg', 20.000, 5.000, 150.00),
(27, 'Fructose Syrup', 'L', 20.000, 5.000, 100.00),
(28, 'Tapioca Pearls (Raw)', 'kg', 14.950, 3.000, 120.00),
(29, 'Taro Powder', 'kg', 5.000, 1.000, 300.00),
(30, 'Matcha Powder', 'kg', 3.000, 0.500, 800.00),
(31, 'Soda Water', 'L', 28.500, 5.000, 40.00),
(32, 'Mango Syrup', 'L', 4.820, 1.000, 200.00),
(33, 'Green Apple Syrup', 'L', 5.000, 1.000, 200.00),
(34, 'Plastic Cup (16oz)', 'pcs', 484.000, 100.000, 2.50),
(35, 'Plastic Cup (22oz)', 'pcs', 500.000, 100.000, 3.00),
(36, 'Straws', 'pcs', 1000.000, 200.000, 0.50);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `slug`, `name`, `is_active`) VALUES
(1, 'kennel-main', 'Kennel Main', 1),
(2, 'kennel-north', 'Kennel North', 1),
(3, 'paws-place', 'Paws Place', 1),
(13, 'pup-stop', 'Pup Stop', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `temperature_type` enum('Hot Brew','Cold Brew','None') DEFAULT 'None'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`item_id`, `name`, `category_id`, `base_price`, `is_available`, `image_url`, `deleted_at`, `temperature_type`) VALUES
(1, 'Espresso', 1, 35.00, 1, NULL, NULL, 'Hot Brew'),
(2, 'Brewed', 1, 40.00, 1, NULL, NULL, 'Hot Brew'),
(3, 'Americano (Hot)', 1, 45.00, 1, NULL, NULL, 'Hot Brew'),
(4, 'Long Black', 1, 45.00, 1, NULL, NULL, 'Hot Brew'),
(5, 'Cappuccino (Hot)', 1, 50.00, 1, NULL, NULL, 'Hot Brew'),
(6, 'Latte (Hot)', 1, 55.00, 1, NULL, NULL, 'Hot Brew'),
(7, 'Mocha (Hot)', 1, 65.00, 1, NULL, NULL, 'Hot Brew'),
(8, 'Iced Americano', 2, 45.00, 1, NULL, NULL, 'Cold Brew'),
(9, 'Cold Brew', 2, 45.00, 1, NULL, NULL, 'Cold Brew'),
(10, 'Iced Latte', 2, 60.00, 1, NULL, NULL, 'Cold Brew'),
(11, 'Iced Cappuccino', 2, 60.00, 1, NULL, NULL, 'Cold Brew'),
(12, 'Iced Mocha', 2, 65.00, 1, NULL, NULL, 'Cold Brew'),
(13, 'Caramel Macchiato', 3, 55.00, 1, NULL, NULL, 'Hot Brew'),
(14, 'Spanish Latte', 3, 55.00, 1, NULL, NULL, 'Hot Brew'),
(15, 'Mocha Latte', 3, 65.00, 1, NULL, NULL, 'Hot Brew'),
(16, 'White Mocha', 3, 65.00, 1, NULL, NULL, 'Hot Brew'),
(17, 'Matcha Green Tea Latte', 3, 65.00, 1, NULL, NULL, 'Hot Brew'),
(18, 'Shaken Lemon Lychee', 3, 65.00, 1, NULL, NULL, 'Hot Brew'),
(19, 'Hot Chocolate', 3, 40.00, 1, NULL, NULL, 'Hot Brew'),
(20, 'Hot Milk', 3, 40.00, 1, NULL, NULL, 'Hot Brew'),
(21, 'Ice Choco', 3, 55.00, 1, NULL, NULL, 'Cold Brew'),
(22, 'Black Forest MT', 4, 55.00, 1, NULL, NULL, 'None'),
(23, 'Chocolate MT', 4, 55.00, 1, NULL, NULL, 'None'),
(24, 'Cookies and Cream MT', 4, 55.00, 1, NULL, NULL, 'None'),
(25, 'Dark Choco MT', 4, 55.00, 1, NULL, NULL, 'None'),
(26, 'Matcha MT', 4, 55.00, 1, NULL, NULL, 'None'),
(27, 'Red Velvet MT', 4, 55.00, 1, NULL, NULL, 'None'),
(28, 'Taro MT', 4, 55.00, 1, NULL, NULL, 'None'),
(29, 'Wintermelon MT', 4, 55.00, 1, NULL, NULL, 'None'),
(30, 'Hokkaido MT', 4, 55.00, 1, NULL, NULL, 'None'),
(31, 'Okinawa MT', 4, 55.00, 1, NULL, NULL, 'None'),
(32, 'Panda Pearl MT', 4, 55.00, 1, NULL, NULL, 'None'),
(33, 'Mango Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(34, 'Green Apple Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(35, 'Lychee Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(36, 'Strawberry Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(37, 'Passion Fruit Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(38, 'Melon Soda', 5, 60.00, 1, NULL, NULL, 'None'),
(39, 'Mango IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(40, 'Vanilla-Cashew IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(41, 'Tablia Native Cacao IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(42, 'Coconut IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(43, 'Matcha IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(44, 'Black Sesame IC (100g)', 7, 50.00, 1, NULL, NULL, 'None'),
(45, 'Coconut IC Bar (95g)', 8, 85.00, 1, NULL, NULL, 'None'),
(46, 'Matcha IC Bar (95g)', 8, 85.00, 1, NULL, NULL, 'None'),
(47, 'Milk-Cashew IC Bar (95g)', 8, 85.00, 1, NULL, NULL, 'None'),
(48, 'Tablia Native Cacao IC Bar (95g)', 8, 85.00, 1, NULL, NULL, 'None'),
(49, 'Cow Milk (350ml)', 9, 85.00, 1, NULL, NULL, 'None'),
(50, 'Water Buffalo Milk (350ml)', 9, 90.00, 1, NULL, NULL, 'None'),
(51, 'Chocolate (Cow) (350ml)', 9, 90.00, 1, NULL, NULL, 'None'),
(52, 'Chocolate (Water Buffalo) (350ml)', 9, 95.00, 1, NULL, NULL, 'None'),
(53, 'Matcha (Cow) (350ml)', 9, 105.00, 1, NULL, NULL, 'None'),
(54, 'Matcha (Water Buffalo) (350ml)', 9, 110.00, 1, NULL, NULL, 'None'),
(55, 'Mocha (Cow) (350ml)', 9, 105.00, 1, NULL, NULL, 'None'),
(56, 'Mocha (Water Buffalo) (350ml)', 9, 115.00, 1, NULL, NULL, 'None');

-- --------------------------------------------------------

--
-- Table structure for table `modifiers`
--

CREATE TABLE `modifiers` (
  `modifier_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_type` enum('Add','Option','Upgrade') NOT NULL,
  `price_add` decimal(10,2) DEFAULT 0.00,
  `applicable_category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modifiers`
--

INSERT INTO `modifiers` (`modifier_id`, `name`, `display_type`, `price_add`, `applicable_category_id`) VALUES
(1, 'Pearls', 'Add', 10.00, NULL),
(2, 'Coffee (Shot)', 'Add', 10.00, NULL),
(3, 'Milk (Extra)', 'Add', 10.00, NULL),
(4, 'Caramel Syrup', 'Add', 10.00, NULL),
(5, 'Coffee Jelly', 'Add', 10.00, NULL),
(6, 'Fruit Jelly', 'Add', 10.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modifier_inventory_links`
--

CREATE TABLE `modifier_inventory_links` (
  `link_id` int(11) NOT NULL,
  `modifier_id` int(11) NOT NULL,
  `raw_id` int(11) NOT NULL,
  `quantity_consumed` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modifier_inventory_links`
--

INSERT INTO `modifier_inventory_links` (`link_id`, `modifier_id`, `raw_id`, `quantity_consumed`) VALUES
(1, 1, 28, 0.050),
(2, 4, 25, 0.015),
(3, 3, 23, 0.050);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `pre_order_code` varchar(20) DEFAULT NULL,
  `final_code` varchar(20) DEFAULT NULL,
  `order_source` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('PENDING PAYMENT','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'PENDING PAYMENT',
  `cashier_id` int(11) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `time_placed` timestamp NOT NULL DEFAULT current_timestamp(),
  `time_paid` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `pre_order_code`, `final_code`, `order_source`, `total_amount`, `status`, `cashier_id`, `student_id`, `customer_name`, `shift_id`, `time_placed`, `time_paid`) VALUES
(1, 'PRE-001', 'OR-1001', 'Manual_POS', 115.00, 'SERVED', 2, NULL, NULL, 1, '2025-12-15 14:26:46', NULL),
(2, 'PRE-002', 'OR-1002', 'Kiosk', 65.00, '', 2, NULL, NULL, 1, '2025-12-15 14:26:46', '2026-02-10 06:00:18'),
(3, '383A', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '218102', NULL, NULL, '2026-02-10 05:26:54', NULL),
(4, '051Z', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20211537', NULL, NULL, '2026-02-10 05:38:51', NULL),
(5, '116S', NULL, 'Kiosk', 70.00, 'SERVED', NULL, '20211537', NULL, NULL, '2026-02-10 05:58:28', '2026-02-10 05:59:01'),
(6, '077F', NULL, 'Kiosk', 60.00, 'CANCELLED', NULL, '218102', NULL, NULL, '2026-02-10 05:59:33', '2026-02-10 06:00:08'),
(7, '662C', NULL, 'Kiosk', 40.00, 'SERVED', NULL, '20211537', NULL, NULL, '2026-02-10 06:01:31', '2026-02-10 06:02:05'),
(8, '736N', NULL, 'Kiosk', 275.00, 'CANCELLED', NULL, '20200173', NULL, NULL, '2026-02-10 07:21:49', NULL),
(9, '248Z', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20190412', NULL, NULL, '2026-02-10 07:34:12', NULL),
(10, '603P', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 07:52:32', NULL),
(11, '108Q', NULL, 'Kiosk', 65.00, 'SERVED', NULL, '225019', NULL, NULL, '2026-02-10 07:56:06', '2026-02-10 07:57:05'),
(12, '217S', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20190412', NULL, NULL, '2026-02-10 09:04:42', NULL),
(13, '688A', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20190412', NULL, NULL, '2026-02-10 09:05:04', NULL),
(14, '081O', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 10:49:43', NULL),
(15, '994Y', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 11:01:16', NULL),
(16, '608S', NULL, 'Kiosk', 60.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 11:09:17', NULL),
(17, '557T', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 11:10:09', NULL),
(18, '534X', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-10 11:10:22', NULL),
(19, '694R', NULL, 'Kiosk', 65.00, 'SERVED', NULL, '20152161', NULL, NULL, '2026-02-10 11:14:39', '2026-02-10 11:15:36'),
(20, '375L', NULL, 'Kiosk', 50.00, 'SERVED', NULL, 'GUEST', 'Cyfer Hogarth', NULL, '2026-02-10 11:37:45', '2026-02-10 12:58:56'),
(21, '819G', NULL, 'Kiosk', 55.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-10 11:40:38', '2026-02-11 03:01:43'),
(22, '925I', NULL, 'Kiosk', 45.00, 'SERVED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-10 12:53:47', '2026-02-11 02:10:19'),
(23, '779O', NULL, 'Kiosk', 85.00, 'CANCELLED', NULL, '20180724', 'DEAN LOUIE RAMIREZ ARAULA', NULL, '2026-02-11 01:41:02', NULL),
(24, '699C', NULL, 'Kiosk', 60.00, 'SERVED', NULL, '20180724', 'DEAN LOUIE RAMIREZ ARAULA', NULL, '2026-02-11 01:41:12', '2026-02-11 01:41:59'),
(25, '062V', NULL, 'Kiosk', 60.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-11 02:50:47', NULL),
(26, '904R', NULL, 'Kiosk', 60.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-11 03:00:04', NULL),
(27, '698L', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, 'GUEST', 'tristan', NULL, '2026-02-11 03:31:16', NULL),
(28, '905M', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20211537', NULL, NULL, '2026-02-11 05:52:24', NULL),
(29, '523T', NULL, 'Kiosk', 95.00, 'CANCELLED', NULL, '20211537', NULL, NULL, '2026-02-11 05:53:11', NULL),
(30, '289I', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20190412', NULL, NULL, '2026-02-11 06:09:13', NULL),
(31, '029X', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20190412', NULL, NULL, '2026-02-11 07:22:56', NULL),
(32, '078A', NULL, 'Kiosk', 55.00, 'SERVED', NULL, '20190412', NULL, NULL, '2026-02-11 07:23:08', '2026-02-11 07:24:15'),
(33, '826L', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20211537', NULL, NULL, '2026-02-11 09:18:32', NULL),
(34, '165V', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-11 09:26:34', NULL),
(35, '560C', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20211537', NULL, NULL, '2026-02-11 09:36:59', NULL),
(36, '116T', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, 'GUEST', NULL, NULL, '2026-02-11 10:24:00', NULL),
(37, '818C', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20220120', NULL, NULL, '2026-02-11 10:32:57', NULL),
(38, '148P', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 10:41:05', NULL),
(39, '610I', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 10:41:48', NULL),
(40, '391P', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 10:52:34', NULL),
(41, '969B', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:53:25', NULL),
(42, '564M', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:55:32', NULL),
(43, '485Y', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 10:56:14', '2026-02-11 11:01:01'),
(44, '655B', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:32', NULL),
(45, '455C', NULL, 'Kiosk', 55.00, 'SERVED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 10:58:34', '2026-02-11 11:00:45'),
(46, '295O', NULL, 'Kiosk', 100.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:40', NULL),
(47, '124D', NULL, 'Kiosk', 45.00, 'SERVED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:42', '2026-02-11 11:00:35'),
(48, '747V', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:47', NULL),
(49, '541L', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:50', NULL),
(50, '907L', NULL, 'Kiosk', 85.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:58', NULL),
(51, '145V', NULL, 'Kiosk', 55.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:01:53', NULL),
(52, '461W', NULL, 'Kiosk', 90.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:05:34', NULL),
(53, '626J', NULL, 'Kiosk', 4600.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:35', NULL),
(54, '375A', NULL, 'Kiosk', 240.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:43', NULL),
(55, '108A', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:46', '2026-02-16 07:49:08'),
(56, '777I', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:48', '2026-02-16 07:48:58'),
(57, '227F', NULL, 'Kiosk', 270.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:52', NULL),
(58, '091W', NULL, 'Kiosk', 45.00, 'SERVED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:56', '2026-02-11 11:18:18'),
(59, '693U', NULL, 'Kiosk', 45.00, 'SERVED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:56', '2026-02-11 11:26:46'),
(60, '343T', NULL, 'Kiosk', 1050.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:09:34', NULL),
(61, '699S', NULL, 'Kiosk', 1560.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:26:44', NULL),
(62, '331K', NULL, 'Kiosk', 7500.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:33:46', NULL),
(63, '322M', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 02:17:44', '2026-02-13 02:18:14'),
(64, '614G', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 04:51:00', NULL),
(65, '354A', NULL, 'Kiosk', 105.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 07:19:40', NULL),
(66, '206D', NULL, 'Kiosk', 135.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-18 05:37:10', '2026-02-20 00:47:40'),
(67, '031L', NULL, 'Kiosk', 60.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-18 06:50:26', '2026-02-18 06:51:42'),
(68, '090B', NULL, 'Kiosk', 330.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-20 01:51:57', NULL),
(69, '063Y', NULL, 'Kiosk', 330.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-20 01:51:57', NULL),
(70, '236I', NULL, 'Kiosk', 215.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-20 02:08:32', NULL),
(71, '499B', NULL, 'Kiosk', 130.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-20 02:29:21', '2026-02-20 02:54:37'),
(72, '198D', NULL, 'Kiosk', 2290.00, 'CANCELLED', NULL, '20200173', 'FELIX CONSTANTINO JR. PIS-AN CATA-AL', NULL, '2026-02-24 06:32:22', NULL),
(73, '090G', NULL, 'Kiosk', 450.00, 'CANCELLED', NULL, '20230182', 'MARK JOSEPH MIRO FERNANDEZ', NULL, '2026-02-24 09:25:52', NULL),
(74, '883I', NULL, 'Kiosk', 50.00, 'CANCELLED', NULL, '20211670', 'RAINER NULLA DIZON', NULL, '2026-02-24 10:49:45', NULL),
(75, '934J', NULL, 'Kiosk', 3420.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-24 11:06:42', NULL),
(76, '494E', NULL, 'Kiosk', 230.00, 'SERVED', NULL, '20220359', 'LEILAH JANE BALANSAG OSTRIA', NULL, '2026-02-24 11:37:23', '2026-02-24 11:55:54'),
(77, '391L', NULL, 'Kiosk', 10000.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-24 11:58:37', NULL),
(78, '823H', NULL, 'Kiosk', 650.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-24 12:27:03', NULL),
(79, '588Z', NULL, 'Kiosk', 280.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-25 00:36:21', NULL),
(80, '954W', NULL, 'Kiosk', 550.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 01:23:35', NULL),
(81, '746D', NULL, 'Kiosk', 110.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 01:30:07', '2026-02-25 01:31:13'),
(82, '382N', NULL, 'Kiosk', 110.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 01:49:16', '2026-02-25 01:50:07'),
(83, '180A', NULL, 'Kiosk', 95.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 01:55:06', NULL),
(84, '119E', NULL, 'Kiosk', 165.00, 'SERVED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-25 04:55:50', '2026-02-25 04:56:28'),
(85, '250F', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-25 04:59:19', '2026-02-25 05:58:11'),
(86, '887A', NULL, 'Kiosk', 600.00, 'SERVED', NULL, '20200173', 'FELIX CONSTANTINO JR. PIS-AN CATA-AL', NULL, '2026-02-25 05:16:17', '2026-02-25 06:13:30'),
(87, '106N', NULL, 'Kiosk', 75.00, 'CANCELLED', NULL, '20200173', 'FELIX CONSTANTINO JR. PIS-AN CATA-AL', NULL, '2026-02-25 05:16:48', NULL),
(88, '769E', NULL, 'Kiosk', 300.00, 'SERVED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-25 05:52:24', '2026-02-25 05:54:21'),
(89, '127S', NULL, 'Kiosk', 35.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 05:57:13', '2026-02-25 05:57:58'),
(90, '706G', NULL, 'Kiosk', 550.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 06:19:06', NULL),
(91, '839M', NULL, 'Kiosk', 65.00, 'SERVED', NULL, '20130685', 'ANDREW VILLALON MORES', NULL, '2026-02-25 06:21:41', '2026-02-25 06:22:10'),
(92, '949I', NULL, 'Kiosk', 600.00, 'CANCELLED', NULL, '20200173', 'FELIX CONSTANTINO JR. PIS-AN CATA-AL', NULL, '2026-02-25 06:28:32', NULL),
(93, '397Z', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-25 06:47:12', NULL),
(94, '255E', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, 'GUEST-1772136579752', 'Guest Customer', NULL, '2026-02-26 20:09:54', '2026-02-26 20:51:21'),
(95, '467Q', NULL, 'Kiosk', 105.00, 'CANCELLED', NULL, 'GUEST-1772136579752', 'Guest Customer', NULL, '2026-02-26 20:12:01', NULL),
(96, '322F', NULL, '', 250.00, 'SERVED', NULL, 'GUEST-1772137228641', 'Guest Customer', NULL, '2026-02-26 20:25:01', '2026-02-26 20:46:42'),
(97, '042G', NULL, '', 60.00, 'SERVED', NULL, 'GUEST-1772137228641', 'Guest Customer', NULL, '2026-02-26 20:26:03', '2026-02-26 20:37:57'),
(98, '119O', NULL, '', 40.00, 'SERVED', NULL, 'GUEST-1772138962465', 'Guest Customer', NULL, '2026-02-26 20:49:39', '2026-02-26 20:49:53'),
(99, '928Q', NULL, '', 50.00, 'SERVED', NULL, 'GUEST-1772138962465', 'Guest Customer', NULL, '2026-02-26 20:53:24', '2026-02-26 20:53:42'),
(100, '830E', NULL, '', 40.00, 'CANCELLED', NULL, 'GUEST-1772140861450', 'Guest Customer', NULL, '2026-02-26 21:21:21', NULL),
(101, '884Q', NULL, '', 115.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 02:55:09', NULL),
(102, '335O', NULL, '', 65.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 02:55:52', '2026-02-27 02:56:04'),
(103, '093I', NULL, '', 40.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 02:57:14', '2026-02-27 02:57:27'),
(106, '575E', NULL, '', 60.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 02:58:46', '2026-02-27 02:59:37'),
(108, '372Z', NULL, 'Paws Place', 125.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 03:50:29', '2026-03-02 21:30:16'),
(109, '977I', NULL, 'Paws Place', 50.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 06:11:50', '2026-02-27 06:12:28'),
(110, '226R', NULL, 'Kennel North', 10.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 06:12:19', NULL),
(111, '605N', NULL, 'Kennel North', 10.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 06:13:16', '2026-02-27 06:16:40'),
(112, '132N', NULL, 'Kennel North', 226.00, 'READY', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 06:26:23', '2026-02-27 06:47:26'),
(113, '203W', NULL, 'Paws Place', 125.00, 'SERVED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 06:26:44', '2026-02-27 06:31:49'),
(114, '897X', NULL, 'Pup Stop', 266.00, 'CANCELLED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 06:35:52', '2026-02-27 06:54:11'),
(115, '778E', NULL, 'Kennel Main', 1382.00, 'CANCELLED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 06:39:00', NULL),
(116, '803X', NULL, 'Kennel North', 2723.00, 'CANCELLED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 06:46:31', NULL),
(117, '139J', NULL, 'Pup Stop', 25.00, 'CANCELLED', NULL, NULL, 'Test User', NULL, '2026-02-27 07:06:44', NULL),
(118, '976V', NULL, 'Paws Place', 50.00, 'SERVED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-27 07:07:25', '2026-02-27 07:07:40'),
(119, '669T', NULL, 'Paws Place', 2250.00, 'CANCELLED', NULL, '20212157', 'EDRIAN SOMOZA SANTAYO', NULL, '2026-02-27 07:10:10', NULL),
(120, '801U', NULL, 'Paws Place', 4560.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-27 07:12:17', NULL),
(121, '217R', NULL, 'Pup Stop', 130.00, 'PENDING PAYMENT', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-27 07:26:48', NULL),
(122, '612P', NULL, 'Paws Place', 115.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 08:41:37', '2026-03-03 00:40:20'),
(123, '924M', NULL, 'Kennel Main', 208.00, 'READY', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-27 09:03:25', '2026-03-03 01:51:08'),
(124, '103M', NULL, 'Kennel Main', 125.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 02:13:50', NULL),
(125, '689P', NULL, 'Paws Place', 360.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 02:14:46', NULL),
(126, '799D', NULL, 'Paws Place', 55.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:13:52', NULL),
(127, '157H', NULL, 'Pup Stop', 20.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:14:04', NULL),
(128, '340Z', NULL, 'Kennel Main', 5.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:14:56', NULL),
(129, '907C', NULL, 'Kennel North', 50.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:15:08', NULL),
(130, '663O', NULL, 'Pup Stop', 208.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:15:55', NULL),
(131, '056G', NULL, 'Pup Stop', 208.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:16:04', NULL),
(132, '296O', NULL, 'Pup Stop', 208.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:18:33', NULL),
(133, '692Q', NULL, 'Paws Place', 60.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:18:41', NULL),
(134, '752J', NULL, 'Kennel Main', 10.00, 'PREPARING', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:18:51', '2026-03-03 01:51:22'),
(135, '014T', NULL, 'Paws Place', 50.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:21:58', NULL),
(136, '686C', NULL, 'Kennel North', 17.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-28 03:26:33', NULL),
(137, '761D', NULL, 'Paws Place', 50.00, 'CANCELLED', 5, NULL, 'Kennel North Cashier', NULL, '2026-02-28 03:28:40', NULL),
(138, '234U', NULL, 'Paws Place', 50.00, 'CANCELLED', 5, NULL, 'Kennel North Cashier', NULL, '2026-02-28 03:28:48', NULL),
(139, '886F', NULL, 'Paws Place', 125.00, 'CANCELLED', 5, NULL, 'Kennel North Cashier', NULL, '2026-02-28 03:30:36', NULL),
(140, '039A', NULL, 'Pup Stop', 7.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-02 02:09:56', NULL),
(144, '573P', NULL, 'Paws Place', 85.00, 'CANCELLED', NULL, 'GUEST', 'Guest', NULL, '2026-03-02 21:53:02', NULL),
(145, '823W', NULL, 'Paws Place', 100.00, 'CANCELLED', NULL, 'GUEST', 'Guest', NULL, '2026-03-02 21:53:07', NULL),
(146, '628U', NULL, 'Paws Place', 85.00, 'CANCELLED', NULL, 'GUEST', 'Guest', NULL, '2026-03-02 21:56:43', NULL),
(147, '169A', NULL, 'Paws Place', 40.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 00:56:35', '2026-03-03 00:57:26'),
(148, '062X', NULL, 'Kennel Main', 5.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 00:56:50', NULL),
(149, '897J', NULL, 'Kennel Main', 10.00, 'PREPARING', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 00:57:09', '2026-03-03 01:52:31'),
(150, '885W', NULL, 'Paws Place', 60.00, 'CANCELLED', NULL, '20220359', 'LEILAH JANE BALANSAG OSTRIA', NULL, '2026-03-03 01:27:08', NULL),
(151, '927M', NULL, 'Paws Place', 65.00, 'CANCELLED', NULL, '20220359', 'LEILAH JANE BALANSAG OSTRIA', NULL, '2026-03-03 01:28:30', NULL),
(152, '920V', NULL, 'Paws Place', 880.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-03-03 01:54:05', NULL),
(153, '703B', NULL, 'Kennel Main', 70000000.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-03-03 01:55:01', NULL),
(154, '599H', NULL, 'Paws Place', 55.00, 'SERVED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 05:10:34', '2026-03-03 05:11:06'),
(155, '224K', NULL, 'Paws Place', 60.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 06:54:21', NULL),
(156, '898I', NULL, 'Paws Place', 60.00, 'CANCELLED', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-03 07:06:56', NULL),
(157, '628H', NULL, 'Paws Place', 105.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-03-18 03:36:54', NULL),
(158, '118K', NULL, 'Paws Place', 275.00, 'PENDING PAYMENT', NULL, 'GUEST', 'Guest', NULL, '2026-03-19 06:10:52', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `external_item_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL,
  `modifiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modifiers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `menu_item_id`, `external_item_name`, `quantity`, `price_at_sale`, `modifiers`) VALUES
(1, 1, 6, NULL, 1, 55.00, NULL),
(2, 1, 33, NULL, 1, 60.00, NULL),
(3, 2, 32, NULL, 1, 65.00, '{\"Pearls\": \"Extra\"}'),
(4, 3, 4, NULL, 1, 45.00, '[]'),
(5, 4, 5, NULL, 1, 50.00, '[]'),
(6, 5, 11, NULL, 1, 70.00, '[\"Coffee Jelly\"]'),
(7, 6, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(8, 7, 2, NULL, 1, 40.00, '[]'),
(9, 8, 23, NULL, 1, 55.00, '[]'),
(10, 8, 24, NULL, 1, 55.00, '[]'),
(11, 8, 32, NULL, 1, 55.00, '[]'),
(12, 8, 25, NULL, 1, 55.00, '[]'),
(13, 8, 30, NULL, 1, 55.00, '[]'),
(14, 9, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(15, 10, 14, NULL, 1, 55.00, '[]'),
(16, 11, 15, NULL, 1, 65.00, '[]'),
(17, 12, 4, NULL, 1, 55.00, '[\"Caramel Syrup\"]'),
(18, 13, 2, NULL, 1, 50.00, '[\"Caramel Syrup\"]'),
(19, 14, 4, NULL, 1, 45.00, '[]'),
(20, 15, 5, NULL, 1, 50.00, '[]'),
(21, 16, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(22, 17, 5, NULL, 1, 50.00, '[]'),
(23, 18, 4, NULL, 1, 45.00, '[]'),
(24, 19, 6, NULL, 1, 65.00, '[\"Milk\"]'),
(25, 20, 5, NULL, 1, 50.00, '[]'),
(26, 21, 30, NULL, 1, 55.00, '[]'),
(27, 22, 1, NULL, 1, 45.00, '[\"Caramel Syrup\"]'),
(28, 23, 46, NULL, 1, 85.00, '[]'),
(29, 24, 2, NULL, 1, 60.00, '[\"Milk\",\"Caramel Syrup\"]'),
(30, 25, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(31, 26, 5, NULL, 1, 60.00, '[\"Milk\"]'),
(32, 27, 2, NULL, 1, 40.00, '[]'),
(33, 28, 39, NULL, 1, 50.00, '[]'),
(34, 29, 52, NULL, 1, 95.00, '[]'),
(35, 30, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(36, 31, 5, NULL, 1, 50.00, '[]'),
(37, 32, 9, NULL, 1, 55.00, '[\"Milk\"]'),
(38, 33, 8, NULL, 1, 55.00, '[\"Milk\"]'),
(39, 34, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(40, 35, 2, NULL, 1, 40.00, '[]'),
(41, 36, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(42, 37, 3, NULL, 1, 55.00, '[\"Caramel Syrup\"]'),
(43, 38, 42, NULL, 1, 50.00, '[]'),
(44, 39, 19, NULL, 1, 40.00, '[]'),
(45, 40, 13, NULL, 1, 55.00, '[]'),
(46, 41, 2, NULL, 1, 40.00, '[]'),
(47, 42, 3, NULL, 1, 45.00, '[]'),
(48, 43, 14, NULL, 1, 55.00, '[]'),
(49, 44, 5, NULL, 1, 50.00, '[]'),
(50, 45, 3, NULL, 1, 55.00, '[\"Caramel Syrup\"]'),
(51, 46, 6, NULL, 1, 55.00, '[]'),
(52, 46, 4, NULL, 1, 45.00, '[]'),
(53, 47, 4, NULL, 1, 45.00, '[]'),
(54, 48, 4, NULL, 1, 45.00, '[]'),
(55, 49, 2, NULL, 1, 40.00, '[]'),
(56, 50, 46, NULL, 1, 85.00, '[]'),
(57, 51, 6, NULL, 1, 55.00, '[]'),
(58, 52, 3, NULL, 1, 45.00, '[]'),
(59, 52, 4, NULL, 1, 45.00, '[]'),
(60, 53, 56, NULL, 40, 115.00, '[]'),
(61, 54, 5, NULL, 3, 50.00, '[]'),
(62, 54, 4, NULL, 2, 45.00, '[]'),
(63, 55, 5, NULL, 1, 50.00, '[]'),
(64, 56, 4, NULL, 1, 45.00, '[]'),
(65, 57, 4, NULL, 6, 45.00, '[]'),
(66, 58, 4, NULL, 1, 45.00, '[]'),
(67, 59, 4, NULL, 1, 45.00, '[]'),
(68, 60, 5, NULL, 21, 50.00, '[]'),
(69, 61, 26, NULL, 24, 65.00, '[\"Pearls\"]'),
(70, 62, 7, NULL, 100, 75.00, '[\"Caramel Syrup\"]'),
(71, 63, 5, NULL, 1, 50.00, '[]'),
(72, 64, 2, NULL, 1, 40.00, '[]'),
(73, 65, 19, NULL, 1, 40.00, '[]'),
(74, 65, 18, NULL, 1, 65.00, '[]'),
(75, 66, 2, NULL, 1, 50.00, '[\"Caramel Syrup\"]'),
(76, 66, 46, NULL, 1, 85.00, '[]'),
(77, 67, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(78, 68, 3, NULL, 6, 55.00, '[\"Caramel Syrup\"]'),
(79, 69, 3, NULL, 6, 55.00, '[\"Caramel Syrup\"]'),
(80, 70, 6, NULL, 2, 75.00, '[\"Caramel Syrup\",\"Milk\"]'),
(81, 70, 4, NULL, 1, 65.00, '[\"Milk\",\"Caramel Syrup\"]'),
(82, 71, 5, NULL, 1, 70.00, '[\"Milk\",\"Caramel Syrup\"]'),
(83, 71, 2, NULL, 1, 60.00, '[\"Milk\",\"Caramel Syrup\"]'),
(84, 72, 19, NULL, 31, 40.00, '[]'),
(85, 72, 20, NULL, 10, 40.00, '[]'),
(86, 72, 15, NULL, 10, 65.00, '[]'),
(87, 73, 33, NULL, 5, 60.00, '[]'),
(88, 73, 41, NULL, 3, 50.00, '[]'),
(89, 74, 40, NULL, 1, 50.00, '[]'),
(90, 75, 5, NULL, 57, 60.00, '[\"Caramel Syrup\"]'),
(91, 76, 16, NULL, 1, 65.00, '[]'),
(92, 76, 36, NULL, 1, 60.00, '[]'),
(93, 76, 55, NULL, 1, 105.00, '[]'),
(94, 77, 2, NULL, 200, 50.00, '[\"Caramel Syrup\"]'),
(95, 78, 18, NULL, 10, 65.00, '[]'),
(96, 79, 10, NULL, 4, 70.00, '[\"Milk\"]'),
(97, 80, 3, NULL, 10, 55.00, '[\"Milk\"]'),
(98, 81, 3, NULL, 2, 55.00, '[\"Milk\"]'),
(99, 82, 3, NULL, 2, 55.00, '[\"Milk\"]'),
(100, 83, 52, NULL, 1, 95.00, '[]'),
(101, 84, 3, NULL, 3, 55.00, '[\"Milk\"]'),
(102, 85, 2, NULL, 1, 40.00, '[]'),
(103, 86, 44, NULL, 12, 50.00, '[]'),
(104, 87, 30, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(105, 88, 13, NULL, 1, 55.00, '[]'),
(106, 88, 6, NULL, 1, 75.00, '[\"Milk\",\"Caramel Syrup\"]'),
(107, 88, 12, NULL, 1, 85.00, '[\"Milk\",\"Coffee Jelly\"]'),
(108, 88, 49, NULL, 1, 85.00, '[]'),
(109, 89, 1, NULL, 1, 35.00, '[]'),
(110, 90, 3, NULL, 10, 55.00, '[\"Milk\"]'),
(111, 91, 3, NULL, 1, 65.00, '[\"Milk\",\"Caramel Syrup\"]'),
(112, 92, 44, NULL, 12, 50.00, '[]'),
(113, 93, 3, NULL, 1, 45.00, '[]'),
(114, 94, 3, NULL, 1, 45.00, '[]'),
(115, 95, 2, NULL, 1, 60.00, '[\"Milk\",\"Caramel Syrup\"]'),
(116, 95, 4, NULL, 1, 45.00, '[]'),
(117, 96, 5, NULL, 5, 50.00, '[]'),
(118, 97, 35, NULL, 1, 60.00, '[]'),
(119, 98, 2, NULL, 1, 40.00, '[]'),
(120, 99, 5, NULL, 1, 50.00, '[]'),
(121, 100, 2, NULL, 1, 40.00, '[]'),
(122, 101, 3, NULL, 1, 55.00, '[\"Caramel Syrup\"]'),
(123, 101, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(124, 102, 23, NULL, 1, 65.00, '[\"Pearls\"]'),
(125, 103, 2, NULL, 1, 40.00, '[]'),
(128, 106, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(130, 108, 5, NULL, 1, 70.00, '[\"Milk\",\"Caramel Syrup\"]'),
(131, 108, 4, NULL, 1, 55.00, '[\"Caramel Syrup\"]'),
(132, 109, 2, NULL, 1, 50.00, '[\"Caramel Syrup\"]'),
(133, 110, NULL, 'Bread Pie', 1, 10.00, '[]'),
(134, 111, NULL, 'Bread @ 10.00', 1, 10.00, '[]'),
(135, 112, NULL, 'Bread @ 10.00', 1, 10.00, '[]'),
(136, 112, NULL, 'Bread Pie', 1, 10.00, '[]'),
(137, 112, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(138, 112, NULL, 'Bread @ 5.00', 1, 5.00, '[]'),
(139, 113, 3, NULL, 1, 65.00, '[\"Milk\",\"Caramel Syrup\"]'),
(140, 113, 2, NULL, 1, 60.00, '[\"Milk\",\"Caramel Syrup\"]'),
(141, 114, NULL, 'Bread @ 10.00', 1, 10.00, '[]'),
(142, 114, NULL, 'Bread @ 5.00', 1, 5.00, '[]'),
(143, 114, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(144, 114, NULL, 'Bread Pie', 2, 10.00, '[]'),
(145, 114, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(146, 114, NULL, 'Bread @7 (2)', 1, 7.00, '[]'),
(147, 114, NULL, 'Toasted Bread', 1, 5.00, '[]'),
(148, 114, NULL, 'Cheese Bread ', 1, 7.00, '[]'),
(149, 114, NULL, 'Assorted Candy', 1, 1.00, '[]'),
(150, 114, NULL, 'Candy', 1, 1.00, '[]'),
(151, 114, NULL, 'Fres', 1, 1.00, '[]'),
(152, 114, NULL, 'Max,xo,dynamite', 1, 1.00, '[]'),
(153, 115, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(154, 115, NULL, '3 N 1 750 Ml', 1, 100.00, '[]'),
(155, 115, NULL, '3 + 1-1.3 Ml', 1, 150.00, '[]'),
(156, 115, NULL, '3n1 750ml', 1, 100.00, '[]'),
(157, 115, NULL, 'Chocky Stick', 1, 15.00, '[]'),
(158, 115, NULL, 'Boom Boom Max', 1, 25.00, '[]'),
(159, 115, NULL, 'Boom Boom ', 1, 20.00, '[]'),
(160, 115, NULL, 'Cookies  And Cream 750 Ml', 1, 150.00, '[]'),
(161, 115, NULL, 'Corneto ', 1, 30.00, '[]'),
(162, 115, NULL, 'Creadaestick Choco Mallow', 1, 15.00, '[]'),
(163, 115, NULL, 'Ice Cream Cup', 1, 25.00, '[]'),
(164, 115, NULL, 'Ice Cream 3 N 1', 1, 100.00, '[]'),
(165, 115, NULL, 'Ice Cream @ 15.00', 1, 15.00, '[]'),
(166, 115, NULL, 'Paddle Pop Spider', 1, 25.00, '[]'),
(167, 115, NULL, 'Ice Cream Soft Serve', 1, 20.00, '[]'),
(168, 115, NULL, 'Paddlepop Ube', 1, 20.00, '[]'),
(169, 115, NULL, 'Watermelon Stick', 1, 15.00, '[]'),
(170, 115, NULL, 'Waffle Time ', 1, 20.00, '[]'),
(171, 115, NULL, 'Soft Serve Ice Cream @ 20.00', 1, 25.00, '[]'),
(172, 115, NULL, 'Fruity Soda', 1, 60.00, '[]'),
(173, 115, NULL, 'Espresso ', 1, 35.00, '[]'),
(174, 115, NULL, 'Add Ons 2', 1, 10.00, '[]'),
(175, 115, NULL, 'Hot Americano', 1, 45.00, '[]'),
(176, 115, NULL, 'Hot Brewed Coffee', 1, 40.00, '[]'),
(177, 115, NULL, 'Hot Cappuccino', 1, 50.00, '[]'),
(178, 115, NULL, 'Hot Caramel Macchiato 2', 1, 55.00, '[]'),
(179, 115, NULL, 'Hot Caramel Latte', 1, 50.00, '[]'),
(180, 115, NULL, 'Hot Latte', 1, 50.00, '[]'),
(181, 115, NULL, 'Hot Long Black', 1, 45.00, '[]'),
(182, 115, NULL, 'Hot Matcha Green Tea Latte', 1, 65.00, '[]'),
(183, 116, NULL, 'Bread @ 5.00', 4, 5.00, '[]'),
(184, 116, NULL, 'Bread @ 10.00', 1, 10.00, '[]'),
(185, 116, NULL, 'Bread Pie', 3, 10.00, '[]'),
(186, 116, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(187, 116, NULL, 'Cater Hangouts', 4, 201.00, '[]'),
(188, 116, NULL, 'Bread @7 (2)', 1, 7.00, '[]'),
(189, 116, NULL, 'Fruity Soda', 2, 60.00, '[]'),
(190, 116, NULL, 'Espresso ', 3, 35.00, '[]'),
(191, 116, NULL, 'Hot Cappuccino', 1, 50.00, '[]'),
(192, 116, NULL, 'Hot Brewed Coffee', 2, 40.00, '[]'),
(193, 116, NULL, 'Hot Americano', 1, 45.00, '[]'),
(194, 116, NULL, 'Add Ons 2', 1, 10.00, '[]'),
(195, 116, NULL, 'Hot Choco 2', 1, 40.00, '[]'),
(196, 116, NULL, 'Hot Caramel Macchiato 2', 2, 55.00, '[]'),
(197, 116, NULL, 'Hot Long Black', 3, 45.00, '[]'),
(198, 116, NULL, 'Hot Matcha Green Tea Latte', 2, 65.00, '[]'),
(199, 116, NULL, 'Hot Latte', 2, 50.00, '[]'),
(200, 116, NULL, 'Hot Mocha', 1, 55.00, '[]'),
(201, 116, NULL, 'Hot Spanish Latte 2', 2, 55.00, '[]'),
(202, 116, NULL, 'Ice Cubes', 1, 5.00, '[]'),
(203, 116, NULL, 'Ice Choco 2', 1, 55.00, '[]'),
(204, 116, NULL, 'Milktea', 2, 55.00, '[]'),
(205, 116, NULL, 'Iced White Mocha ', 1, 65.00, '[]'),
(206, 116, NULL, 'Iced Shaken Lemon Lychee', 1, 65.00, '[]'),
(207, 116, NULL, 'Iced Spanish Latte', 1, 55.00, '[]'),
(208, 116, NULL, 'Fruity Soda', 2, 60.00, '[]'),
(209, 116, NULL, 'Coffee Latte', 2, 40.00, '[]'),
(210, 116, NULL, 'Hot Espresso', 1, 35.00, '[]'),
(211, 116, NULL, 'Hot Choco', 1, 45.00, '[]'),
(212, 116, NULL, 'Coffee Americano', 3, 40.00, '[]'),
(213, 117, NULL, 'Hotdog', 1, 25.00, '[]'),
(214, 118, 2, NULL, 1, 50.00, '[\"Milk\"]'),
(215, 119, 5, NULL, 1, 70.00, '[\"Milk\",\"Caramel Syrup\"]'),
(216, 119, 6, NULL, 1, 75.00, '[\"Milk\",\"Caramel Syrup\"]'),
(217, 119, 4, NULL, 1, 65.00, '[\"Milk\",\"Caramel Syrup\"]'),
(218, 119, 1, NULL, 1, 55.00, '[\"Milk\",\"Caramel Syrup\"]'),
(219, 119, 3, NULL, 1, 65.00, '[\"Milk\",\"Caramel Syrup\"]'),
(220, 119, 7, NULL, 1, 85.00, '[\"Milk\",\"Caramel Syrup\"]'),
(221, 119, 42, NULL, 1, 50.00, '[]'),
(222, 119, 39, NULL, 1, 50.00, '[]'),
(223, 119, 30, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(224, 119, 24, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(225, 119, 27, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(226, 119, 25, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(227, 119, 52, NULL, 1, 95.00, '[]'),
(228, 119, 49, NULL, 1, 85.00, '[]'),
(229, 119, 54, NULL, 1, 110.00, '[]'),
(230, 119, 55, NULL, 1, 105.00, '[]'),
(231, 119, 50, NULL, 1, 90.00, '[]'),
(232, 119, 56, NULL, 1, 115.00, '[]'),
(233, 119, 53, NULL, 1, 105.00, '[]'),
(234, 119, 51, NULL, 1, 90.00, '[]'),
(235, 119, 34, NULL, 1, 60.00, '[]'),
(236, 119, 35, NULL, 1, 60.00, '[]'),
(237, 119, 33, NULL, 1, 60.00, '[]'),
(238, 119, 36, NULL, 1, 60.00, '[]'),
(239, 119, 37, NULL, 1, 60.00, '[]'),
(240, 119, 38, NULL, 1, 60.00, '[]'),
(241, 119, 23, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(242, 119, 22, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(243, 119, 26, NULL, 1, 75.00, '[\"Pearls\",\"Coffee Jelly\"]'),
(244, 119, 21, NULL, 1, 55.00, '[]'),
(245, 120, 5, NULL, 76, 60.00, '[\"Milk\"]'),
(246, 121, NULL, 'Bread @ 5.00', 26, 5.00, '[]'),
(247, 122, 22, NULL, 1, 65.00, '[\"Coffee Jelly\"]'),
(248, 122, 42, NULL, 1, 50.00, '[]'),
(249, 123, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(250, 123, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(251, 124, NULL, 'Boom Boom Max', 5, 25.00, '[]'),
(252, 125, 35, NULL, 6, 60.00, '[]'),
(253, 126, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(254, 127, NULL, 'Bread Pie', 2, 10.00, '[]'),
(255, 128, NULL, 'Bread @ 5.00', 1, 5.00, '[]'),
(256, 129, NULL, 'Soft Serve Ice Cream @ 20.00', 1, 25.00, '[]'),
(257, 129, NULL, 'Ice Cream Cup', 1, 25.00, '[]'),
(258, 130, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(259, 130, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(260, 131, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(261, 131, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(262, 132, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(263, 132, NULL, 'Cater Hangouts', 1, 201.00, '[]'),
(264, 133, 5, NULL, 1, 60.00, '[\"Milk\"]'),
(265, 134, NULL, 'Bread Pie', 1, 10.00, '[]'),
(266, 135, 5, NULL, 1, 50.00, '[]'),
(267, 136, NULL, 'Bread @ 10.00', 1, 10.00, '[]'),
(268, 136, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(269, 137, 5, NULL, 1, 50.00, '[]'),
(270, 138, 5, NULL, 1, 50.00, '[]'),
(271, 139, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(272, 139, 6, NULL, 1, 65.00, '[\"Milk\"]'),
(273, 140, NULL, 'Bread @ 7.00', 1, 7.00, '[]'),
(276, 144, 2, NULL, 1, 40.00, '[]'),
(277, 144, 4, NULL, 1, 45.00, '[]'),
(278, 145, 39, NULL, 1, 50.00, '[]'),
(279, 145, 41, NULL, 1, 50.00, '[]'),
(280, 146, 3, NULL, 1, 45.00, '[]'),
(281, 146, 2, NULL, 1, 40.00, '[]'),
(282, 147, 2, NULL, 1, 40.00, '[]'),
(283, 148, NULL, 'Bread @ 5.00', 1, 5.00, '[]'),
(284, 149, NULL, 'Bread Pie', 1, 10.00, '[]'),
(285, 150, 34, NULL, 1, 60.00, '[]'),
(286, 151, 3, NULL, 1, 65.00, '[\"Caramel Syrup\",\"Milk\"]'),
(287, 152, 3, NULL, 16, 55.00, '[\"Milk\"]'),
(288, 153, NULL, 'Cheese Bread ', 10000000, 7.00, '[]'),
(289, 154, 3, NULL, 1, 55.00, '[\"Milk\"]'),
(290, 155, 35, NULL, 1, 60.00, '[]'),
(291, 156, 36, NULL, 1, 60.00, '[]'),
(292, 157, 5, NULL, 1, 60.00, '[\"Caramel Syrup\"]'),
(293, 157, 4, NULL, 1, 45.00, '[]'),
(294, 158, 3, NULL, 5, 55.00, '[\"Caramel Syrup\"]');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('Cash','GCash','Maya') DEFAULT 'Cash',
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `amount`, `reference_number`, `payment_time`) VALUES
(1, 1, 'Cash', 115.00, NULL, '2025-12-15 14:26:46'),
(2, 2, 'GCash', 65.00, 'GCASH-REF-998877', '2025-12-15 14:26:46'),
(3, 5, 'Cash', 100.00, NULL, '2026-02-10 05:59:01'),
(4, 6, 'Cash', 100.00, NULL, '2026-02-10 06:00:08'),
(5, 7, 'Cash', 50.00, NULL, '2026-02-10 06:02:05'),
(6, 11, 'Cash', 100.00, NULL, '2026-02-10 07:57:05'),
(7, 19, 'Cash', 66.00, NULL, '2026-02-10 11:15:36'),
(8, 20, 'Cash', 100.00, NULL, '2026-02-10 12:58:56'),
(9, 24, 'Cash', 100.00, NULL, '2026-02-11 01:41:59'),
(10, 22, 'Cash', 50.00, NULL, '2026-02-11 02:10:19'),
(11, 21, 'Cash', 55.00, NULL, '2026-02-11 03:01:43'),
(12, 32, 'Cash', 55.00, NULL, '2026-02-11 07:24:15'),
(13, 47, 'Cash', 50.00, NULL, '2026-02-11 11:00:35'),
(14, 45, 'Cash', 60.00, NULL, '2026-02-11 11:00:45'),
(15, 43, 'Cash', 60.00, NULL, '2026-02-11 11:01:01'),
(16, 58, 'Cash', 50.00, NULL, '2026-02-11 11:18:18'),
(17, 59, 'Cash', 50.00, NULL, '2026-02-11 11:26:46'),
(18, 63, 'Cash', 50.00, NULL, '2026-02-13 02:18:14'),
(19, 56, 'Cash', 50.00, NULL, '2026-02-16 07:48:58'),
(20, 55, 'Cash', 100.00, NULL, '2026-02-16 07:49:08'),
(21, 67, 'Cash', 100.00, NULL, '2026-02-18 06:51:42'),
(22, 66, 'Cash', 200.00, NULL, '2026-02-20 00:47:40'),
(23, 71, 'Cash', 200.00, NULL, '2026-02-20 02:54:37'),
(24, 76, 'Cash', 300.00, NULL, '2026-02-24 11:55:54'),
(25, 81, 'Cash', 110.00, NULL, '2026-02-25 01:31:13'),
(26, 82, 'Cash', 200.00, NULL, '2026-02-25 01:50:07'),
(27, 84, 'Cash', 165.00, NULL, '2026-02-25 04:56:28'),
(28, 88, 'Cash', 300.00, NULL, '2026-02-25 05:54:21'),
(29, 89, 'Cash', 50.00, NULL, '2026-02-25 05:57:58'),
(30, 85, 'Cash', 50.00, NULL, '2026-02-25 05:58:11'),
(31, 86, 'Cash', 700.00, NULL, '2026-02-25 06:13:30'),
(32, 91, 'Cash', 100.00, NULL, '2026-02-25 06:22:10'),
(33, 97, 'Cash', 70.00, NULL, '2026-02-26 20:37:57'),
(34, 96, 'Cash', 300.00, NULL, '2026-02-26 20:46:42'),
(35, 98, 'Cash', 50.00, NULL, '2026-02-26 20:49:53'),
(36, 94, 'Cash', 50.00, NULL, '2026-02-26 20:51:21'),
(37, 99, 'Cash', 100.00, NULL, '2026-02-26 20:53:42'),
(38, 102, 'Cash', 70.00, NULL, '2026-02-27 02:56:04'),
(39, 103, 'Cash', 50.00, NULL, '2026-02-27 02:57:27'),
(40, 106, 'Cash', 70.00, NULL, '2026-02-27 02:59:37'),
(41, 109, 'Cash', 50.00, NULL, '2026-02-27 06:12:28'),
(42, 111, 'Cash', 20.00, NULL, '2026-02-27 06:16:40'),
(43, 113, 'Cash', 150.00, NULL, '2026-02-27 06:31:49'),
(44, 112, 'Cash', 300.00, NULL, '2026-02-27 06:47:26'),
(45, 114, 'Cash', 300.00, NULL, '2026-02-27 06:54:11'),
(46, 108, 'Cash', 200.00, NULL, '2026-02-27 06:59:06'),
(47, 118, 'Cash', 100.00, NULL, '2026-02-27 07:07:40'),
(48, 122, 'Cash', 200.00, NULL, '2026-03-03 00:40:20'),
(49, 147, 'Cash', 50.00, NULL, '2026-03-03 00:57:26'),
(50, 123, 'Cash', 300.00, NULL, '2026-03-03 01:51:08'),
(51, 134, 'Cash', 100.00, NULL, '2026-03-03 01:51:22'),
(52, 149, 'Cash', 20.00, NULL, '2026-03-03 01:51:43'),
(53, 154, 'Cash', 100.00, NULL, '2026-03-03 05:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `raw_id` int(11) NOT NULL,
  `quantity_consumed` decimal(10,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`recipe_id`, `menu_item_id`, `raw_id`, `quantity_consumed`) VALUES
(1, 10, 4, 0.020),
(2, 10, 1, 0.040),
(3, 6, 4, 0.018),
(4, 6, 23, 0.200),
(5, 6, 34, 1.000),
(6, 8, 4, 0.018),
(7, 8, 34, 1.000),
(8, 32, 2, 0.010),
(9, 32, 28, 0.050),
(10, 32, 34, 1.000),
(11, 33, 31, 0.250),
(12, 33, 32, 0.030),
(13, 33, 34, 1.000);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('maintenance_mode', 'false', '2026-02-13 07:22:40'),
('service_charge', '0.00', '2026-02-13 07:22:40'),
('store_name', 'GrubHound', '2026-02-13 07:22:40'),
('tax_rate', '0.00', '2026-02-13 07:22:40');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `starting_cash` decimal(10,2) DEFAULT 0.00,
  `expected_cash` decimal(10,2) DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT 0.00,
  `discrepancy` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `user_id`, `start_time`, `end_time`, `starting_cash`, `expected_cash`, `actual_cash`, `discrepancy`) VALUES
(1, 2, '2025-12-15 14:26:39', NULL, 1000.00, 1000.00, 1000.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('Admin','Cashier','Barista','Kitchen') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_store` varchar(50) DEFAULT 'Paws Place'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `role`, `created_at`, `assigned_store`) VALUES
(1, 'admin01', '$2y$10$rKzl/vBo9p1HbEacI1/KNu0fP45OlcsZ3GuutyQMr46mDVX8TDOOG', 'Erika Cruz', 'Admin', '2025-12-15 14:23:10', 'Paws Place'),
(2, 'cashier01', '$2y$10$qRxPcOxL2QF8iAspNdnl3uehqdacZd.RQSidpoZtc4mo/iuHy/sdG', 'James Dee', 'Cashier', '2025-12-15 14:23:10', 'Paws Place'),
(3, 'pupstop_cashier', '$2y$10$Oi.8YKD0pegxwztXLB0qeem3t63lY6jRp8VH2k/MD4XB1MPL1s0zK', 'Pup Stop Cashier', 'Cashier', '2026-02-27 06:31:06', 'Pup Stop'),
(4, 'kennelmain_cashier', '$2y$10$Oi.8YKD0pegxwztXLB0qeem3t63lY6jRp8VH2k/MD4XB1MPL1s0zK', 'Kennel Main Cashier', 'Cashier', '2026-02-27 06:31:06', 'Kennel Main'),
(5, 'kennelnorth_cashier', '$2y$10$Oi.8YKD0pegxwztXLB0qeem3t63lY6jRp8VH2k/MD4XB1MPL1s0zK', 'Kennel North Cashier', 'Cashier', '2026-02-27 06:31:06', 'Kennel North'),
(6, 'kitchen_staff', '$2y$10$jk9a4wQ9.c2Mt3pexTp1ku16DkEzXtagdRtsNX4NgZvIaBpKFVhPa', 'Kitchen Staff', 'Kitchen', '2026-03-02 21:15:29', 'Paws Place');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_activity_type` (`activity_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `api_cache_categories`
--
ALTER TABLE `api_cache_categories`
  ADD PRIMARY KEY (`id`,`location_id`);

--
-- Indexes for table `api_cache_items`
--
ALTER TABLE `api_cache_items`
  ADD PRIMARY KEY (`item_id`,`location_id`,`category_name`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `raw_id` (`raw_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `inventory_raw`
--
ALTER TABLE `inventory_raw`
  ADD PRIMARY KEY (`raw_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- Indexes for table `modifiers`
--
ALTER TABLE `modifiers`
  ADD PRIMARY KEY (`modifier_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `applicable_category_id` (`applicable_category_id`);

--
-- Indexes for table `modifier_inventory_links`
--
ALTER TABLE `modifier_inventory_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `modifier_id` (`modifier_id`),
  ADD KEY `raw_id` (`raw_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `pre_order_code` (`pre_order_code`),
  ADD UNIQUE KEY `final_code` (`final_code`),
  ADD KEY `cashier_id` (`cashier_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `raw_id` (`raw_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=210;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `inventory_raw`
--
ALTER TABLE `inventory_raw`
  MODIFY `raw_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `modifiers`
--
ALTER TABLE `modifiers`
  MODIFY `modifier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `modifier_inventory_links`
--
ALTER TABLE `modifier_inventory_links`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`),
  ADD CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `modifiers`
--
ALTER TABLE `modifiers`
  ADD CONSTRAINT `modifiers_ibfk_1` FOREIGN KEY (`applicable_category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `modifier_inventory_links`
--
ALTER TABLE `modifier_inventory_links`
  ADD CONSTRAINT `modifier_inventory_links_ibfk_1` FOREIGN KEY (`modifier_id`) REFERENCES `modifiers` (`modifier_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `modifier_inventory_links_ibfk_2` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`item_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`) ON DELETE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
