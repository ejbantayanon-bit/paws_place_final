-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2026 at 03:44 AM
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
(35, 1, 'Admin', 'LOGOUT', 'User logged out', NULL, '2026-02-19 02:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `is_active`, `sort_order`) VALUES
(1, 'Hot Coffee', 1, 1),
(2, 'Cold Coffee', 1, 2),
(3, 'Specialty Drinks (Hot/Cold)', 1, 3),
(4, 'Milk Tea', 1, 4),
(5, 'Fruity Soda', 1, 5),
(6, 'Add Ons', 1, 6),
(7, 'Ice Cream in Cups (100g)', 1, 7),
(8, 'Ice Cream Bar (95g)', 1, 8),
(9, 'Milk Drink (350ml)', 1, 9);

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
(45, 34, NULL, -1.000, 'Order sale', '2026-02-11 11:01:53');

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
(1, 'Milk Powder (Full Cream)', 'kg', 0.000, 5.000, 0.00),
(2, 'Black Tea Leaves', 'kg', -0.010, 3.000, 0.00),
(3, 'Sugar Syrup Base', 'L', 0.000, 10.000, 0.00),
(4, 'Espresso Coffee Beans', 'kg', -0.072, 5.000, 0.00),
(5, 'Pearl Tapioca', 'kg', 0.000, 5.000, 0.00),
(6, 'Caramel Syrup Concentrate', 'L', 0.000, 2.000, 0.00),
(23, 'Full Cream Milk', 'L', 49.400, 10.000, 90.00),
(24, 'Chocolate Syrup', 'L', 10.000, 2.000, 250.00),
(25, 'Caramel Syrup', 'L', 10.000, 2.000, 250.00),
(26, 'Milk Tea Creamer', 'kg', 20.000, 5.000, 150.00),
(27, 'Fructose Syrup', 'L', 20.000, 5.000, 100.00),
(28, 'Tapioca Pearls (Raw)', 'kg', 14.950, 3.000, 120.00),
(29, 'Taro Powder', 'kg', 5.000, 1.000, 300.00),
(30, 'Matcha Powder', 'kg', 3.000, 0.500, 800.00),
(31, 'Soda Water', 'L', 30.000, 5.000, 40.00),
(32, 'Mango Syrup', 'L', 5.000, 1.000, 200.00),
(33, 'Green Apple Syrup', 'L', 5.000, 1.000, 200.00),
(34, 'Plastic Cup (16oz)', 'pcs', 495.000, 100.000, 2.50),
(35, 'Plastic Cup (22oz)', 'pcs', 500.000, 100.000, 3.00),
(36, 'Straws', 'pcs', 1000.000, 200.000, 0.50);

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
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`item_id`, `name`, `category_id`, `base_price`, `is_available`, `image_url`, `deleted_at`) VALUES
(1, 'Espresso', 1, 35.00, 1, NULL, NULL),
(2, 'Brewed', 1, 40.00, 1, NULL, NULL),
(3, 'Americano (Hot)', 1, 45.00, 1, NULL, NULL),
(4, 'Long Black', 1, 45.00, 1, NULL, NULL),
(5, 'Cappuccino (Hot)', 1, 50.00, 1, NULL, NULL),
(6, 'Latte (Hot)', 1, 55.00, 1, NULL, NULL),
(7, 'Mocha (Hot)', 1, 65.00, 1, NULL, NULL),
(8, 'Iced Americano', 2, 45.00, 1, NULL, NULL),
(9, 'Cold Brew', 2, 45.00, 1, NULL, NULL),
(10, 'Iced Latte', 2, 60.00, 1, NULL, NULL),
(11, 'Iced Cappuccino', 2, 60.00, 1, NULL, NULL),
(12, 'Iced Mocha', 2, 65.00, 1, NULL, NULL),
(13, 'Caramel Macchiato', 3, 55.00, 1, NULL, NULL),
(14, 'Spanish Latte', 3, 55.00, 1, NULL, NULL),
(15, 'Mocha Latte', 3, 65.00, 1, NULL, NULL),
(16, 'White Mocha', 3, 65.00, 1, NULL, NULL),
(17, 'Matcha Green Tea Latte', 3, 65.00, 1, NULL, NULL),
(18, 'Shaken Lemon Lychee', 3, 65.00, 1, NULL, NULL),
(19, 'Hot Chocolate', 3, 40.00, 1, NULL, NULL),
(20, 'Hot Milk', 3, 40.00, 1, NULL, NULL),
(21, 'Ice Choco', 3, 55.00, 1, NULL, NULL),
(22, 'Black Forest MT', 4, 55.00, 1, NULL, NULL),
(23, 'Chocolate MT', 4, 55.00, 1, NULL, NULL),
(24, 'Cookies and Cream MT', 4, 55.00, 1, NULL, NULL),
(25, 'Dark Choco MT', 4, 55.00, 1, NULL, NULL),
(26, 'Matcha MT', 4, 55.00, 1, NULL, NULL),
(27, 'Red Velvet MT', 4, 55.00, 1, NULL, NULL),
(28, 'Taro MT', 4, 55.00, 1, NULL, NULL),
(29, 'Wintermelon MT', 4, 55.00, 1, NULL, NULL),
(30, 'Hokkaido MT', 4, 55.00, 1, NULL, NULL),
(31, 'Okinawa MT', 4, 55.00, 1, NULL, NULL),
(32, 'Panda Pearl MT', 4, 55.00, 1, NULL, NULL),
(33, 'Mango Soda', 5, 60.00, 1, NULL, NULL),
(34, 'Green Apple Soda', 5, 60.00, 1, NULL, NULL),
(35, 'Lychee Soda', 5, 60.00, 1, NULL, NULL),
(36, 'Strawberry Soda', 5, 60.00, 1, NULL, NULL),
(37, 'Passion Fruit Soda', 5, 60.00, 1, NULL, NULL),
(38, 'Melon Soda', 5, 60.00, 1, NULL, NULL),
(39, 'Mango IC (100g)', 7, 50.00, 1, NULL, NULL),
(40, 'Vanilla-Cashew IC (100g)', 7, 50.00, 1, NULL, NULL),
(41, 'Tablia Native Cacao IC (100g)', 7, 50.00, 1, NULL, NULL),
(42, 'Coconut IC (100g)', 7, 50.00, 1, NULL, NULL),
(43, 'Matcha IC (100g)', 7, 50.00, 1, NULL, NULL),
(44, 'Black Sesame IC (100g)', 7, 50.00, 1, NULL, NULL),
(45, 'Coconut IC Bar (95g)', 8, 85.00, 1, NULL, NULL),
(46, 'Matcha IC Bar (95g)', 8, 85.00, 1, NULL, NULL),
(47, 'Milk-Cashew IC Bar (95g)', 8, 85.00, 1, NULL, NULL),
(48, 'Tablia Native Cacao IC Bar (95g)', 8, 85.00, 1, NULL, NULL),
(49, 'Cow Milk (350ml)', 9, 85.00, 1, NULL, NULL),
(50, 'Water Buffalo Milk (350ml)', 9, 90.00, 1, NULL, NULL),
(51, 'Chocolate (Cow) (350ml)', 9, 90.00, 1, NULL, NULL),
(52, 'Chocolate (Water Buffalo) (350ml)', 9, 95.00, 1, NULL, NULL),
(53, 'Matcha (Cow) (350ml)', 9, 105.00, 1, NULL, NULL),
(54, 'Matcha (Water Buffalo) (350ml)', 9, 110.00, 1, NULL, NULL),
(55, 'Mocha (Cow) (350ml)', 9, 105.00, 1, NULL, NULL),
(56, 'Mocha (Water Buffalo) (350ml)', 9, 115.00, 1, NULL, NULL);

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
  `order_source` enum('Kiosk','Manual_POS') NOT NULL,
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
(47, '124D', NULL, 'Kiosk', 45.00, 'READY', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:42', '2026-02-11 11:00:35'),
(48, '747V', NULL, 'Kiosk', 45.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:47', NULL),
(49, '541L', NULL, 'Kiosk', 40.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:50', NULL),
(50, '907L', NULL, 'Kiosk', 85.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 10:58:58', NULL),
(51, '145V', NULL, 'Kiosk', 55.00, 'PENDING PAYMENT', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:01:53', NULL),
(52, '461W', NULL, 'Kiosk', 90.00, 'PENDING PAYMENT', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:05:34', NULL),
(53, '626J', NULL, 'Kiosk', 4600.00, 'CANCELLED', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:35', NULL),
(54, '375A', NULL, 'Kiosk', 240.00, 'PENDING PAYMENT', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:43', NULL),
(55, '108A', NULL, 'Kiosk', 50.00, 'PREPARING', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:46', '2026-02-16 07:49:08'),
(56, '777I', NULL, 'Kiosk', 45.00, 'PREPARING', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:48', '2026-02-16 07:48:58'),
(57, '227F', NULL, 'Kiosk', 270.00, 'PENDING PAYMENT', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:52', NULL),
(58, '091W', NULL, 'Kiosk', 45.00, 'READY', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:56', '2026-02-11 11:18:18'),
(59, '693U', NULL, 'Kiosk', 45.00, 'READY', NULL, '20190412', 'HOGARTH CYFER BUENAVENTURA CATIPAY', NULL, '2026-02-11 11:06:56', '2026-02-11 11:26:46'),
(60, '343T', NULL, 'Kiosk', 1050.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:09:34', NULL),
(61, '699S', NULL, 'Kiosk', 1560.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:26:44', NULL),
(62, '331K', NULL, 'Kiosk', 7500.00, 'CANCELLED', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-11 11:33:46', NULL),
(63, '322M', NULL, 'Kiosk', 50.00, 'PREPARING', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 02:17:44', '2026-02-13 02:18:14'),
(64, '614G', NULL, 'Kiosk', 40.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 04:51:00', NULL),
(65, '354A', NULL, 'Kiosk', 105.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-13 07:19:40', NULL),
(66, '206D', NULL, 'Kiosk', 135.00, 'PENDING PAYMENT', NULL, '20211537', 'EJ TRUNO BANTAYANON', NULL, '2026-02-18 05:37:10', NULL),
(67, '031L', NULL, 'Kiosk', 60.00, 'PREPARING', NULL, '20220120', 'JHON HURTZ PALUCA DAUG', NULL, '2026-02-18 06:50:26', '2026-02-18 06:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL,
  `modifiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modifiers`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `menu_item_id`, `quantity`, `price_at_sale`, `modifiers`) VALUES
(1, 1, 6, 1, 55.00, NULL),
(2, 1, 33, 1, 60.00, NULL),
(3, 2, 32, 1, 65.00, '{\"Pearls\": \"Extra\"}'),
(4, 3, 4, 1, 45.00, '[]'),
(5, 4, 5, 1, 50.00, '[]'),
(6, 5, 11, 1, 70.00, '[\"Coffee Jelly\"]'),
(7, 6, 5, 1, 60.00, '[\"Caramel Syrup\"]'),
(8, 7, 2, 1, 40.00, '[]'),
(9, 8, 23, 1, 55.00, '[]'),
(10, 8, 24, 1, 55.00, '[]'),
(11, 8, 32, 1, 55.00, '[]'),
(12, 8, 25, 1, 55.00, '[]'),
(13, 8, 30, 1, 55.00, '[]'),
(14, 9, 3, 1, 55.00, '[\"Milk\"]'),
(15, 10, 14, 1, 55.00, '[]'),
(16, 11, 15, 1, 65.00, '[]'),
(17, 12, 4, 1, 55.00, '[\"Caramel Syrup\"]'),
(18, 13, 2, 1, 50.00, '[\"Caramel Syrup\"]'),
(19, 14, 4, 1, 45.00, '[]'),
(20, 15, 5, 1, 50.00, '[]'),
(21, 16, 5, 1, 60.00, '[\"Caramel Syrup\"]'),
(22, 17, 5, 1, 50.00, '[]'),
(23, 18, 4, 1, 45.00, '[]'),
(24, 19, 6, 1, 65.00, '[\"Milk\"]'),
(25, 20, 5, 1, 50.00, '[]'),
(26, 21, 30, 1, 55.00, '[]'),
(27, 22, 1, 1, 45.00, '[\"Caramel Syrup\"]'),
(28, 23, 46, 1, 85.00, '[]'),
(29, 24, 2, 1, 60.00, '[\"Milk\",\"Caramel Syrup\"]'),
(30, 25, 5, 1, 60.00, '[\"Caramel Syrup\"]'),
(31, 26, 5, 1, 60.00, '[\"Milk\"]'),
(32, 27, 2, 1, 40.00, '[]'),
(33, 28, 39, 1, 50.00, '[]'),
(34, 29, 52, 1, 95.00, '[]'),
(35, 30, 3, 1, 55.00, '[\"Milk\"]'),
(36, 31, 5, 1, 50.00, '[]'),
(37, 32, 9, 1, 55.00, '[\"Milk\"]'),
(38, 33, 8, 1, 55.00, '[\"Milk\"]'),
(39, 34, 3, 1, 55.00, '[\"Milk\"]'),
(40, 35, 2, 1, 40.00, '[]'),
(41, 36, 3, 1, 55.00, '[\"Milk\"]'),
(42, 37, 3, 1, 55.00, '[\"Caramel Syrup\"]'),
(43, 38, 42, 1, 50.00, '[]'),
(44, 39, 19, 1, 40.00, '[]'),
(45, 40, 13, 1, 55.00, '[]'),
(46, 41, 2, 1, 40.00, '[]'),
(47, 42, 3, 1, 45.00, '[]'),
(48, 43, 14, 1, 55.00, '[]'),
(49, 44, 5, 1, 50.00, '[]'),
(50, 45, 3, 1, 55.00, '[\"Caramel Syrup\"]'),
(51, 46, 6, 1, 55.00, '[]'),
(52, 46, 4, 1, 45.00, '[]'),
(53, 47, 4, 1, 45.00, '[]'),
(54, 48, 4, 1, 45.00, '[]'),
(55, 49, 2, 1, 40.00, '[]'),
(56, 50, 46, 1, 85.00, '[]'),
(57, 51, 6, 1, 55.00, '[]'),
(58, 52, 3, 1, 45.00, '[]'),
(59, 52, 4, 1, 45.00, '[]'),
(60, 53, 56, 40, 115.00, '[]'),
(61, 54, 5, 3, 50.00, '[]'),
(62, 54, 4, 2, 45.00, '[]'),
(63, 55, 5, 1, 50.00, '[]'),
(64, 56, 4, 1, 45.00, '[]'),
(65, 57, 4, 6, 45.00, '[]'),
(66, 58, 4, 1, 45.00, '[]'),
(67, 59, 4, 1, 45.00, '[]'),
(68, 60, 5, 21, 50.00, '[]'),
(69, 61, 26, 24, 65.00, '[\"Pearls\"]'),
(70, 62, 7, 100, 75.00, '[\"Caramel Syrup\"]'),
(71, 63, 5, 1, 50.00, '[]'),
(72, 64, 2, 1, 40.00, '[]'),
(73, 65, 19, 1, 40.00, '[]'),
(74, 65, 18, 1, 65.00, '[]'),
(75, 66, 2, 1, 50.00, '[\"Caramel Syrup\"]'),
(76, 66, 46, 1, 85.00, '[]'),
(77, 67, 5, 1, 60.00, '[\"Caramel Syrup\"]');

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
(21, 67, 'Cash', 100.00, NULL, '2026-02-18 06:51:42');

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
  `role` enum('Admin','Cashier','Barista') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin01', '$2y$10$rKzl/vBo9p1HbEacI1/KNu0fP45OlcsZ3GuutyQMr46mDVX8TDOOG', 'Erika Cruz', 'Admin', '2025-12-15 14:23:10'),
(2, 'cashier01', '$2y$10$qRxPcOxL2QF8iAspNdnl3uehqdacZd.RQSidpoZtc4mo/iuHy/sdG', 'James Dee', 'Cashier', '2025-12-15 14:23:10');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `inventory_raw`
--
ALTER TABLE `inventory_raw`
  MODIFY `raw_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

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
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
