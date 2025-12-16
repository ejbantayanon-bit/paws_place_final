-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: paws_place_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Hot Coffee',1,1),(2,'Cold Coffee',1,2),(3,'Specialty Drinks (Hot/Cold)',1,3),(4,'Milk Tea',1,4),(5,'Fruity Soda',1,5),(6,'Add Ons',1,6),(7,'Ice Cream in Cups (100g)',1,7),(8,'Ice Cream Bar (95g)',1,8),(9,'Milk Drink (350ml)',1,9);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_logs`
--

DROP TABLE IF EXISTS `inventory_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_logs`
--

LOCK TABLES `inventory_logs` WRITE;
/*!40000 ALTER TABLE `inventory_logs` DISABLE KEYS */;
INSERT INTO `inventory_logs` VALUES (1,1,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(2,2,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(3,3,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(4,4,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(5,5,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(6,6,1,0.000,'Initial Stock In','2025-12-15 14:26:53'),(7,23,1,50.000,'Initial Stock In','2025-12-15 14:26:53'),(8,24,1,10.000,'Initial Stock In','2025-12-15 14:26:53'),(9,25,1,10.000,'Initial Stock In','2025-12-15 14:26:53'),(10,26,1,20.000,'Initial Stock In','2025-12-15 14:26:53'),(11,27,1,20.000,'Initial Stock In','2025-12-15 14:26:53'),(12,28,1,15.000,'Initial Stock In','2025-12-15 14:26:53'),(13,29,1,5.000,'Initial Stock In','2025-12-15 14:26:53'),(14,30,1,3.000,'Initial Stock In','2025-12-15 14:26:53'),(15,31,1,30.000,'Initial Stock In','2025-12-15 14:26:53'),(16,32,1,5.000,'Initial Stock In','2025-12-15 14:26:53'),(17,33,1,5.000,'Initial Stock In','2025-12-15 14:26:53'),(18,34,1,500.000,'Initial Stock In','2025-12-15 14:26:53'),(19,35,1,500.000,'Initial Stock In','2025-12-15 14:26:53'),(20,36,1,1000.000,'Initial Stock In','2025-12-15 14:26:53');
/*!40000 ALTER TABLE `inventory_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_raw`
--

DROP TABLE IF EXISTS `inventory_raw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_raw`
--

LOCK TABLES `inventory_raw` WRITE;
/*!40000 ALTER TABLE `inventory_raw` DISABLE KEYS */;
INSERT INTO `inventory_raw` VALUES (1,'Milk Powder (Full Cream)','kg',0.000,5.000,0.00),(2,'Black Tea Leaves','kg',0.000,3.000,0.00),(3,'Sugar Syrup Base','L',0.000,10.000,0.00),(4,'Espresso Coffee Beans','kg',0.000,5.000,0.00),(5,'Pearl Tapioca','kg',0.000,5.000,0.00),(6,'Caramel Syrup Concentrate','L',0.000,2.000,0.00),(23,'Full Cream Milk','L',50.000,10.000,90.00),(24,'Chocolate Syrup','L',10.000,2.000,250.00),(25,'Caramel Syrup','L',10.000,2.000,250.00),(26,'Milk Tea Creamer','kg',20.000,5.000,150.00),(27,'Fructose Syrup','L',20.000,5.000,100.00),(28,'Tapioca Pearls (Raw)','kg',15.000,3.000,120.00),(29,'Taro Powder','kg',5.000,1.000,300.00),(30,'Matcha Powder','kg',3.000,0.500,800.00),(31,'Soda Water','L',30.000,5.000,40.00),(32,'Mango Syrup','L',5.000,1.000,200.00),(33,'Green Apple Syrup','L',5.000,1.000,200.00),(34,'Plastic Cup (16oz)','pcs',500.000,100.000,2.50),(35,'Plastic Cup (22oz)','pcs',500.000,100.000,3.00),(36,'Straws','pcs',1000.000,200.000,0.50);
/*!40000 ALTER TABLE `inventory_raw` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL,
  `base_price` decimal(10,2) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `name` (`name`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,'Espresso',1,35.00,1,NULL),(2,'Brewed',1,40.00,1,NULL),(3,'Americano (Hot)',1,45.00,1,NULL),(4,'Long Black',1,45.00,1,NULL),(5,'Cappuccino (Hot)',1,50.00,1,NULL),(6,'Latte (Hot)',1,55.00,1,NULL),(7,'Mocha (Hot)',1,65.00,1,NULL),(8,'Iced Americano',2,45.00,1,NULL),(9,'Cold Brew',2,45.00,1,NULL),(10,'Iced Latte',2,60.00,1,NULL),(11,'Iced Cappuccino',2,60.00,1,NULL),(12,'Iced Mocha',2,65.00,1,NULL),(13,'Caramel Macchiato',3,55.00,1,NULL),(14,'Spanish Latte',3,55.00,1,NULL),(15,'Mocha Latte',3,65.00,1,NULL),(16,'White Mocha',3,65.00,1,NULL),(17,'Matcha Green Tea Latte',3,65.00,1,NULL),(18,'Shaken Lemon Lychee',3,65.00,1,NULL),(19,'Hot Chocolate',3,40.00,1,NULL),(20,'Hot Milk',3,40.00,1,NULL),(21,'Ice Choco',3,55.00,1,NULL),(22,'Black Forest MT',4,55.00,1,NULL),(23,'Chocolate MT',4,55.00,1,NULL),(24,'Cookies and Cream MT',4,55.00,1,NULL),(25,'Dark Choco MT',4,55.00,1,NULL),(26,'Matcha MT',4,55.00,1,NULL),(27,'Red Velvet MT',4,55.00,1,NULL),(28,'Taro MT',4,55.00,1,NULL),(29,'Wintermelon MT',4,55.00,1,NULL),(30,'Hokkaido MT',4,55.00,1,NULL),(31,'Okinawa MT',4,55.00,1,NULL),(32,'Panda Pearl MT',4,55.00,1,NULL),(33,'Mango Soda',5,60.00,1,NULL),(34,'Green Apple Soda',5,60.00,1,NULL),(35,'Lychee Soda',5,60.00,1,NULL),(36,'Strawberry Soda',5,60.00,1,NULL),(37,'Passion Fruit Soda',5,60.00,1,NULL),(38,'Melon Soda',5,60.00,1,NULL),(39,'Mango IC (100g)',7,50.00,1,NULL),(40,'Vanilla-Cashew IC (100g)',7,50.00,1,NULL),(41,'Tablia Native Cacao IC (100g)',7,50.00,1,NULL),(42,'Coconut IC (100g)',7,50.00,1,NULL),(43,'Matcha IC (100g)',7,50.00,1,NULL),(44,'Black Sesame IC (100g)',7,50.00,1,NULL),(45,'Coconut IC Bar (95g)',8,85.00,1,NULL),(46,'Matcha IC Bar (95g)',8,85.00,1,NULL),(47,'Milk-Cashew IC Bar (95g)',8,85.00,1,NULL),(48,'Tablia Native Cacao IC Bar (95g)',8,85.00,1,NULL),(49,'Cow Milk (350ml)',9,85.00,1,NULL),(50,'Water Buffalo Milk (350ml)',9,90.00,1,NULL),(51,'Chocolate (Cow) (350ml)',9,90.00,1,NULL),(52,'Chocolate (Water Buffalo) (350ml)',9,95.00,1,NULL),(53,'Matcha (Cow) (350ml)',9,105.00,1,NULL),(54,'Matcha (Water Buffalo) (350ml)',9,110.00,1,NULL),(55,'Mocha (Cow) (350ml)',9,105.00,1,NULL),(56,'Mocha (Water Buffalo) (350ml)',9,115.00,1,NULL);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modifier_inventory_links`
--

DROP TABLE IF EXISTS `modifier_inventory_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modifier_inventory_links`
--

LOCK TABLES `modifier_inventory_links` WRITE;
/*!40000 ALTER TABLE `modifier_inventory_links` DISABLE KEYS */;
INSERT INTO `modifier_inventory_links` VALUES (1,1,28,0.050),(2,4,25,0.015),(3,3,23,0.050);
/*!40000 ALTER TABLE `modifier_inventory_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modifiers`
--

DROP TABLE IF EXISTS `modifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modifiers`
--

LOCK TABLES `modifiers` WRITE;
/*!40000 ALTER TABLE `modifiers` DISABLE KEYS */;
INSERT INTO `modifiers` VALUES (1,'Pearls','Add',10.00,NULL),(2,'Coffee (Shot)','Add',10.00,NULL),(3,'Milk (Extra)','Add',10.00,NULL),(4,'Caramel Syrup','Add',10.00,NULL),(5,'Coffee Jelly','Add',10.00,NULL),(6,'Fruit Jelly','Add',10.00,NULL);
/*!40000 ALTER TABLE `modifiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,6,1,55.00,NULL),(2,1,33,1,60.00,NULL),(3,2,32,1,65.00,'{\"Pearls\": \"Extra\"}');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `pre_order_code` varchar(20) DEFAULT NULL,
  `final_code` varchar(20) DEFAULT NULL,
  `order_source` enum('Kiosk','Manual_POS') NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('PENDING PAYMENT','PREPARING','READY','SERVED','CANCELLED') DEFAULT 'PENDING PAYMENT',
  `cashier_id` int(11) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'PRE-001','OR-1001','Manual_POS',115.00,'SERVED',2,1,'2025-12-15 14:26:46',NULL),(2,'PRE-002','OR-1002','Kiosk',65.00,'READY',2,1,'2025-12-15 14:26:46',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'Cash',115.00,NULL,'2025-12-15 14:26:46'),(2,2,'GCash',65.00,'GCASH-REF-998877','2025-12-15 14:26:46');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
INSERT INTO `recipes` VALUES (1,10,4,0.020),(2,10,1,0.040),(3,6,4,0.018),(4,6,23,0.200),(5,6,34,1.000),(6,8,4,0.018),(7,8,34,1.000),(8,32,2,0.010),(9,32,28,0.050),(10,32,34,1.000),(11,33,31,0.250),(12,33,32,0.030),(13,33,34,1.000);
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shifts`
--

DROP TABLE IF EXISTS `shifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shifts`
--

LOCK TABLES `shifts` WRITE;
/*!40000 ALTER TABLE `shifts` DISABLE KEYS */;
INSERT INTO `shifts` VALUES (1,2,'2025-12-15 14:26:39',NULL,1000.00,1000.00,1000.00,0.00);
/*!40000 ALTER TABLE `shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin01','hashed_password_admin','Erika Cruz','Admin','2025-12-15 14:23:10'),(2,'cashier01','hashed_password_cashier','James Dee','Cashier','2025-12-15 14:23:10');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-12-15 23:23:43
