-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: tanisha_telecom
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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(191) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('tanisha-telecom-cache-5c785c036466adea360111aa28563bfd556b5fba','i:2;',1790926030),('tanisha-telecom-cache-5c785c036466adea360111aa28563bfd556b5fba:timer','i:1790926030;',1790926030);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(191) NOT NULL,
  `owner` varchar(191) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Smartphones','smartphones','Latest 5G flagships, mid-range and budget smartphones with official warranty.','assets/images/categories/smartphones.jpg',0,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(2,'Laptops & Tablets','laptops-tablets','Ultrabooks, gaming laptops and tablets for work, study and play.','assets/images/categories/laptops.jpg',1,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(3,'Smartwatches','smartwatches','Smartwatches and fitness bands to track your health and stay connected.','assets/images/categories/smartwatches.jpg',2,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(4,'Audio','audio','True wireless earbuds, headphones and Bluetooth speakers.','assets/images/categories/audio.jpg',3,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(5,'Power & Charging','power-charging','Power banks, fast chargers and wireless chargers.','assets/images/categories/power.jpg',4,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(6,'Accessories','accessories','Cases, screen protectors, cables, mice and more.','assets/images/categories/accessories.jpg',5,1,'2026-10-01 16:02:36','2026-10-01 16:02:36');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(191) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) NOT NULL,
  `connection` varchar(191) NOT NULL,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) NOT NULL,
  `name` varchar(191) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint(5) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_10_01_134955_create_personal_access_tokens_table',1),(5,'2026_10_01_140000_create_shop_tables',1),(6,'2026_10_01_160000_add_online_payment_to_orders_table',1),(7,'2026_10_02_120000_add_free_shipping_to_products_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `product_name` varchar(191) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,13,'20000mAh 22.5W Fast Charging Power Bank',2290.00,1,2290.00,'2026-10-01 16:43:59','2026-10-01 16:43:59'),(2,2,8,'Fit Square Smartwatch with Calling - Navy',3490.00,1,3490.00,'2026-10-01 16:47:02','2026-10-01 16:47:02');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `customer_name` varchar(191) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(191) DEFAULT NULL,
  `shipping_area` varchar(30) NOT NULL DEFAULT 'inside_dhaka',
  `notes` text DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'cod',
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid',
  `transaction_id` varchar(191) DEFAULT NULL,
  `sp_order_id` varchar(100) DEFAULT NULL,
  `payment_channel` varchar(50) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_sp_order_id_index` (`sp_order_id`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'TT-261001-E0E05',2,'Customer','01877078962','customer@gmail.com','Kaliakoir, Bagan Bari, Gazipur','Gazipur','outside_dhaka',NULL,2290.00,120.00,0.00,2410.00,'shurjopay','paid','6abee264','TTM6abee23250d4d','Nagad','2026-10-01 16:44:54','pending','2026-10-01 16:43:59','2026-10-01 16:44:54'),(2,'TT-261001-30563',2,'Customer','01877078962','customer@gmail.com','Kaliakoir, Bagan Bari, Gazipur','Gazipur','outside_dhaka',NULL,3490.00,120.00,0.00,3610.00,'shurjopay','paid','6abee2fb','TTM6abee2e791e49','Nagad','2026-10-01 16:47:25','pending','2026-10-01 16:47:02','2026-10-01 16:47:25');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `content` longtext DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
INSERT INTO `pages` VALUES (1,'About Us','about-us','<h2>Welcome to Tanisha Telecom</h2>\n<p><strong>Tanisha Telecom</strong> is your trusted destination for original smartphones, laptops, smartwatches, audio gear and mobile accessories in Bangladesh. We believe everyone deserves genuine technology at a fair price, backed by real warranty and friendly support.</p>\n<hr>\n<h3><i class=\"fa-solid fa-boxes-stacked\"></i> What we offer</h3>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-mobile-screen-button\"></i><span><strong>Smartphones:</strong> the latest flagship, mid-range and budget phones.</span></li>\n<li><i class=\"fa-solid fa-laptop\"></i><span><strong>Laptops &amp; Tablets:</strong> ultrabooks, gaming laptops and tablets for work and study.</span></li>\n<li><i class=\"fa-solid fa-clock\"></i><span><strong>Smartwatches:</strong> smartwatches and fitness bands to keep you healthy and connected.</span></li>\n<li><i class=\"fa-solid fa-headphones\"></i><span><strong>Audio:</strong> earbuds, headphones and Bluetooth speakers.</span></li>\n<li><i class=\"fa-solid fa-battery-full\"></i><span><strong>Power &amp; Charging:</strong> power banks, fast chargers and wireless chargers.</span></li>\n<li><i class=\"fa-solid fa-plug\"></i><span><strong>Accessories:</strong> cases, screen protectors, cables, mice and much more.</span></li>\n</ul>\n<hr>\n<h3><i class=\"fa-solid fa-award\"></i> Why shop with us?</h3>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-circle-check\"></i><span>100% original products with official / brand warranty</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span>Competitive prices and regular offers</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span>Cash on Delivery and secure online payment via shurjoPay (bKash, Nagad, Rocket, Visa, Mastercard)</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span>Fast home delivery all over Bangladesh</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span>Expert advice before you buy and support after you buy</span></li>\n</ul>\n<hr>\n<h3><i class=\"fa-solid fa-headset\"></i> Contact us</h3>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-phone\"></i><span>Phone: <a href=\"tel:01754471654\">01754471654</a></span></li>\n<li><i class=\"fa-brands fa-whatsapp\"></i><span>WhatsApp: <a href=\"https://wa.me/8801754471654\" target=\"_blank\" rel=\"noopener\">01754471654</a></span></li>\n<li><i class=\"fa-solid fa-envelope\"></i><span>Email: <a href=\"mailto:info@tanishatelecom.com\">info@tanishatelecom.com</a></span></li>\n</ul>',1,'2026-10-01 16:02:36','2026-10-01 16:21:46'),(2,'Refund and Returns Policy','refund-returns','<h2>Refund &amp; Returns Policy</h2>\n<p>At <strong>Tanisha Telecom</strong> we want you to be completely happy with your purchase. If something isn\'t right, here is how our return and refund process works.</p>\n<h3><i class=\"fa-solid fa-circle-check\"></i> Eligible for return / replacement</h3>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-circle-check\"></i><span><strong>Wrong product delivered:</strong> the item, model or variant is different from your order.</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span><strong>Damaged or defective on arrival:</strong> physical damage or a manufacturing fault found at delivery.</span></li>\n<li><i class=\"fa-solid fa-circle-check\"></i><span><strong>Missing items:</strong> accessories or parts listed in the box are missing.</span></li>\n</ul>\n<p>Please report the issue within <strong>3 days of delivery</strong> with photos or a short video of the product and packaging.</p>\n<h3><i class=\"fa-solid fa-circle-xmark\"></i> Not eligible</h3>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-circle-xmark\"></i><span>Products with physical damage, liquid damage or signs of misuse after delivery</span></li>\n<li><i class=\"fa-solid fa-circle-xmark\"></i><span>Products without the original box, accessories, invoice or with broken warranty seals</span></li>\n<li><i class=\"fa-solid fa-circle-xmark\"></i><span>Change of mind after the product has been unsealed or activated</span></li>\n<li><i class=\"fa-solid fa-circle-xmark\"></i><span>Software issues that can be fixed by an update or reset</span></li>\n</ul>\n<h3><i class=\"fa-solid fa-screwdriver-wrench\"></i> Warranty claims</h3>\n<p>After the return window, faults are handled under the brand / official warranty shown on the product page. Bring or send the product with its invoice and we will help you with the claim.</p>\n<h3><i class=\"fa-solid fa-rotate-left\"></i> Refund process</h3>\n<ul>\n<li>Once we receive and inspect the returned product, we will confirm your refund or replacement.</li>\n<li>Approved refunds are processed within <strong>5 to 7 business days</strong>.</li>\n<li>Online payments made through shurjoPay are refunded to the original payment method; Cash on Delivery orders are refunded via bKash, Nagad or bank transfer.</li>\n<li>Delivery charges are non-refundable unless the return is due to our mistake.</li>\n</ul>\n<h3><i class=\"fa-solid fa-headset\"></i> Contact us</h3>\n<p>For any return or refund question, please contact our support team.</p>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-phone\"></i><span>Phone: <a href=\"tel:01754471654\">01754471654</a></span></li>\n<li><i class=\"fa-brands fa-whatsapp\"></i><span>WhatsApp: <a href=\"https://wa.me/8801754471654\" target=\"_blank\" rel=\"noopener\">01754471654</a></span></li>\n</ul>',1,'2026-10-01 16:02:36','2026-10-01 16:21:46'),(3,'Terms & Conditions','terms-conditions','<h2>Terms &amp; Conditions</h2>\n<p>By using the Tanisha Telecom website or placing an order, you agree to the following terms.</p>\n<h3>1. Products &amp; pricing</h3>\n<ul>\n<li>All prices are in Bangladeshi Taka (৳) and include applicable VAT unless stated otherwise.</li>\n<li>Product images are for illustration; colours and packaging may vary slightly.</li>\n<li>Prices, offers and availability may change without prior notice.</li>\n</ul>\n<h3>2. Orders</h3>\n<ul>\n<li>Our team may call you to confirm your order before it is shipped.</li>\n<li>We may cancel an order if a product is out of stock, the price was listed incorrectly, or the order details cannot be verified. Any payment made will be refunded in full.</li>\n</ul>\n<h3>3. Payment</h3>\n<ul>\n<li>We accept Cash on Delivery and online payment through <strong>shurjoPay</strong> (bKash, Nagad, Rocket, Visa, Mastercard and other supported methods).</li>\n<li>Online payments are processed securely by shurjoPay; we never see or store your card or wallet PIN.</li>\n</ul>\n<h3>4. Delivery</h3>\n<ul>\n<li>Inside Dhaka: usually within 24 to 48 hours. Outside Dhaka: usually within 2 to 5 business days.</li>\n<li>Please check the product in front of the delivery person before accepting it.</li>\n<li>If delivery fails because of a wrong address or unreachable phone, a re-delivery charge may apply.</li>\n</ul>\n<h3>5. Warranty</h3>\n<p>Warranty terms are set by the brand or as stated on the product page. Physical damage, liquid damage and unauthorised repairs void the warranty.</p>\n<h3>6. Privacy</h3>\n<p>Your name, phone number and address are used only to process and deliver your order and are never sold to third parties.</p>\n<h3>7. Returns</h3>\n<p>See our <a href=\"refund-returns.html\">Refund &amp; Returns Policy</a> for details.</p>\n<h3>8. Contact</h3>\n<p>Questions? We are happy to help.</p>\n<ul class=\"icon-list\">\n<li><i class=\"fa-solid fa-phone\"></i><span>Phone: <a href=\"tel:01754471654\">01754471654</a></span></li>\n<li><i class=\"fa-brands fa-whatsapp\"></i><span>WhatsApp: <a href=\"https://wa.me/8801754471654\" target=\"_blank\" rel=\"noopener\">01754471654</a></span></li>\n</ul>',1,'2026-10-01 16:02:36','2026-10-01 16:21:46');
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `short_description` text DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `unit` varchar(50) DEFAULT NULL,
  `image` varchar(191) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `sold_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,'X15 Pro 5G Smartphone - Titanium Grey','x15-pro-5g-smartphone','Flagship 5G smartphone with a 6.7\" 120Hz AMOLED display, pro-grade triple camera and all-day battery.','<p>Flagship 5G smartphone with a 6.7\" 120Hz AMOLED display, pro-grade triple camera and all-day battery.</p><h3>Key specifications</h3><ul><li>6.7&quot; LTPO AMOLED, 120Hz, 2500 nits</li><li>Octa-core 3nm flagship processor</li><li>50MP + 48MP ultra-wide + 12MP 5x telephoto</li><li>4,800mAh battery, 45W fast charging</li><li>Titanium frame, IP68 water resistance</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',149999.00,139999.00,12,'12GB / 256GB','assets/images/products/phone-flagship.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(2,1,'A55 5G Smartphone - Ocean Blue','a55-5g-smartphone','Powerful mid-range 5G phone with a smooth 120Hz display, 50MP OIS camera and 5,000mAh battery.','<p>Powerful mid-range 5G phone with a smooth 120Hz display, 50MP OIS camera and 5,000mAh battery.</p><h3>Key specifications</h3><ul><li>6.6&quot; FHD+ Super AMOLED, 120Hz</li><li>Octa-core 5G processor</li><li>50MP OIS main + 12MP ultra-wide camera</li><li>5,000mAh battery, 25W fast charging</li><li>Expandable storage up to 1TB</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',42999.00,39499.00,25,'8GB / 128GB','assets/images/products/phone-midrange.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(3,1,'C12 Smartphone - Mint Green','c12-smartphone','Budget-friendly smartphone with a big 6.6\" display, dual camera and long-lasting 5,000mAh battery.','<p>Budget-friendly smartphone with a big 6.6\" display, dual camera and long-lasting 5,000mAh battery.</p><h3>Key specifications</h3><ul><li>6.6&quot; HD+ 90Hz display</li><li>Octa-core processor</li><li>13MP dual rear camera, 5MP selfie</li><li>5,000mAh battery</li><li>Dual SIM, side fingerprint sensor</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',14999.00,13499.00,40,'4GB / 64GB','assets/images/products/phone-budget.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(4,2,'AirBook 14 Ultrabook - Core i5','airbook-14-ultrabook','Ultra-thin 1.2kg aluminium laptop with a 14\" 2.8K display and up to 15 hours of battery life.','<p>Ultra-thin 1.2kg aluminium laptop with a 14\" 2.8K display and up to 15 hours of battery life.</p><h3>Key specifications</h3><ul><li>14&quot; 2.8K IPS display, 100% sRGB</li><li>Intel Core i5 (13th Gen)</li><li>16GB LPDDR5 RAM, 512GB NVMe SSD</li><li>Backlit keyboard, fingerprint reader</li><li>USB-C 65W charging, Wi-Fi 6E</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',129000.00,118500.00,8,'16GB RAM / 512GB SSD','assets/images/products/laptop-ultrabook.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(5,2,'Raptor 16 Gaming Laptop - RTX 4060','raptor-16-gaming-laptop','High-performance gaming laptop with RTX 4060 graphics, 165Hz display and RGB keyboard.','<p>High-performance gaming laptop with RTX 4060 graphics, 165Hz display and RGB keyboard.</p><h3>Key specifications</h3><ul><li>16&quot; QHD 165Hz display</li><li>Intel Core i7 (13th Gen)</li><li>NVIDIA GeForce RTX 4060 8GB</li><li>16GB DDR5 RAM, 1TB NVMe SSD</li><li>Per-key RGB keyboard, advanced cooling</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',185000.00,172000.00,5,'16GB RAM / 1TB SSD','assets/images/products/laptop-gaming.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(6,2,'Pad 11 Tablet with Stylus - Space Grey','pad-11-tablet','11\" 2K tablet with stylus support, quad speakers and a big battery for study, work and entertainment.','<p>11\" 2K tablet with stylus support, quad speakers and a big battery for study, work and entertainment.</p><h3>Key specifications</h3><ul><li>11&quot; 2K 120Hz display</li><li>Octa-core processor</li><li>Stylus pen included</li><li>Quad speakers with Dolby Atmos</li><li>8,000mAh battery, 33W charging</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',38500.00,34990.00,15,'8GB / 256GB','assets/images/products/tablet.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(7,3,'Active Pro AMOLED Smartwatch - Black','active-pro-amoled-smartwatch','Premium round smartwatch with a 1.43\" AMOLED display, GPS, heart-rate and SpO2 monitoring.','<p>Premium round smartwatch with a 1.43\" AMOLED display, GPS, heart-rate and SpO2 monitoring.</p><h3>Key specifications</h3><ul><li>1.43&quot; AMOLED always-on display</li><li>Built-in GPS, 150+ sports modes</li><li>Heart rate, SpO2, sleep &amp; stress tracking</li><li>Bluetooth calling</li><li>Up to 12 days battery, 5ATM water resistance</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',8990.00,6990.00,30,'46mm, Black','assets/images/products/watch-round.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(8,3,'Fit Square Smartwatch with Calling - Navy','fit-square-smartwatch','Stylish square smartwatch with Bluetooth calling, health tracking and customisable watch faces.','<p>Stylish square smartwatch with Bluetooth calling, health tracking and customisable watch faces.</p><h3>Key specifications</h3><ul><li>1.96&quot; HD display</li><li>Bluetooth calling with mic &amp; speaker</li><li>Heart rate &amp; SpO2 monitoring</li><li>100+ sports modes</li><li>IP67 water resistance</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',4500.00,3490.00,45,'44mm, Silver / Navy','assets/images/products/watch-square.jpg',0,1,0,1,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(9,3,'Band 8 Fitness Tracker','band-8-fitness-tracker','Slim fitness band with AMOLED display that tracks steps, heart rate, sleep and workouts.','<p>Slim fitness band with AMOLED display that tracks steps, heart rate, sleep and workouts.</p><h3>Key specifications</h3><ul><li>1.62&quot; AMOLED display</li><li>24/7 heart rate &amp; SpO2</li><li>150+ workout modes</li><li>Up to 16 days battery</li><li>5ATM water resistance</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',3200.00,2650.00,60,'Black','assets/images/products/fitness-band.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(10,4,'Buds Pro ANC True Wireless Earbuds','buds-pro-anc-earbuds','Active noise cancelling earbuds with rich bass, clear calls and up to 36 hours of total playtime.','<p>Active noise cancelling earbuds with rich bass, clear calls and up to 36 hours of total playtime.</p><h3>Key specifications</h3><ul><li>Hybrid ANC up to 45dB</li><li>11mm dynamic drivers</li><li>4-mic AI call noise reduction</li><li>36 hours with charging case</li><li>Bluetooth 5.3, IPX5</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',4990.00,3790.00,50,'Black','assets/images/products/earbuds.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(11,4,'SoundMax ANC Wireless Headphones','soundmax-anc-headphones','Over-ear wireless headphones with adaptive noise cancelling, Hi-Res audio and 60-hour battery.','<p>Over-ear wireless headphones with adaptive noise cancelling, Hi-Res audio and 60-hour battery.</p><h3>Key specifications</h3><ul><li>Adaptive active noise cancelling</li><li>40mm Hi-Res drivers</li><li>Up to 60 hours battery</li><li>Multipoint Bluetooth 5.3</li><li>Foldable design with carry case</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',9500.00,7990.00,20,'Silver White','assets/images/products/headphones.jpg',1,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(12,4,'Boom Mini Waterproof Bluetooth Speaker','boom-mini-bluetooth-speaker','Portable 20W speaker with deep bass, IPX7 waterproofing and 12 hours of playtime.','<p>Portable 20W speaker with deep bass, IPX7 waterproofing and 12 hours of playtime.</p><h3>Key specifications</h3><ul><li>20W stereo sound with passive bass radiator</li><li>IPX7 waterproof</li><li>12 hours playtime</li><li>TWS pairing for stereo</li><li>Built-in mic for calls</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',3800.00,2990.00,35,'Navy Blue','assets/images/products/speaker.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(13,5,'20000mAh 22.5W Fast Charging Power Bank','20000mah-power-bank','High-capacity power bank with digital display that charges your phone up to 4 times.','<p>High-capacity power bank with digital display that charges your phone up to 4 times.</p><h3>Key specifications</h3><ul><li>20,000mAh capacity</li><li>22.5W fast charging (PD &amp; QC)</li><li>USB-C + 2× USB-A outputs</li><li>Digital battery percentage display</li><li>Charge 3 devices at once</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',2800.00,2290.00,70,'20000mAh, Black','assets/images/products/power-bank.jpg',1,1,0,1,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(14,5,'65W GaN 3-Port Fast Charger','65w-gan-fast-charger','Compact GaN charger that powers laptops, tablets and phones. USB-C cable included.','<p>Compact GaN charger that powers laptops, tablets and phones. USB-C cable included.</p><h3>Key specifications</h3><ul><li>65W total output (GaN technology)</li><li>2× USB-C + 1× USB-A</li><li>Charges laptops, tablets and phones</li><li>Over-heat &amp; over-current protection</li><li>USB-C to USB-C cable included</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',3200.00,2590.00,55,'65W, White','assets/images/products/charger.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(15,5,'15W Magnetic Wireless Charging Pad','15w-wireless-charger','Slim wireless charger with magnetic alignment and LED ring for Qi-enabled phones and earbuds.','<p>Slim wireless charger with magnetic alignment and LED ring for Qi-enabled phones and earbuds.</p><h3>Key specifications</h3><ul><li>Up to 15W fast wireless charging</li><li>Magnetic alignment</li><li>Works through cases up to 5mm</li><li>Soft LED indicator ring</li><li>Foreign object detection</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',1800.00,1350.00,40,'15W, Black','assets/images/products/wireless-charger.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(16,6,'Rugged Shockproof Case + Tempered Glass Combo','rugged-case-glass-combo','Military-grade drop protection case with raised camera guard, plus 9H tempered glass screen protector.','<p>Military-grade drop protection case with raised camera guard, plus 9H tempered glass screen protector.</p><h3>Key specifications</h3><ul><li>Shock-absorbing TPU + PC construction</li><li>Raised bezel for screen &amp; camera</li><li>9H hardness tempered glass included</li><li>Precise cut-outs for ports &amp; buttons</li><li>Available for popular models</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',850.00,650.00,120,'Black','assets/images/products/phone-case.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(17,6,'100W Braided USB-C to USB-C Cable (2m)','100w-braided-usb-c-cable','Durable nylon braided cable supporting up to 100W fast charging and 480Mbps data transfer.','<p>Durable nylon braided cable supporting up to 100W fast charging and 480Mbps data transfer.</p><h3>Key specifications</h3><ul><li>Up to 100W PD fast charging</li><li>Nylon braided, 30,000+ bend lifespan</li><li>Aluminium connector heads</li><li>480Mbps data transfer</li><li>2 metre length</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',990.00,690.00,150,'2 metres','assets/images/products/cable.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(18,6,'Wireless RGB Gaming Mouse','wireless-rgb-gaming-mouse','Ergonomic wireless gaming mouse with adjustable DPI, RGB lighting and rechargeable battery.','<p>Ergonomic wireless gaming mouse with adjustable DPI, RGB lighting and rechargeable battery.</p><h3>Key specifications</h3><ul><li>2.4GHz wireless + USB-C wired mode</li><li>Adjustable DPI up to 12,000</li><li>6 programmable buttons</li><li>RGB lighting</li><li>Rechargeable, up to 70 hours</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',2200.00,1750.00,45,'Black','assets/images/products/mouse.jpg',0,1,0,0,'2026-10-01 16:02:36','2026-10-02 01:24:07'),(19,6,'Silicone Mobile Back Cover - Navy Blue','silicone-mobile-cover-navy','Soft-touch liquid silicone back cover with microfiber lining and raised camera protection.','<p>Soft-touch liquid silicone back cover with microfiber lining and raised camera protection.</p><h3>Key specifications</h3><ul><li>Liquid silicone, soft-touch finish</li><li>Microfiber inner lining</li><li>Raised lip for camera &amp; screen</li><li>Precise cut-outs for ports &amp; buttons</li><li>Slim, lightweight design</li></ul><h3>Why buy from Tanisha Telecom?</h3><ul><li>100% original product</li><li>Official / brand warranty</li><li>Cash on Delivery or secure online payment (bKash, Nagad, cards)</li><li>Fast delivery all over Bangladesh</li></ul>',1.00,NULL,500,'Navy Blue','assets/images/products/mobile-cover.jpg',1,1,1,0,'2026-10-02 01:24:07','2026-10-02 01:26:47');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(191) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('fXkkhRVpjDOasoGe5jFb6GZ3T6Z5O4pjWNwDs9z0',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiIyVlpJUVlGRkZ6V085ZFVSRDVybWVLUzVPT25yU0lMM3JidnhFWWhIIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1790926273),('H8WwNI7t50WCbkclwnTnW2PKRW5HA2LXQEC3mbBU',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36 Edg/154.0.0.0','eyJfdG9rZW4iOiJrY1dnVGtidVBub1VXMzRmNGlEUXVlcExxeGJ2VHRPdldIZ3ZBcUVSIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2FkbWluXC9sb2dpbiIsInJvdXRlIjoiYWRtaW4ubG9naW4ifX0=',1790895162),('SAUSsWDeKaTrF8YBotcwCXndzS0iY042QDDZAwDN',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJTRUNyZXRqc0xnSmJrQ2VyS0ZqZ2JsaXVuUlQ1WGV4dEdmOTIxeEIxIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1790926271);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'phone','01754471654','2026-10-01 16:13:15','2026-10-01 16:13:15'),(2,'whatsapp','01754471654','2026-10-01 16:13:15','2026-10-01 16:13:15');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sliders`
--

DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sliders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) DEFAULT NULL,
  `subtitle` varchar(191) DEFAULT NULL,
  `image` varchar(191) NOT NULL,
  `link` varchar(191) DEFAULT NULL,
  `position` varchar(20) NOT NULL DEFAULT 'main',
  `sort_order` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` VALUES (1,'Next-Gen Gadgets Are Here','Smartphones, earbuds & smartwatches, up to 15% off','assets/images/slides/slide-1.jpg','shop.html?category=smartphones','main',1,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(2,'Work & Play Without Limits','Laptops, headphones and power banks for every need','assets/images/slides/slide-2.jpg','shop.html?category=laptops-tablets','main',2,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(3,'Wireless Earbuds',NULL,'assets/images/products/earbuds.jpg','shop.html?category=audio','side',1,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(4,'Smartwatches',NULL,'assets/images/products/watch-round.jpg','shop.html?category=smartwatches','side',2,1,'2026-10-01 16:02:36','2026-10-01 16:02:36'),(5,'Power Banks',NULL,'assets/images/products/power-bank.jpg','shop.html?category=power-charging','side',3,1,'2026-10-01 16:02:36','2026-10-01 16:02:36');
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `email` varchar(191) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(191) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin@gmail.com','01754471654',NULL,NULL,1,NULL,'$2y$12$Ul3NRYR0H9P2YQ435baOAOO6LR9m1vD7syRvQFrRp.1JYfs1Di2.G','DUSAy5MPe5FzYPbp8QIYiWcjxXwUDf6J4qQSst740aoG6k4lsZifboe0335v','2026-10-01 16:02:36','2026-10-01 16:21:48'),(2,'Customer','customer@gmail.com',NULL,NULL,NULL,0,NULL,'$2y$12$iKm0HHnrrDNP0KR0CkQPuu7.HNqOyyuVqyDXB7JhZK5qr4Vt8kwWS',NULL,'2026-10-01 16:23:23','2026-10-01 16:23:23');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'tanisha_telecom'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-02 13:47:26
