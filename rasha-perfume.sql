CREATE DATABASE  IF NOT EXISTS `rasha_perfume` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `rasha_perfume`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: rasha_perfume
-- ------------------------------------------------------
-- Server version	8.4.5

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,15,2,90.00),(2,2,20,3,200.00),(3,3,16,1,75.00),(4,4,18,1,155.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `order_summary_view`
--

DROP TABLE IF EXISTS `order_summary_view`;
/*!50001 DROP VIEW IF EXISTS `order_summary_view`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `order_summary_view` AS SELECT 
 1 AS `order_id`,
 1 AS `user_id`,
 1 AS `customer_name`,
 1 AS `customer_email`,
 1 AS `total_amount`,
 1 AS `order_status`,
 1 AS `shipping_address`,
 1 AS `phone`,
 1 AS `order_date`,
 1 AS `payment_method`,
 1 AS `card_number_last4`,
 1 AS `payment_amount`,
 1 AS `payment_status`,
 1 AS `transaction_id`,
 1 AS `payment_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,3,180.00,'pending','3 Ahmed Ayoub street','01007545813','2026-05-02 11:57:42'),(2,4,600.00,'pending','3 Ahmed Ayoub street','01007545813','2026-05-02 18:49:19'),(3,5,75.00,'pending','3 Ahmed Ayoub street','01007545813','2026-05-02 18:52:03'),(4,5,155.00,'pending','3 Ahmed Ayoub street','01007545813','2026-05-02 19:02:40');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` int NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50) NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `method_name` (`method_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'credit_card','Credit Card',1,'2026-05-02 11:51:42'),(2,'debit_card','Debit Card',1,'2026-05-02 11:51:42');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `card_number_last4` varchar(4) DEFAULT NULL,
  `card_holder_name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_payments_order_id` (`order_id`),
  KEY `idx_payments_status` (`payment_status`),
  KEY `idx_payments_date` (`payment_date`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'credit_card','4445','Mohaned Ehab',180.00,'completed','TXN_1777723062_1367','2026-05-02 11:57:42'),(2,2,'debit_card','4566','Mohamed Eid',600.00,'completed','TXN_1777747759_7261','2026-05-02 18:49:19'),(3,3,'credit_card','6545','Ban Test',75.00,'completed','TXN_1777747923_8072','2026-05-02 18:52:03'),(4,4,'credit_card','5465','Ban Test',155.00,'completed','TXN_1777748560_9716','2026-05-02 19:02:40');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int DEFAULT '10',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_products_category` (`category`),
  KEY `idx_products_price` (`price`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Chanel No. 5','Classic floral aldehyde perfume for women. Timeless elegance.',120.00,'https://images.unsplash.com/photo-1541643600914-78b084683601?w=300','Women',15,'2026-04-30 11:41:45'),(2,'Dior Sauvage','Fresh and powerful men\'s fragrance with notes of bergamot and ambroxan.',95.00,'https://allparfume.by/images/parfumes/christian_dior_sauvage_eau_de_parfum_2018.jpg','Men',20,'2026-04-30 11:41:45'),(3,'Jo Malone London','Luxury unisex fragrance with peony and blush suede notes.',85.00,'https://m.media-amazon.com/images/I/71zE0Jz35WL._AC_.jpg','Unisex',12,'2026-04-30 11:41:45'),(5,'Creed Aventus','Fruity and smoky masculine fragrance.',250.00,'https://cdn.shopify.com/s/files/1/2170/5343/products/71Xm33tqXWL._SL1200_1024x1024.jpg?v=1571752392','Men',5,'2026-04-30 11:41:45'),(7,'Acqua di Gio','Fresh aquatic fragrance for men.',80.00,'https://images.ctchealth.ca/Giorgio_Armani_Acqua_Di_Gi__Parfum_Fresh_Aquatic_Fragrance_for_Men__Eco_Friendly__Refillable_3614273954174_1.webp','Men',25,'2026-04-30 11:41:45'),(8,'Le Labo Santal 33','Woody unisex fragrance with sandalwood notes.',180.00,'https://img.ssensemedia.com/images/231642M787003_3/santal-33-eau-de-parfum-100-ml.jpg','Unisex',10,'2026-04-30 11:41:45'),(9,'Gucci Bloom','A captivating white floral fragrance with notes of jasmine and tuberose.',125.00,'https://i5.walmartimages.com/seo/Gucci-Bloom-Eau-De-Parfum-Perfume-for-Women-3-3-Oz_d40c9979-047c-4d1e-8eda-06f9c552aab8_1.88073c0e96cfbf0e993ffa4e6ea44b95.jpeg','Women',15,'2026-05-01 12:10:57'),(10,'Marc Jacobs Daisy','Fresh and whimsical with wild strawberry, violet leaves, and jasmine.',85.00,'https://www.perfumes.com.ph/cdn/shop/files/marc-jacobs-daisy-100ml-perfume-philippines-best-price.webp?v=1698311753&width=1200','Women',20,'2026-05-01 12:13:32'),(11,'Bleu de Chanel','Sophisticated with citrus, cedar, and sandalwood.',130.00,'https://www.aarfragrances.com/public/uploads/all/JkcaUyvRs4syCdI1vDUcIzXlbRiVJ8lElovBT1pL.jpg','Men',14,'2026-05-01 12:18:04'),(12,'Prada Candy','Sweet and warm with caramel, benzoin, and musk.',95.00,'https://img.fragrancex.com/images/products/sku/large/pcandy1oz.jpg','Women',18,'2026-05-01 12:20:11'),(13,'Dior J\'adore','Feminine and floral with ylang-ylang, rose, and jasmine.',140.00,'https://images-na.ssl-images-amazon.com/images/I/515eJKjfAdL._SL1000_.jpg','Women',14,'2026-05-01 12:21:14'),(14,'Carolina Herrera Good Girl','Daring and seductive with almond, coffee, and tuberose.',135.00,'https://m.media-amazon.com/images/I/51VKjr+u84L._SL1000_.jpg','Women',10,'2026-05-01 12:22:11'),(15,'Versace Eros','Energetic and passionate with mint, green apple, and tonka bean.',90.00,'https://res.cloudinary.com/beleza-na-web/image/upload/w_1500,f_auto,fl_progressive,q_auto:eco,w_1800,c_limit/e_trim/v1/imagens/product/20054390/858b1dfd-bed4-421e-a9f3-ee2247f59f51-eros-versace-eau-de-parfum-perfume-masculino-50ml.png','Men',14,'2026-05-01 12:23:15'),(16,'Hugo Boss Bottled','Classic masculine with apple, cinnamon, and oakmoss.',75.00,'https://www.perfumenz.co.nz/cdn/shop/files/boss-bottled-absolu-100ml_1400x1400.png?v=1747279141','Men',24,'2026-05-01 12:24:44'),(17,'Tom Ford Oud Wood','Exotic and warm with oud, sandalwood, and tonka bean.',210.00,'https://cdn.shopify.com/s/files/1/0259/7733/products/tom-ford-oud-wood_1024x1024.png?v=1540964328','Unisex',6,'2026-05-01 12:25:47'),(18,'Diptyque Philosykos','Fig tree scent with fig leaf, wood, and coconut.',155.00,'https://perfumescentsation.com/wp-content/uploads/2023/11/DIPTYQUE-PHILOSYKOS-EDP-75ML-1.jpeg','Unisex',7,'2026-05-01 12:26:48'),(19,'Byredo Gypsy Water','Aromatic with bergamot, juniper berries, and vanilla.',175.00,'https://www.perfumenz.co.nz/cdn/shop/products/byredo-gypsy-water-50ml_700x700.png?v=1662172998','Unisex',6,'2026-05-01 12:27:45'),(20,'Maison Francis Kurkdjian Baccarat Rouge 540','Airy and sweet with jasmine, saffron, and cedar.',200.00,'https://azperfumes.vteximg.com.br/arquivos/ids/174723-1000-1000/Baccarat-Rouge-540-Extrait-Maison-Francis-Kurkdjian-Eau-De-Parfum-Perfume-Feminino.jpg?v=638254790561470000','Unisex',0,'2026-05-01 12:29:15');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `last_login` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Admin','admin@rasha.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',1,'2026-05-02 11:54:16',NULL,NULL,'2026-05-02 19:17:02','active'),(3,'Mohaned Ehab','Mohaned@gmail.com','$2y$10$CvC/EXDYx5M.mMQRBJIlPu2lECIjdnTtX8Y41SGRc/iJ2SzQIg4Aq',0,'2026-05-02 11:56:15','01007545813','3 Ahmed Ayoub street',NULL,'active'),(4,'Mohamed Eid','Eid@gmail.com','$2y$10$b5dGpSkat3nLBBTwMpVqTO.dgJUPannRMT.YVIeZWr3ilczaZ7ooi',0,'2026-05-02 18:48:03','01007545813','3 Ahmed Ayoub street',NULL,'active'),(5,'Ban Test','Ban@gmail.com','$2y$10$csUOHx2T26.Zb5HpwJhMXe1PCPMzRvrf69U4ivByM7TLMSlBp7sc6',0,'2026-05-02 18:50:36','01007545813','3 Ahmed Ayoub street',NULL,'inactive');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Final view structure for view `order_summary_view`
--

/*!50001 DROP VIEW IF EXISTS `order_summary_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `order_summary_view` AS select `o`.`id` AS `order_id`,`o`.`user_id` AS `user_id`,`u`.`name` AS `customer_name`,`u`.`email` AS `customer_email`,`o`.`total_amount` AS `total_amount`,`o`.`status` AS `order_status`,`o`.`shipping_address` AS `shipping_address`,`o`.`phone` AS `phone`,`o`.`created_at` AS `order_date`,`p`.`payment_method` AS `payment_method`,`p`.`card_number_last4` AS `card_number_last4`,`p`.`amount` AS `payment_amount`,`p`.`payment_status` AS `payment_status`,`p`.`transaction_id` AS `transaction_id`,`p`.`payment_date` AS `payment_date` from ((`orders` `o` join `users` `u` on((`o`.`user_id` = `u`.`id`))) left join `payments` `p` on((`o`.`id` = `p`.`order_id`))) order by `o`.`created_at` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-02 23:17:23
