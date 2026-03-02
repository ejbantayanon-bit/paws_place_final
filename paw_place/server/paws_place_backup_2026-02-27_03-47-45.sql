-- Paws Place Database Backup
-- Date: 2026-02-27 03:47:45

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_role` enum('Admin','Cashier','Barista') NOT NULL,
  `activity_type` enum('LOGIN','LOGOUT','MENU_CREATE','MENU_UPDATE','MENU_DELETE','MENU_RESTORE','INVENTORY_ADJUST','ORDER_STATUS_CHANGE') NOT NULL,
  `description` text NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_activity_type` (`activity_type`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `activity_logs` VALUES("1","1","Admin","LOGIN","Admin logged in","{\"ip\": \"127.0.0.1\"}","2026-02-11 15:14:09");
INSERT INTO `activity_logs` VALUES("2","2","Cashier","LOGIN","Cashier logged in","{\"ip\": \"127.0.0.1\"}","2026-02-11 15:14:09");
INSERT INTO `activity_logs` VALUES("3","1","Admin","LOGOUT","User logged out","","2026-02-16 13:00:24");
INSERT INTO `activity_logs` VALUES("4","2","Cashier","LOGIN","User logged in","","2026-02-16 13:00:34");
INSERT INTO `activity_logs` VALUES("5","2","Cashier","LOGOUT","User logged out","","2026-02-16 13:00:57");
INSERT INTO `activity_logs` VALUES("6","1","Admin","LOGIN","User logged in","","2026-02-16 13:01:07");
INSERT INTO `activity_logs` VALUES("7","1","Admin","LOGOUT","User logged out","","2026-02-16 13:01:26");
INSERT INTO `activity_logs` VALUES("8","2","Cashier","LOGIN","User logged in","","2026-02-16 15:40:37");
INSERT INTO `activity_logs` VALUES("9","2","Cashier","LOGOUT","User logged out","","2026-02-16 15:42:41");
INSERT INTO `activity_logs` VALUES("10","2","Cashier","LOGIN","User logged in","","2026-02-16 15:48:35");
INSERT INTO `activity_logs` VALUES("11","2","Cashier","LOGOUT","User logged out","","2026-02-16 15:49:16");
INSERT INTO `activity_logs` VALUES("12","1","Admin","LOGIN","User logged in","","2026-02-18 13:18:50");
INSERT INTO `activity_logs` VALUES("13","1","Admin","LOGOUT","User logged out","","2026-02-18 13:24:46");
INSERT INTO `activity_logs` VALUES("14","2","Cashier","LOGIN","User logged in","","2026-02-18 13:25:14");
INSERT INTO `activity_logs` VALUES("15","2","Cashier","LOGOUT","User logged out","","2026-02-18 13:26:37");
INSERT INTO `activity_logs` VALUES("16","2","Cashier","LOGIN","User logged in","","2026-02-18 13:28:01");
INSERT INTO `activity_logs` VALUES("17","2","Cashier","LOGOUT","User logged out","","2026-02-18 13:28:28");
INSERT INTO `activity_logs` VALUES("20","1","Admin","LOGIN","User logged in","","2026-02-18 13:29:01");
INSERT INTO `activity_logs` VALUES("21","1","Admin","LOGOUT","User logged out","","2026-02-18 13:33:00");
INSERT INTO `activity_logs` VALUES("22","2","Cashier","LOGIN","User logged in","","2026-02-18 14:42:17");
INSERT INTO `activity_logs` VALUES("23","2","Cashier","LOGOUT","User logged out","","2026-02-18 14:42:47");
INSERT INTO `activity_logs` VALUES("24","2","Cashier","LOGIN","User logged in","","2026-02-18 14:47:33");
INSERT INTO `activity_logs` VALUES("25","2","Cashier","LOGOUT","User logged out","","2026-02-18 14:48:51");
INSERT INTO `activity_logs` VALUES("26","2","Cashier","LOGIN","User logged in","","2026-02-18 14:49:02");
INSERT INTO `activity_logs` VALUES("27","2","Cashier","LOGOUT","User logged out","","2026-02-18 14:54:12");
INSERT INTO `activity_logs` VALUES("28","2","Cashier","LOGIN","User logged in","","2026-02-18 15:05:52");
INSERT INTO `activity_logs` VALUES("29","2","Cashier","LOGOUT","User logged out","","2026-02-18 15:09:02");
INSERT INTO `activity_logs` VALUES("30","1","Admin","LOGIN","User logged in","","2026-02-18 15:09:12");
INSERT INTO `activity_logs` VALUES("31","1","Admin","LOGOUT","User logged out","","2026-02-18 15:10:41");
INSERT INTO `activity_logs` VALUES("32","2","Cashier","LOGIN","User logged in","","2026-02-19 10:16:01");
INSERT INTO `activity_logs` VALUES("33","2","Cashier","LOGOUT","User logged out","","2026-02-19 10:16:11");
INSERT INTO `activity_logs` VALUES("34","1","Admin","LOGIN","User logged in","","2026-02-19 10:21:04");
INSERT INTO `activity_logs` VALUES("35","1","Admin","LOGOUT","User logged out","","2026-02-19 10:22:53");
INSERT INTO `activity_logs` VALUES("36","2","Cashier","LOGIN","User logged in","","2026-02-20 08:47:16");
INSERT INTO `activity_logs` VALUES("37","2","Cashier","LOGOUT","User logged out","","2026-02-20 08:48:23");
INSERT INTO `activity_logs` VALUES("38","1","Admin","LOGIN","User logged in","","2026-02-20 08:50:15");
INSERT INTO `activity_logs` VALUES("39","1","Admin","LOGOUT","User logged out","","2026-02-20 08:51:04");
INSERT INTO `activity_logs` VALUES("40","2","Cashier","LOGIN","User logged in","","2026-02-20 08:56:12");
INSERT INTO `activity_logs` VALUES("41","2","Cashier","LOGOUT","User logged out","","2026-02-20 08:56:24");
INSERT INTO `activity_logs` VALUES("42","1","Admin","LOGIN","User logged in","","2026-02-20 09:14:14");
INSERT INTO `activity_logs` VALUES("43","1","Admin","LOGOUT","User logged out","","2026-02-20 09:14:36");
INSERT INTO `activity_logs` VALUES("44","2","Cashier","LOGIN","User logged in","","2026-02-20 09:14:51");
INSERT INTO `activity_logs` VALUES("45","2","Cashier","LOGOUT","User logged out","","2026-02-20 09:15:28");
INSERT INTO `activity_logs` VALUES("46","1","Admin","LOGIN","User logged in","","2026-02-20 09:18:54");
INSERT INTO `activity_logs` VALUES("47","1","Admin","LOGOUT","User logged out","","2026-02-20 09:19:48");
INSERT INTO `activity_logs` VALUES("48","2","Cashier","LOGIN","User logged in","","2026-02-20 10:51:43");
INSERT INTO `activity_logs` VALUES("49","2","Cashier","LOGOUT","User logged out","","2026-02-20 11:11:35");
INSERT INTO `activity_logs` VALUES("50","1","Admin","LOGIN","User logged in","","2026-02-20 11:11:42");
INSERT INTO `activity_logs` VALUES("51","1","Admin","LOGOUT","User logged out","","2026-02-20 11:11:57");
INSERT INTO `activity_logs` VALUES("52","1","Admin","LOGIN","User logged in","","2026-02-20 11:13:41");
INSERT INTO `activity_logs` VALUES("53","1","Admin","LOGOUT","User logged out","","2026-02-20 11:25:31");
INSERT INTO `activity_logs` VALUES("54","2","Cashier","LOGIN","User logged in","","2026-02-20 13:00:32");
INSERT INTO `activity_logs` VALUES("55","2","Cashier","LOGOUT","User logged out","","2026-02-20 13:01:47");
INSERT INTO `activity_logs` VALUES("56","2","Cashier","LOGIN","User logged in","","2026-02-24 14:52:59");
INSERT INTO `activity_logs` VALUES("57","2","Cashier","LOGOUT","User logged out","","2026-02-24 14:53:06");
INSERT INTO `activity_logs` VALUES("58","2","Cashier","LOGIN","User logged in","","2026-02-24 19:55:17");
INSERT INTO `activity_logs` VALUES("59","2","Cashier","LOGOUT","User logged out","","2026-02-24 19:59:05");
INSERT INTO `activity_logs` VALUES("60","1","Admin","LOGIN","User logged in","","2026-02-24 19:59:12");
INSERT INTO `activity_logs` VALUES("61","1","Admin","LOGOUT","User logged out","","2026-02-24 19:59:59");
INSERT INTO `activity_logs` VALUES("62","2","Cashier","LOGIN","User logged in","","2026-02-24 20:01:28");
INSERT INTO `activity_logs` VALUES("63","2","Cashier","LOGOUT","User logged out","","2026-02-24 20:03:36");
INSERT INTO `activity_logs` VALUES("64","2","Cashier","LOGIN","User logged in","","2026-02-25 00:17:34");
INSERT INTO `activity_logs` VALUES("65","2","Cashier","LOGOUT","User logged out","","2026-02-25 00:25:02");
INSERT INTO `activity_logs` VALUES("66","2","Cashier","LOGIN","User logged in","","2026-02-25 05:05:15");
INSERT INTO `activity_logs` VALUES("67","2","Cashier","LOGOUT","User logged out","","2026-02-25 05:05:27");
INSERT INTO `activity_logs` VALUES("68","1","Admin","LOGIN","User logged in","","2026-02-25 05:05:34");
INSERT INTO `activity_logs` VALUES("69","1","Admin","LOGOUT","User logged out","","2026-02-25 05:05:51");
INSERT INTO `activity_logs` VALUES("70","2","Cashier","LOGIN","User logged in","","2026-02-25 09:25:17");
INSERT INTO `activity_logs` VALUES("71","2","Cashier","LOGIN","User logged in","","2026-02-25 09:49:37");
INSERT INTO `activity_logs` VALUES("72","2","Cashier","LOGOUT","User logged out","","2026-02-25 09:52:56");
INSERT INTO `activity_logs` VALUES("73","2","Cashier","LOGIN","User logged in","","2026-02-25 09:53:12");
INSERT INTO `activity_logs` VALUES("74","2","Cashier","LOGOUT","User logged out","","2026-02-25 09:53:24");
INSERT INTO `activity_logs` VALUES("75","2","Cashier","LOGIN","User logged in","","2026-02-25 09:53:29");
INSERT INTO `activity_logs` VALUES("76","2","Cashier","LOGIN","User logged in","","2026-02-25 11:49:40");
INSERT INTO `activity_logs` VALUES("77","2","Cashier","LOGOUT","User logged out","","2026-02-25 11:49:53");
INSERT INTO `activity_logs` VALUES("78","2","Cashier","LOGOUT","User logged out","","2026-02-25 12:08:25");
INSERT INTO `activity_logs` VALUES("79","2","Cashier","LOGIN","User logged in","","2026-02-25 12:08:32");
INSERT INTO `activity_logs` VALUES("80","2","Cashier","LOGOUT","User logged out","","2026-02-25 13:50:14");
INSERT INTO `activity_logs` VALUES("81","2","Cashier","LOGIN","User logged in","","2026-02-25 13:54:02");
INSERT INTO `activity_logs` VALUES("82","2","Cashier","LOGIN","User logged in","","2026-02-25 13:57:41");
INSERT INTO `activity_logs` VALUES("83","2","Cashier","LOGOUT","User logged out","","2026-02-25 13:59:22");
INSERT INTO `activity_logs` VALUES("84","2","Cashier","LOGOUT","User logged out","","2026-02-25 14:11:11");
INSERT INTO `activity_logs` VALUES("85","2","Cashier","LOGIN","User logged in","","2026-02-25 14:13:15");
INSERT INTO `activity_logs` VALUES("86","2","Cashier","LOGOUT","User logged out","","2026-02-25 14:16:28");
INSERT INTO `activity_logs` VALUES("87","2","Cashier","LOGIN","User logged in","","2026-02-25 14:21:34");
INSERT INTO `activity_logs` VALUES("88","2","Cashier","LOGOUT","User logged out","","2026-02-25 14:23:53");
INSERT INTO `activity_logs` VALUES("89","2","Cashier","LOGIN","User logged in","","2026-02-25 16:22:41");
INSERT INTO `activity_logs` VALUES("90","2","Cashier","LOGOUT","User logged out","","2026-02-25 16:22:49");
INSERT INTO `activity_logs` VALUES("91","1","Admin","LOGIN","User logged in","","2026-02-25 16:22:59");
INSERT INTO `activity_logs` VALUES("92","1","Admin","LOGOUT","User logged out","","2026-02-25 16:23:40");
INSERT INTO `activity_logs` VALUES("93","1","Admin","LOGIN","User logged in","","2026-02-25 17:02:17");
INSERT INTO `activity_logs` VALUES("94","1","Admin","LOGOUT","User logged out","","2026-02-25 17:03:06");
INSERT INTO `activity_logs` VALUES("95","1","Admin","LOGIN","User logged in","","2026-02-25 17:03:37");
INSERT INTO `activity_logs` VALUES("96","1","Admin","LOGOUT","User logged out","","2026-02-25 17:03:55");
INSERT INTO `activity_logs` VALUES("97","2","Cashier","LOGIN","User logged in","","2026-02-27 04:18:22");
INSERT INTO `activity_logs` VALUES("98","2","Cashier","LOGIN","User logged in","","2026-02-27 04:46:35");
INSERT INTO `activity_logs` VALUES("99","2","Cashier","LOGIN","User logged in","","2026-02-27 05:06:48");
INSERT INTO `activity_logs` VALUES("100","2","Cashier","LOGIN","User logged in","","2026-02-27 05:22:18");
INSERT INTO `activity_logs` VALUES("101","2","Cashier","LOGOUT","User logged out","","2026-02-27 05:22:31");
INSERT INTO `activity_logs` VALUES("102","2","Cashier","LOGIN","User logged in","","2026-02-27 09:29:31");
INSERT INTO `activity_logs` VALUES("103","2","Cashier","LOGOUT","User logged out","","2026-02-27 09:30:25");


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` VALUES("1","Hot Coffee","1","1");
INSERT INTO `categories` VALUES("2","Cold Coffee","1","2");
INSERT INTO `categories` VALUES("3","Specialty Drinks (Hot/Cold)","1","3");
INSERT INTO `categories` VALUES("4","Milk Tea","1","4");
INSERT INTO `categories` VALUES("5","Fruity Soda","1","5");
INSERT INTO `categories` VALUES("6","Add Ons","1","6");
INSERT INTO `categories` VALUES("7","Ice Cream in Cups (100g)","1","7");
INSERT INTO `categories` VALUES("8","Ice Cream Bar (95g)","1","8");
INSERT INTO `categories` VALUES("9","Milk Drink (350ml)","1","9");


DROP TABLE IF EXISTS `inventory_logs`;
CREATE TABLE `inventory_logs` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `raw_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `change_amount` decimal(10,3) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `log_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `raw_id` (`raw_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `inventory_logs_ibfk_1` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`),
  CONSTRAINT `inventory_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `inventory_logs` VALUES("1","1","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("2","2","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("3","3","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("4","4","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("5","5","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("6","6","1","0.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("7","23","1","50.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("8","24","1","10.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("9","25","1","10.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("10","26","1","20.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("11","27","1","20.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("12","28","1","15.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("13","29","1","5.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("14","30","1","3.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("15","31","1","30.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("16","32","1","5.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("17","33","1","5.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("18","34","1","500.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("19","35","1","500.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("20","36","1","1000.000","Initial Stock In","2025-12-15 22:26:53");
INSERT INTO `inventory_logs` VALUES("32","2","","-0.010","Order sale","2026-02-10 15:21:49");
INSERT INTO `inventory_logs` VALUES("33","28","","-0.050","Order sale","2026-02-10 15:21:49");
INSERT INTO `inventory_logs` VALUES("34","34","","-1.000","Order sale","2026-02-10 15:21:49");
INSERT INTO `inventory_logs` VALUES("35","4","","-0.018","Order sale","2026-02-10 19:14:39");
INSERT INTO `inventory_logs` VALUES("36","23","","-0.200","Order sale","2026-02-10 19:14:39");
INSERT INTO `inventory_logs` VALUES("37","34","","-1.000","Order sale","2026-02-10 19:14:39");
INSERT INTO `inventory_logs` VALUES("38","4","","-0.018","Order sale","2026-02-11 17:18:32");
INSERT INTO `inventory_logs` VALUES("39","34","","-1.000","Order sale","2026-02-11 17:18:32");
INSERT INTO `inventory_logs` VALUES("40","4","","-0.018","Order sale","2026-02-11 18:58:40");
INSERT INTO `inventory_logs` VALUES("41","23","","-0.200","Order sale","2026-02-11 18:58:40");
INSERT INTO `inventory_logs` VALUES("42","34","","-1.000","Order sale","2026-02-11 18:58:40");
INSERT INTO `inventory_logs` VALUES("43","4","","-0.018","Order sale","2026-02-11 19:01:53");
INSERT INTO `inventory_logs` VALUES("44","23","","-0.200","Order sale","2026-02-11 19:01:53");
INSERT INTO `inventory_logs` VALUES("45","34","","-1.000","Order sale","2026-02-11 19:01:53");
INSERT INTO `inventory_logs` VALUES("46","4","","-0.036","Order sale","2026-02-20 10:08:32");
INSERT INTO `inventory_logs` VALUES("47","23","","-0.400","Order sale","2026-02-20 10:08:32");
INSERT INTO `inventory_logs` VALUES("48","34","","-2.000","Order sale","2026-02-20 10:08:32");
INSERT INTO `inventory_logs` VALUES("49","31","","-1.250","Order sale","2026-02-24 17:25:52");
INSERT INTO `inventory_logs` VALUES("50","32","","-0.150","Order sale","2026-02-24 17:25:52");
INSERT INTO `inventory_logs` VALUES("51","34","","-5.000","Order sale","2026-02-24 17:25:52");
INSERT INTO `inventory_logs` VALUES("52","4","","-0.080","Order sale","2026-02-25 08:36:21");
INSERT INTO `inventory_logs` VALUES("53","1","","-0.160","Order sale","2026-02-25 08:36:21");
INSERT INTO `inventory_logs` VALUES("54","4","","-0.018","Order sale","2026-02-25 13:52:24");
INSERT INTO `inventory_logs` VALUES("55","23","","-0.200","Order sale","2026-02-25 13:52:24");
INSERT INTO `inventory_logs` VALUES("56","34","","-1.000","Order sale","2026-02-25 13:52:24");


DROP TABLE IF EXISTS `inventory_raw`;
CREATE TABLE `inventory_raw` (
  `raw_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `unit_of_measure` varchar(20) NOT NULL,
  `quantity_on_hand` decimal(10,3) DEFAULT 0.000,
  `reorder_point` decimal(10,3) DEFAULT 10.000,
  `cost_per_unit` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`raw_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `inventory_raw` VALUES("1","Milk Powder (Full Cream)","kg","-0.160","5.000","0.00");
INSERT INTO `inventory_raw` VALUES("2","Black Tea Leaves","kg","-0.010","3.000","0.00");
INSERT INTO `inventory_raw` VALUES("3","Sugar Syrup Base","L","0.000","10.000","0.00");
INSERT INTO `inventory_raw` VALUES("4","Espresso Coffee Beans","kg","-0.206","5.000","0.00");
INSERT INTO `inventory_raw` VALUES("5","Pearl Tapioca","kg","0.000","5.000","0.00");
INSERT INTO `inventory_raw` VALUES("6","Caramel Syrup Concentrate","L","0.000","2.000","0.00");
INSERT INTO `inventory_raw` VALUES("23","Full Cream Milk","L","48.800","10.000","90.00");
INSERT INTO `inventory_raw` VALUES("24","Chocolate Syrup","L","10.000","2.000","250.00");
INSERT INTO `inventory_raw` VALUES("25","Caramel Syrup","L","10.000","2.000","250.00");
INSERT INTO `inventory_raw` VALUES("26","Milk Tea Creamer","kg","20.000","5.000","150.00");
INSERT INTO `inventory_raw` VALUES("27","Fructose Syrup","L","20.000","5.000","100.00");
INSERT INTO `inventory_raw` VALUES("28","Tapioca Pearls (Raw)","kg","14.950","3.000","120.00");
INSERT INTO `inventory_raw` VALUES("29","Taro Powder","kg","5.000","1.000","300.00");
INSERT INTO `inventory_raw` VALUES("30","Matcha Powder","kg","3.000","0.500","800.00");
INSERT INTO `inventory_raw` VALUES("31","Soda Water","L","28.750","5.000","40.00");
INSERT INTO `inventory_raw` VALUES("32","Mango Syrup","L","4.850","1.000","200.00");
INSERT INTO `inventory_raw` VALUES("33","Green Apple Syrup","L","5.000","1.000","200.00");
INSERT INTO `inventory_raw` VALUES("34","Plastic Cup (16oz)","pcs","487.000","100.000","2.50");
INSERT INTO `inventory_raw` VALUES("35","Plastic Cup (22oz)","pcs","500.000","100.000","3.00");
INSERT INTO `inventory_raw` VALUES("36","Straws","pcs","1000.000","200.000","0.50");


DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `name` (`name`),
  KEY `category_id` (`category_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `menu_items` VALUES("1","Espresso","1","35.00","1","","");
INSERT INTO `menu_items` VALUES("2","Brewed","1","40.00","1","","");
INSERT INTO `menu_items` VALUES("3","Americano (Hot)","1","45.00","1","","");
INSERT INTO `menu_items` VALUES("4","Long Black","1","45.00","1","","");
INSERT INTO `menu_items` VALUES("5","Cappuccino (Hot)","1","50.00","1","","");
INSERT INTO `menu_items` VALUES("6","Latte (Hot)","1","55.00","1","","");
INSERT INTO `menu_items` VALUES("7","Mocha (Hot)","1","65.00","1","","");
INSERT INTO `menu_items` VALUES("8","Iced Americano","2","45.00","1","","");
INSERT INTO `menu_items` VALUES("9","Cold Brew","2","45.00","1","","");
INSERT INTO `menu_items` VALUES("10","Iced Latte","2","60.00","1","","");
INSERT INTO `menu_items` VALUES("11","Iced Cappuccino","2","60.00","1","","");
INSERT INTO `menu_items` VALUES("12","Iced Mocha","2","65.00","1","","");
INSERT INTO `menu_items` VALUES("13","Caramel Macchiato","3","55.00","1","","");
INSERT INTO `menu_items` VALUES("14","Spanish Latte","3","55.00","1","","");
INSERT INTO `menu_items` VALUES("15","Mocha Latte","3","65.00","1","","");
INSERT INTO `menu_items` VALUES("16","White Mocha","3","65.00","1","","");
INSERT INTO `menu_items` VALUES("17","Matcha Green Tea Latte","3","65.00","1","","");
INSERT INTO `menu_items` VALUES("18","Shaken Lemon Lychee","3","65.00","1","","");
INSERT INTO `menu_items` VALUES("19","Hot Chocolate","3","40.00","1","","");
INSERT INTO `menu_items` VALUES("20","Hot Milk","3","40.00","1","","");
INSERT INTO `menu_items` VALUES("21","Ice Choco","3","55.00","1","","");
INSERT INTO `menu_items` VALUES("22","Black Forest MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("23","Chocolate MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("24","Cookies and Cream MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("25","Dark Choco MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("26","Matcha MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("27","Red Velvet MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("28","Taro MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("29","Wintermelon MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("30","Hokkaido MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("31","Okinawa MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("32","Panda Pearl MT","4","55.00","1","","");
INSERT INTO `menu_items` VALUES("33","Mango Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("34","Green Apple Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("35","Lychee Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("36","Strawberry Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("37","Passion Fruit Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("38","Melon Soda","5","60.00","1","","");
INSERT INTO `menu_items` VALUES("39","Mango IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("40","Vanilla-Cashew IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("41","Tablia Native Cacao IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("42","Coconut IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("43","Matcha IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("44","Black Sesame IC (100g)","7","50.00","1","","");
INSERT INTO `menu_items` VALUES("45","Coconut IC Bar (95g)","8","85.00","1","","");
INSERT INTO `menu_items` VALUES("46","Matcha IC Bar (95g)","8","85.00","1","","");
INSERT INTO `menu_items` VALUES("47","Milk-Cashew IC Bar (95g)","8","85.00","1","","");
INSERT INTO `menu_items` VALUES("48","Tablia Native Cacao IC Bar (95g)","8","85.00","1","","");
INSERT INTO `menu_items` VALUES("49","Cow Milk (350ml)","9","85.00","1","","");
INSERT INTO `menu_items` VALUES("50","Water Buffalo Milk (350ml)","9","90.00","1","","");
INSERT INTO `menu_items` VALUES("51","Chocolate (Cow) (350ml)","9","90.00","1","","");
INSERT INTO `menu_items` VALUES("52","Chocolate (Water Buffalo) (350ml)","9","95.00","1","","");
INSERT INTO `menu_items` VALUES("53","Matcha (Cow) (350ml)","9","105.00","1","","");
INSERT INTO `menu_items` VALUES("54","Matcha (Water Buffalo) (350ml)","9","110.00","1","","");
INSERT INTO `menu_items` VALUES("55","Mocha (Cow) (350ml)","9","105.00","1","","");
INSERT INTO `menu_items` VALUES("56","Mocha (Water Buffalo) (350ml)","9","115.00","1","","");


DROP TABLE IF EXISTS `modifier_inventory_links`;
CREATE TABLE `modifier_inventory_links` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `modifier_id` int(11) NOT NULL,
  `raw_id` int(11) NOT NULL,
  `quantity_consumed` decimal(10,3) NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `modifier_id` (`modifier_id`),
  KEY `raw_id` (`raw_id`),
  CONSTRAINT `modifier_inventory_links_ibfk_1` FOREIGN KEY (`modifier_id`) REFERENCES `modifiers` (`modifier_id`) ON DELETE CASCADE,
  CONSTRAINT `modifier_inventory_links_ibfk_2` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `modifier_inventory_links` VALUES("1","1","28","0.050");
INSERT INTO `modifier_inventory_links` VALUES("2","4","25","0.015");
INSERT INTO `modifier_inventory_links` VALUES("3","3","23","0.050");


DROP TABLE IF EXISTS `modifiers`;
CREATE TABLE `modifiers` (
  `modifier_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `display_type` enum('Add','Option','Upgrade') NOT NULL,
  `price_add` decimal(10,2) DEFAULT 0.00,
  `applicable_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`modifier_id`),
  UNIQUE KEY `name` (`name`),
  KEY `applicable_category_id` (`applicable_category_id`),
  CONSTRAINT `modifiers_ibfk_1` FOREIGN KEY (`applicable_category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `modifiers` VALUES("1","Pearls","Add","10.00","");
INSERT INTO `modifiers` VALUES("2","Coffee (Shot)","Add","10.00","");
INSERT INTO `modifiers` VALUES("3","Milk (Extra)","Add","10.00","");
INSERT INTO `modifiers` VALUES("4","Caramel Syrup","Add","10.00","");
INSERT INTO `modifiers` VALUES("5","Coffee Jelly","Add","10.00","");
INSERT INTO `modifiers` VALUES("6","Fruit Jelly","Add","10.00","");


DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_sale` decimal(10,2) NOT NULL,
  `modifiers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`modifiers`)),
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`),
  KEY `menu_item_id` (`menu_item_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` VALUES("1","1","6","1","55.00","");
INSERT INTO `order_items` VALUES("2","1","33","1","60.00","");
INSERT INTO `order_items` VALUES("3","2","32","1","65.00","{\"Pearls\": \"Extra\"}");
INSERT INTO `order_items` VALUES("4","3","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("5","4","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("6","5","11","1","70.00","[\"Coffee Jelly\"]");
INSERT INTO `order_items` VALUES("7","6","5","1","60.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("8","7","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("9","8","23","1","55.00","[]");
INSERT INTO `order_items` VALUES("10","8","24","1","55.00","[]");
INSERT INTO `order_items` VALUES("11","8","32","1","55.00","[]");
INSERT INTO `order_items` VALUES("12","8","25","1","55.00","[]");
INSERT INTO `order_items` VALUES("13","8","30","1","55.00","[]");
INSERT INTO `order_items` VALUES("14","9","3","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("15","10","14","1","55.00","[]");
INSERT INTO `order_items` VALUES("16","11","15","1","65.00","[]");
INSERT INTO `order_items` VALUES("17","12","4","1","55.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("18","13","2","1","50.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("19","14","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("20","15","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("21","16","5","1","60.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("22","17","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("23","18","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("24","19","6","1","65.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("25","20","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("26","21","30","1","55.00","[]");
INSERT INTO `order_items` VALUES("27","22","1","1","45.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("28","23","46","1","85.00","[]");
INSERT INTO `order_items` VALUES("29","24","2","1","60.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("30","25","5","1","60.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("31","26","5","1","60.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("32","27","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("33","28","39","1","50.00","[]");
INSERT INTO `order_items` VALUES("34","29","52","1","95.00","[]");
INSERT INTO `order_items` VALUES("35","30","3","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("36","31","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("37","32","9","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("38","33","8","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("39","34","3","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("40","35","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("41","36","3","1","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("42","37","3","1","55.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("43","38","42","1","50.00","[]");
INSERT INTO `order_items` VALUES("44","39","19","1","40.00","[]");
INSERT INTO `order_items` VALUES("45","40","13","1","55.00","[]");
INSERT INTO `order_items` VALUES("46","41","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("47","42","3","1","45.00","[]");
INSERT INTO `order_items` VALUES("48","43","14","1","55.00","[]");
INSERT INTO `order_items` VALUES("49","44","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("50","45","3","1","55.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("51","46","6","1","55.00","[]");
INSERT INTO `order_items` VALUES("52","46","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("53","47","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("54","48","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("55","49","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("56","50","46","1","85.00","[]");
INSERT INTO `order_items` VALUES("57","51","6","1","55.00","[]");
INSERT INTO `order_items` VALUES("58","52","3","1","45.00","[]");
INSERT INTO `order_items` VALUES("59","52","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("60","53","56","40","115.00","[]");
INSERT INTO `order_items` VALUES("61","54","5","3","50.00","[]");
INSERT INTO `order_items` VALUES("62","54","4","2","45.00","[]");
INSERT INTO `order_items` VALUES("63","55","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("64","56","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("65","57","4","6","45.00","[]");
INSERT INTO `order_items` VALUES("66","58","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("67","59","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("68","60","5","21","50.00","[]");
INSERT INTO `order_items` VALUES("69","61","26","24","65.00","[\"Pearls\"]");
INSERT INTO `order_items` VALUES("70","62","7","100","75.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("71","63","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("72","64","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("73","65","19","1","40.00","[]");
INSERT INTO `order_items` VALUES("74","65","18","1","65.00","[]");
INSERT INTO `order_items` VALUES("75","66","2","1","50.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("76","66","46","1","85.00","[]");
INSERT INTO `order_items` VALUES("77","67","5","1","60.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("78","68","3","6","55.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("79","69","3","6","55.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("80","70","6","2","75.00","[\"Caramel Syrup\",\"Milk\"]");
INSERT INTO `order_items` VALUES("81","70","4","1","65.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("82","71","5","1","70.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("83","71","2","1","60.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("84","72","19","31","40.00","[]");
INSERT INTO `order_items` VALUES("85","72","20","10","40.00","[]");
INSERT INTO `order_items` VALUES("86","72","15","10","65.00","[]");
INSERT INTO `order_items` VALUES("87","73","33","5","60.00","[]");
INSERT INTO `order_items` VALUES("88","73","41","3","50.00","[]");
INSERT INTO `order_items` VALUES("89","74","40","1","50.00","[]");
INSERT INTO `order_items` VALUES("90","75","5","57","60.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("91","76","16","1","65.00","[]");
INSERT INTO `order_items` VALUES("92","76","36","1","60.00","[]");
INSERT INTO `order_items` VALUES("93","76","55","1","105.00","[]");
INSERT INTO `order_items` VALUES("94","77","2","200","50.00","[\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("95","78","18","10","65.00","[]");
INSERT INTO `order_items` VALUES("96","79","10","4","70.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("97","80","3","10","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("98","81","3","2","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("99","82","3","2","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("100","83","52","1","95.00","[]");
INSERT INTO `order_items` VALUES("101","84","3","3","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("102","85","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("103","86","44","12","50.00","[]");
INSERT INTO `order_items` VALUES("104","87","30","1","75.00","[\"Pearls\",\"Coffee Jelly\"]");
INSERT INTO `order_items` VALUES("105","88","13","1","55.00","[]");
INSERT INTO `order_items` VALUES("106","88","6","1","75.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("107","88","12","1","85.00","[\"Milk\",\"Coffee Jelly\"]");
INSERT INTO `order_items` VALUES("108","88","49","1","85.00","[]");
INSERT INTO `order_items` VALUES("109","89","1","1","35.00","[]");
INSERT INTO `order_items` VALUES("110","90","3","10","55.00","[\"Milk\"]");
INSERT INTO `order_items` VALUES("111","91","3","1","65.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("112","92","44","12","50.00","[]");
INSERT INTO `order_items` VALUES("113","93","3","1","45.00","[]");
INSERT INTO `order_items` VALUES("114","94","3","1","45.00","[]");
INSERT INTO `order_items` VALUES("115","95","2","1","60.00","[\"Milk\",\"Caramel Syrup\"]");
INSERT INTO `order_items` VALUES("116","95","4","1","45.00","[]");
INSERT INTO `order_items` VALUES("117","96","5","5","50.00","[]");
INSERT INTO `order_items` VALUES("118","97","35","1","60.00","[]");
INSERT INTO `order_items` VALUES("119","98","2","1","40.00","[]");
INSERT INTO `order_items` VALUES("120","99","5","1","50.00","[]");
INSERT INTO `order_items` VALUES("121","100","2","1","40.00","[]");


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `time_paid` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `pre_order_code` (`pre_order_code`),
  UNIQUE KEY `final_code` (`final_code`),
  KEY `cashier_id` (`cashier_id`),
  KEY `shift_id` (`shift_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`shift_id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `orders` VALUES("1","PRE-001","OR-1001","Manual_POS","115.00","SERVED","2","","","1","2025-12-15 22:26:46","");
INSERT INTO `orders` VALUES("2","PRE-002","OR-1002","Kiosk","65.00","","2","","","1","2025-12-15 22:26:46","2026-02-10 14:00:18");
INSERT INTO `orders` VALUES("3","383A","","Kiosk","45.00","CANCELLED","","218102","","","2026-02-10 13:26:54","");
INSERT INTO `orders` VALUES("4","051Z","","Kiosk","50.00","CANCELLED","","20211537","","","2026-02-10 13:38:51","");
INSERT INTO `orders` VALUES("5","116S","","Kiosk","70.00","SERVED","","20211537","","","2026-02-10 13:58:28","2026-02-10 13:59:01");
INSERT INTO `orders` VALUES("6","077F","","Kiosk","60.00","CANCELLED","","218102","","","2026-02-10 13:59:33","2026-02-10 14:00:08");
INSERT INTO `orders` VALUES("7","662C","","Kiosk","40.00","SERVED","","20211537","","","2026-02-10 14:01:31","2026-02-10 14:02:05");
INSERT INTO `orders` VALUES("8","736N","","Kiosk","275.00","CANCELLED","","20200173","","","2026-02-10 15:21:49","");
INSERT INTO `orders` VALUES("9","248Z","","Kiosk","55.00","CANCELLED","","20190412","","","2026-02-10 15:34:12","");
INSERT INTO `orders` VALUES("10","603P","","Kiosk","55.00","CANCELLED","","GUEST","","","2026-02-10 15:52:32","");
INSERT INTO `orders` VALUES("11","108Q","","Kiosk","65.00","SERVED","","225019","","","2026-02-10 15:56:06","2026-02-10 15:57:05");
INSERT INTO `orders` VALUES("12","217S","","Kiosk","55.00","CANCELLED","","20190412","","","2026-02-10 17:04:42","");
INSERT INTO `orders` VALUES("13","688A","","Kiosk","50.00","CANCELLED","","20190412","","","2026-02-10 17:05:04","");
INSERT INTO `orders` VALUES("14","081O","","Kiosk","45.00","CANCELLED","","GUEST","","","2026-02-10 18:49:43","");
INSERT INTO `orders` VALUES("15","994Y","","Kiosk","50.00","CANCELLED","","GUEST","","","2026-02-10 19:01:16","");
INSERT INTO `orders` VALUES("16","608S","","Kiosk","60.00","CANCELLED","","GUEST","","","2026-02-10 19:09:17","");
INSERT INTO `orders` VALUES("17","557T","","Kiosk","50.00","CANCELLED","","GUEST","","","2026-02-10 19:10:09","");
INSERT INTO `orders` VALUES("18","534X","","Kiosk","45.00","CANCELLED","","GUEST","","","2026-02-10 19:10:22","");
INSERT INTO `orders` VALUES("19","694R","","Kiosk","65.00","SERVED","","20152161","","","2026-02-10 19:14:39","2026-02-10 19:15:36");
INSERT INTO `orders` VALUES("20","375L","","Kiosk","50.00","SERVED","","GUEST","Cyfer Hogarth","","2026-02-10 19:37:45","2026-02-10 20:58:56");
INSERT INTO `orders` VALUES("21","819G","","Kiosk","55.00","SERVED","","20211537","EJ TRUNO BANTAYANON","","2026-02-10 19:40:38","2026-02-11 11:01:43");
INSERT INTO `orders` VALUES("22","925I","","Kiosk","45.00","SERVED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-10 20:53:47","2026-02-11 10:10:19");
INSERT INTO `orders` VALUES("23","779O","","Kiosk","85.00","CANCELLED","","20180724","DEAN LOUIE RAMIREZ ARAULA","","2026-02-11 09:41:02","");
INSERT INTO `orders` VALUES("24","699C","","Kiosk","60.00","SERVED","","20180724","DEAN LOUIE RAMIREZ ARAULA","","2026-02-11 09:41:12","2026-02-11 09:41:59");
INSERT INTO `orders` VALUES("25","062V","","Kiosk","60.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-11 10:50:47","");
INSERT INTO `orders` VALUES("26","904R","","Kiosk","60.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-11 11:00:04","");
INSERT INTO `orders` VALUES("27","698L","","Kiosk","40.00","CANCELLED","","GUEST","tristan","","2026-02-11 11:31:16","");
INSERT INTO `orders` VALUES("28","905M","","Kiosk","50.00","CANCELLED","","20211537","","","2026-02-11 13:52:24","");
INSERT INTO `orders` VALUES("29","523T","","Kiosk","95.00","CANCELLED","","20211537","","","2026-02-11 13:53:11","");
INSERT INTO `orders` VALUES("30","289I","","Kiosk","55.00","CANCELLED","","20190412","","","2026-02-11 14:09:13","");
INSERT INTO `orders` VALUES("31","029X","","Kiosk","50.00","CANCELLED","","20190412","","","2026-02-11 15:22:56","");
INSERT INTO `orders` VALUES("32","078A","","Kiosk","55.00","SERVED","","20190412","","","2026-02-11 15:23:08","2026-02-11 15:24:15");
INSERT INTO `orders` VALUES("33","826L","","Kiosk","55.00","CANCELLED","","20211537","","","2026-02-11 17:18:32","");
INSERT INTO `orders` VALUES("34","165V","","Kiosk","55.00","CANCELLED","","GUEST","","","2026-02-11 17:26:34","");
INSERT INTO `orders` VALUES("35","560C","","Kiosk","40.00","CANCELLED","","20211537","","","2026-02-11 17:36:59","");
INSERT INTO `orders` VALUES("36","116T","","Kiosk","55.00","CANCELLED","","GUEST","","","2026-02-11 18:24:00","");
INSERT INTO `orders` VALUES("37","818C","","Kiosk","55.00","CANCELLED","","20220120","","","2026-02-11 18:32:57","");
INSERT INTO `orders` VALUES("38","148P","","Kiosk","50.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 18:41:05","");
INSERT INTO `orders` VALUES("39","610I","","Kiosk","40.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 18:41:48","");
INSERT INTO `orders` VALUES("40","391P","","Kiosk","55.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 18:52:34","");
INSERT INTO `orders` VALUES("41","969B","","Kiosk","40.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:53:25","");
INSERT INTO `orders` VALUES("42","564M","","Kiosk","45.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:55:32","");
INSERT INTO `orders` VALUES("43","485Y","","Kiosk","55.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 18:56:14","2026-02-11 19:01:01");
INSERT INTO `orders` VALUES("44","655B","","Kiosk","50.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:32","");
INSERT INTO `orders` VALUES("45","455C","","Kiosk","55.00","SERVED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 18:58:34","2026-02-11 19:00:45");
INSERT INTO `orders` VALUES("46","295O","","Kiosk","100.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:40","");
INSERT INTO `orders` VALUES("47","124D","","Kiosk","45.00","SERVED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:42","2026-02-11 19:00:35");
INSERT INTO `orders` VALUES("48","747V","","Kiosk","45.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:47","");
INSERT INTO `orders` VALUES("49","541L","","Kiosk","40.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:50","");
INSERT INTO `orders` VALUES("50","907L","","Kiosk","85.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 18:58:58","");
INSERT INTO `orders` VALUES("51","145V","","Kiosk","55.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 19:01:53","");
INSERT INTO `orders` VALUES("52","461W","","Kiosk","90.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:05:34","");
INSERT INTO `orders` VALUES("53","626J","","Kiosk","4600.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:35","");
INSERT INTO `orders` VALUES("54","375A","","Kiosk","240.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:43","");
INSERT INTO `orders` VALUES("55","108A","","Kiosk","50.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:46","2026-02-16 15:49:08");
INSERT INTO `orders` VALUES("56","777I","","Kiosk","45.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:48","2026-02-16 15:48:58");
INSERT INTO `orders` VALUES("57","227F","","Kiosk","270.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:52","");
INSERT INTO `orders` VALUES("58","091W","","Kiosk","45.00","SERVED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:56","2026-02-11 19:18:18");
INSERT INTO `orders` VALUES("59","693U","","Kiosk","45.00","SERVED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-11 19:06:56","2026-02-11 19:26:46");
INSERT INTO `orders` VALUES("60","343T","","Kiosk","1050.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 19:09:34","");
INSERT INTO `orders` VALUES("61","699S","","Kiosk","1560.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 19:26:44","");
INSERT INTO `orders` VALUES("62","331K","","Kiosk","7500.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-11 19:33:46","");
INSERT INTO `orders` VALUES("63","322M","","Kiosk","50.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-13 10:17:44","2026-02-13 10:18:14");
INSERT INTO `orders` VALUES("64","614G","","Kiosk","40.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-13 12:51:00","");
INSERT INTO `orders` VALUES("65","354A","","Kiosk","105.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-13 15:19:40","");
INSERT INTO `orders` VALUES("66","206D","","Kiosk","135.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-18 13:37:10","2026-02-20 08:47:40");
INSERT INTO `orders` VALUES("67","031L","","Kiosk","60.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-18 14:50:26","2026-02-18 14:51:42");
INSERT INTO `orders` VALUES("68","090B","","Kiosk","330.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-20 09:51:57","");
INSERT INTO `orders` VALUES("69","063Y","","Kiosk","330.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-20 09:51:57","");
INSERT INTO `orders` VALUES("70","236I","","Kiosk","215.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-20 10:08:32","");
INSERT INTO `orders` VALUES("71","499B","","Kiosk","130.00","SERVED","","20211537","EJ TRUNO BANTAYANON","","2026-02-20 10:29:21","2026-02-20 10:54:37");
INSERT INTO `orders` VALUES("72","198D","","Kiosk","2290.00","CANCELLED","","20200173","FELIX CONSTANTINO JR. PIS-AN CATA-AL","","2026-02-24 14:32:22","");
INSERT INTO `orders` VALUES("73","090G","","Kiosk","450.00","CANCELLED","","20230182","MARK JOSEPH MIRO FERNANDEZ","","2026-02-24 17:25:52","");
INSERT INTO `orders` VALUES("74","883I","","Kiosk","50.00","CANCELLED","","20211670","RAINER NULLA DIZON","","2026-02-24 18:49:45","");
INSERT INTO `orders` VALUES("75","934J","","Kiosk","3420.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-24 19:06:42","");
INSERT INTO `orders` VALUES("76","494E","","Kiosk","230.00","SERVED","","20220359","LEILAH JANE BALANSAG OSTRIA","","2026-02-24 19:37:23","2026-02-24 19:55:54");
INSERT INTO `orders` VALUES("77","391L","","Kiosk","10000.00","CANCELLED","","20220120","JHON HURTZ PALUCA DAUG","","2026-02-24 19:58:37","");
INSERT INTO `orders` VALUES("78","823H","","Kiosk","650.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-24 20:27:03","");
INSERT INTO `orders` VALUES("79","588Z","","Kiosk","280.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-25 08:36:21","");
INSERT INTO `orders` VALUES("80","954W","","Kiosk","550.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 09:23:35","");
INSERT INTO `orders` VALUES("81","746D","","Kiosk","110.00","SERVED","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 09:30:07","2026-02-25 09:31:13");
INSERT INTO `orders` VALUES("82","382N","","Kiosk","110.00","SERVED","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 09:49:16","2026-02-25 09:50:07");
INSERT INTO `orders` VALUES("83","180A","","Kiosk","95.00","CANCELLED","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 09:55:06","");
INSERT INTO `orders` VALUES("84","119E","","Kiosk","165.00","SERVED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-25 12:55:50","2026-02-25 12:56:28");
INSERT INTO `orders` VALUES("85","250F","","Kiosk","40.00","CANCELLED","","20190412","HOGARTH CYFER BUENAVENTURA CATIPAY","","2026-02-25 12:59:19","2026-02-25 13:58:11");
INSERT INTO `orders` VALUES("86","887A","","Kiosk","600.00","SERVED","","20200173","FELIX CONSTANTINO JR. PIS-AN CATA-AL","","2026-02-25 13:16:17","2026-02-25 14:13:30");
INSERT INTO `orders` VALUES("87","106N","","Kiosk","75.00","CANCELLED","","20200173","FELIX CONSTANTINO JR. PIS-AN CATA-AL","","2026-02-25 13:16:48","");
INSERT INTO `orders` VALUES("88","769E","","Kiosk","300.00","SERVED","","20212157","EDRIAN SOMOZA SANTAYO","","2026-02-25 13:52:24","2026-02-25 13:54:21");
INSERT INTO `orders` VALUES("89","127S","","Kiosk","35.00","SERVED","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 13:57:13","2026-02-25 13:57:58");
INSERT INTO `orders` VALUES("90","706G","","Kiosk","550.00","PENDING PAYMENT","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 14:19:06","");
INSERT INTO `orders` VALUES("91","839M","","Kiosk","65.00","SERVED","","20130685","ANDREW VILLALON MORES","","2026-02-25 14:21:41","2026-02-25 14:22:10");
INSERT INTO `orders` VALUES("92","949I","","Kiosk","600.00","PENDING PAYMENT","","20200173","FELIX CONSTANTINO JR. PIS-AN CATA-AL","","2026-02-25 14:28:32","");
INSERT INTO `orders` VALUES("93","397Z","","Kiosk","45.00","PENDING PAYMENT","","20211537","EJ TRUNO BANTAYANON","","2026-02-25 14:47:12","");
INSERT INTO `orders` VALUES("94","255E","","Kiosk","45.00","CANCELLED","","GUEST-1772136579752","Guest Customer","","2026-02-27 04:09:54","2026-02-27 04:51:21");
INSERT INTO `orders` VALUES("95","467Q","","Kiosk","105.00","CANCELLED","","GUEST-1772136579752","Guest Customer","","2026-02-27 04:12:01","");
INSERT INTO `orders` VALUES("96","322F","","","250.00","SERVED","","GUEST-1772137228641","Guest Customer","","2026-02-27 04:25:01","2026-02-27 04:46:42");
INSERT INTO `orders` VALUES("97","042G","","","60.00","SERVED","","GUEST-1772137228641","Guest Customer","","2026-02-27 04:26:03","2026-02-27 04:37:57");
INSERT INTO `orders` VALUES("98","119O","","","40.00","READY","","GUEST-1772138962465","Guest Customer","","2026-02-27 04:49:39","2026-02-27 04:49:53");
INSERT INTO `orders` VALUES("99","928Q","","","50.00","READY","","GUEST-1772138962465","Guest Customer","","2026-02-27 04:53:24","2026-02-27 04:53:42");
INSERT INTO `orders` VALUES("100","830E","","","40.00","CANCELLED","","GUEST-1772140861450","Guest Customer","","2026-02-27 05:21:21","");


DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('Cash','GCash','Maya') DEFAULT 'Cash',
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `payment_time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `payments` VALUES("1","1","Cash","115.00","","2025-12-15 22:26:46");
INSERT INTO `payments` VALUES("2","2","GCash","65.00","GCASH-REF-998877","2025-12-15 22:26:46");
INSERT INTO `payments` VALUES("3","5","Cash","100.00","","2026-02-10 13:59:01");
INSERT INTO `payments` VALUES("4","6","Cash","100.00","","2026-02-10 14:00:08");
INSERT INTO `payments` VALUES("5","7","Cash","50.00","","2026-02-10 14:02:05");
INSERT INTO `payments` VALUES("6","11","Cash","100.00","","2026-02-10 15:57:05");
INSERT INTO `payments` VALUES("7","19","Cash","66.00","","2026-02-10 19:15:36");
INSERT INTO `payments` VALUES("8","20","Cash","100.00","","2026-02-10 20:58:56");
INSERT INTO `payments` VALUES("9","24","Cash","100.00","","2026-02-11 09:41:59");
INSERT INTO `payments` VALUES("10","22","Cash","50.00","","2026-02-11 10:10:19");
INSERT INTO `payments` VALUES("11","21","Cash","55.00","","2026-02-11 11:01:43");
INSERT INTO `payments` VALUES("12","32","Cash","55.00","","2026-02-11 15:24:15");
INSERT INTO `payments` VALUES("13","47","Cash","50.00","","2026-02-11 19:00:35");
INSERT INTO `payments` VALUES("14","45","Cash","60.00","","2026-02-11 19:00:45");
INSERT INTO `payments` VALUES("15","43","Cash","60.00","","2026-02-11 19:01:01");
INSERT INTO `payments` VALUES("16","58","Cash","50.00","","2026-02-11 19:18:18");
INSERT INTO `payments` VALUES("17","59","Cash","50.00","","2026-02-11 19:26:46");
INSERT INTO `payments` VALUES("18","63","Cash","50.00","","2026-02-13 10:18:14");
INSERT INTO `payments` VALUES("19","56","Cash","50.00","","2026-02-16 15:48:58");
INSERT INTO `payments` VALUES("20","55","Cash","100.00","","2026-02-16 15:49:08");
INSERT INTO `payments` VALUES("21","67","Cash","100.00","","2026-02-18 14:51:42");
INSERT INTO `payments` VALUES("22","66","Cash","200.00","","2026-02-20 08:47:40");
INSERT INTO `payments` VALUES("23","71","Cash","200.00","","2026-02-20 10:54:37");
INSERT INTO `payments` VALUES("24","76","Cash","300.00","","2026-02-24 19:55:54");
INSERT INTO `payments` VALUES("25","81","Cash","110.00","","2026-02-25 09:31:13");
INSERT INTO `payments` VALUES("26","82","Cash","200.00","","2026-02-25 09:50:07");
INSERT INTO `payments` VALUES("27","84","Cash","165.00","","2026-02-25 12:56:28");
INSERT INTO `payments` VALUES("28","88","Cash","300.00","","2026-02-25 13:54:21");
INSERT INTO `payments` VALUES("29","89","Cash","50.00","","2026-02-25 13:57:58");
INSERT INTO `payments` VALUES("30","85","Cash","50.00","","2026-02-25 13:58:11");
INSERT INTO `payments` VALUES("31","86","Cash","700.00","","2026-02-25 14:13:30");
INSERT INTO `payments` VALUES("32","91","Cash","100.00","","2026-02-25 14:22:10");
INSERT INTO `payments` VALUES("33","97","Cash","70.00","","2026-02-27 04:37:57");
INSERT INTO `payments` VALUES("34","96","Cash","300.00","","2026-02-27 04:46:42");
INSERT INTO `payments` VALUES("35","98","Cash","50.00","","2026-02-27 04:49:53");
INSERT INTO `payments` VALUES("36","94","Cash","50.00","","2026-02-27 04:51:21");
INSERT INTO `payments` VALUES("37","99","Cash","100.00","","2026-02-27 04:53:42");


DROP TABLE IF EXISTS `recipes`;
CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_item_id` int(11) NOT NULL,
  `raw_id` int(11) NOT NULL,
  `quantity_consumed` decimal(10,3) NOT NULL,
  PRIMARY KEY (`recipe_id`),
  KEY `menu_item_id` (`menu_item_id`),
  KEY `raw_id` (`raw_id`),
  CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`item_id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`raw_id`) REFERENCES `inventory_raw` (`raw_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `recipes` VALUES("1","10","4","0.020");
INSERT INTO `recipes` VALUES("2","10","1","0.040");
INSERT INTO `recipes` VALUES("3","6","4","0.018");
INSERT INTO `recipes` VALUES("4","6","23","0.200");
INSERT INTO `recipes` VALUES("5","6","34","1.000");
INSERT INTO `recipes` VALUES("6","8","4","0.018");
INSERT INTO `recipes` VALUES("7","8","34","1.000");
INSERT INTO `recipes` VALUES("8","32","2","0.010");
INSERT INTO `recipes` VALUES("9","32","28","0.050");
INSERT INTO `recipes` VALUES("10","32","34","1.000");
INSERT INTO `recipes` VALUES("11","33","31","0.250");
INSERT INTO `recipes` VALUES("12","33","32","0.030");
INSERT INTO `recipes` VALUES("13","33","34","1.000");


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `settings` VALUES("maintenance_mode","false","2026-02-13 15:22:40");
INSERT INTO `settings` VALUES("service_charge","0.00","2026-02-13 15:22:40");
INSERT INTO `settings` VALUES("store_name","GrubHound","2026-02-13 15:22:40");
INSERT INTO `settings` VALUES("tax_rate","0.00","2026-02-13 15:22:40");


DROP TABLE IF EXISTS `shifts`;
CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `starting_cash` decimal(10,2) DEFAULT 0.00,
  `expected_cash` decimal(10,2) DEFAULT 0.00,
  `actual_cash` decimal(10,2) DEFAULT 0.00,
  `discrepancy` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`shift_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `shifts` VALUES("1","2","2025-12-15 22:26:39","","1000.00","1000.00","1000.00","0.00");


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('Admin','Cashier','Barista') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES("1","admin01","$2y$10$rKzl/vBo9p1HbEacI1/KNu0fP45OlcsZ3GuutyQMr46mDVX8TDOOG","Erika Cruz","Admin","2025-12-15 22:23:10");
INSERT INTO `users` VALUES("2","cashier01","$2y$10$qRxPcOxL2QF8iAspNdnl3uehqdacZd.RQSidpoZtc4mo/iuHy/sdG","James Dee","Cashier","2025-12-15 22:23:10");


