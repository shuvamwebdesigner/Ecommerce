-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 06, 2025 at 05:06 AM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(5, 'admin', 'admin@123', '2025-10-31 02:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `created_at`, `payment_method`) VALUES
(1, 1, 18999.00, 'cancelled', '2025-11-01 02:45:19', NULL),
(2, 1, 21999.00, 'pending', '2025-11-01 03:00:53', 'cod'),
(3, 1, 70999.00, 'completed', '2025-11-01 04:01:37', 'cod'),
(4, 2, 8499.00, 'completed', '2025-11-06 02:57:53', 'cod');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 4, 1, 21999.00),
(2, 3, 6, 1, 70999.00),
(3, 4, 11, 1, 8499.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `stock`, `created_at`, `category`) VALUES
(3, 'Realme P3x', 'realme P3x 5G (Stellar Pink, 128 GB)  (6 GB RAM)\r\n17.07 cm (6.72 inch) Full HD Display\r\n50MP Rear Camera | 8MP Front Camera\r\n6000 mAh Battery/45 W Charge - Never worry about battery life again! With a 6000 mAh battery and 45 W charging, this phone powers up in no time, keeping you connected and ready to go whenever you need it.\r\nMediaTek Dimensity 6400 5G Chipset -Experience lightning-fast speed with a powerful chipset that keeps everything running smoothly. Whether you\'re multitasking, gaming, or streaming, enjoy seamless performance without any lag or interruptions!\r\nIP69 Dust and Water Resistance - With IP69 dust and water resistance, your phone is ready for anything! No more worrying about dust, spills, or rain—your device stays protected even in tough conditions. Built to last, it keeps you connected without the stress of damage. Stay worry-free and focus on what matters!', 11999.00, '1761880954_7558.png', 5, '2025-10-31 03:22:34', 'Mobiles'),
(4, 'Edge 60 Fusion 5G', 'MOTOROLA Edge 60 Fusion 5G (PANTONE Amazonite, 256 GB) (8 GB RAM)\r\n8 GB RAM | 256 GB ROM | Expandable Upto 1 TB\r\n16.94 cm (6.67 inch) Display\r\n50MP + 13MP | 32MP Front Camera\r\n5500 mAh Battery\r\nDimensity 7400 Processor\r\n68 W TurboPower Charging - The 5500 mAh battery keeps you powered throughout the day, while 68W TurboPower charging delivers an incredible boost—get a full day’s power in just 9 minutes, so you spend less time plugged in and more time on the go.\r\nImmersive 1.5K All-curved Display - The 17.018 cm (6.7) pOLED display delivers sharper detail and less pixelation. With a 1.5K 100% True All-Curved Display, Pantone Validation, and a Segment\'s best 96.3% screen-to-body ratio, every visual feels immersive and lifelike. Enjoy 4500 nits peak brightness— ensuring clarity even in direct sunlight. The 120Hz refresh rate delivers ultra-smooth visuals, while Smart Water Touch 3.0 enhances touch accuracy. Built for durability, the display is protected by Gorilla Glass 7i and SGS Eye Protection for a comfortable viewing experience.', 21999.00, '1761882562_5086.jpg', 7, '2025-10-31 03:49:22', 'Mobiles'),
(5, 'OPPO K13 5G', 'OPPO K13 5G with 7000mAh and 80W SUPERVOOC Charger In-The-Box (Icy Purple, 128 GB) (8 GB RAM)\r\n16.94 cm (6.67 inch) Display\r\n50MP + 2MP | 16MP Front Camera\r\nSnapdragon 6 Gen 4 Processor\r\nMaximum Play, Minimum wait! - Huge 7000mAh battery that lasts longer with 80W SUPERVOOC for super-fast charging and 5-Year Durability. It has REAL graphite that\'s REALLY exceptional, adding years to your battery\'s life. Graphite anodes deliver unmatched stability, superior thermal and structural performance over 1800+ charge cycles.\r\n50MP Ultra-Clear Camera System - Flagship-grade AI capabilities unleashed! Experience the Picture Perfect Portraits, Clear Night Photos, and Livephoto.', 18999.00, '1761884152_2232.jpg', 6, '2025-10-31 04:15:52', 'Mobiles'),
(6, 'Samsung Galaxy S24 5G', 'Samsung Galaxy S24 5G Snapdragon (Amber Yellow, 256 GB)  (8 GB RAM)\r\n15.75 cm (6.2 inch) Full HD+ Display\r\n50MP + 12MP | 12MP Front Camera\r\n4000 mAh Battery\r\n8 Gen 3 Processor\r\nNext Level Speed. Snapdragon 8 Gen 3 for Galaxy S24 now with Snapdragon 8 Gen 3 for Galaxy.\r\nGalaxy AI for Your Everyday Unleash whole new levels of creativity, productivity, and possibility.', 70999.00, '1761886195_9894.jpg', 2, '2025-10-31 04:49:55', 'Mobiles'),
(7, 'Nothing Phone 2 Pro', 'CMF by Nothing Phone 2 Pro (Light Green, 256 GB)  (8 GB RAM)\r\n17.2 cm (6.77 inch) Display\r\n50MP + 50MP + 8MP | 16MP Front Camera\r\n5000 mAh Battery\r\nDimensity 7300 Pro 5G Processor\r\nTriple Camera System - CMF Phone 2 Pro presents a refined, flagship-level, 3-camera configuration that prioritizes practicality, delivering optimal benefits. This design empowers you to capture every light and shadow in stunning detail for pro-level shots, wherever you are. Featuring a large, light-absorbing 50 MP sensor—the largest in the segment—a high-resolution 50 MP telephoto sensor with a wide aperture—a segment first, perfect for portraits, and a 119 degrees ultra-wide camera to capture breathtaking landscapes. Plus, takes centre stage with the 16 MP front camera. All four sensors support Ultra HDR photo output for exceptional images.\r\nCMF Phone 2 Pro features flexible FHD + 6.77” AMOLED display, delivering stunningly accurate colour reproduction with 1.07 billion colours. It boasts a peak brightness of 3000 nits—the segment\'s and our brightest screen yet. This display also supports Ultra HDR photos, utilizing high brightness to showcase highlights in images. Interactions are effortlessly smooth, thanks to the 120 Hz adaptive refresh rate.', 20999.00, '1761973160_9687.jpg', 5, '2025-11-01 04:59:20', 'Mobiles'),
(8, 'Apple iPhone 17', 'Apple iPhone 17 (Mist Blue, 256 GB)\r\n16.0 cm (6.3 inch) Super Retina XDR Display\r\n48MP + 48MP | 18MP Front Camera\r\nA19 Chip, 6 Core Processor', 82899.00, '1761973722_3104.jpg', 3, '2025-11-01 05:08:42', 'Mobiles'),
(9, 'realme TechLife 80 cm Smart TV', 'realme TechLife 80 cm (32 inch) QLED HD Ready Smart Google TV 2025 Edition  (32HDGQRDDAQ)\r\nOperating System: Google TV\r\nResolution: HD Ready 1366 x 768 Pixels\r\nSound Output: 26 W\r\nRefresh Rate: 60 Hz\r\nrealme TechLife TV boasts a Quad-Core Processor with 1 GB RAM and 8 GB internal storage for smooth performance, complemented by 26W down-firing speakers with cinematic surround sound. Enjoy ultra-vivid visuals with 4K UHD resolution and Vivid Picture Mode, along with the extensive entertainment options of Google TV 5.0 and the immersive audio experience of Dolby Audio.', 9999.00, '1762057956_6815.jpg', 12, '2025-11-02 03:36:29', 'Smart TV'),
(10, 'Reliance Jumbo 80 cm Smart TV', 'Reliance Jumbo 80 cm (32 inch) Full HD LED Smart Android TV 2025 Edition with 24 W Front Boom Speakers | 1000+ Smart Apps | Mobile Screen Connect | Wifi | Games  (RGT32MP2784FHD)\r\nSupported Apps: Netflix, Prime Video, YouTube, JioHotstar, JioCinema\r\nOperating System: Android\r\nResolution: Full HD 1920 x 1080 Pixels\r\nSound Output: 24 W\r\nRefresh Rate: 60 Hz\r\nDiscover a new level of entertainment with the Reliance 32-inch Frameless Android Smart TV. Featuring a sleek, frameless design, this TV offers an expansive viewing experience, showcasing your favorite content in breathtaking FHD resolution. Enjoy stunning picture quality with vibrant colors, sharp contrasts, and exceptional detail that bring every scene to life, making it ideal for movies, sports, and gaming. This smart TV provides a user-friendly interface with fast access to your favorite streaming services, apps, and channels. The intuitive Android Tv platform makes navigating through content a breeze, commands, giving you a seamless, interactive experience. Designed to complement any modern space, the Reliance 32-inch TV combines its minimalist aesthetics with powerful audio that fills the room with immersive sound. With multiple HDMI and USB ports, it’s easy to connect all your devices, making this TV the centerpiece of your home entertainment system.', 7499.00, '1762058085_5206.jpg', 25, '2025-11-02 03:42:57', 'Smart TV'),
(11, 'Infinix 32GH3Q 80 cm Smart TV', 'Infinix 32GH3Q 80 cm (32 inch) QLED HD Ready Smart Google TV 2025 Edition with Bezel-less Design| EPIC Engine| Google Assistant| Built-in Chromecast| Google Home App compatibility| Dual-Band WiFi|  (32GH3Q)\r\nSupported Apps: Prime Video, Netflix, JioHotstar, YouTube\r\nOperating System: Google TV\r\nResolution: HD Ready 1366 x 768 Pixels\r\nSound Output: 20 W\r\nRefresh Rate: 60 Hz\r\nThis Infinix TV features a narrow bezel frame for an expansive view. Equipped with a QLED panel that offers up to 300 nits of brightness, this TV provides enhanced visual clarity. The anti-blue-ray technology ensures an immersive and comfortable viewing experience, while the 20 W dual speakers deliver a true cinematic surround sound experience. Enjoy lag-free viewing with the quad-core processor and elevate your entertainment levels by installing a variety of apps, streaming live shows, watching movies, browsing channels, and playing games.', 8499.00, '1762057451_1216.png', 8, '2025-11-02 03:59:56', 'Smart TV'),
(12, 'Samsung Galaxy Book5 Pro 360 AI PC', 'Samsung Galaxy Book5 Pro 360 AI PC Full Metal Chasis Intel Core Ultra 7 258V - (32 GB/1 TB SSD/Windows 11 Home) NP960QHA 2 in 1 Laptop  (16 Inch, Gray, 1.69 Kg, With MS Office)\r\nCarry It Along 2 in 1 Laptop\r\n16 Inch WQXGA+ Dynamic AMOLED 2X Display\r\nFinger Print Sensor for Faster System Access\r\nLight Laptop without Optical Disk Drive\r\nMeet Galaxy Book5 Pro 360 — our most capable Galaxy AI PC yet. Flex your productivity on the go with the power of the latest Intel® processor, delivering boosted performance for AI-accelerated experiences. Conquer tasks and create like never before while staying charged with an all-day battery\r\n360 Degree of Creative Freedom\r\nGalaxy Book5 Pro 360 puts a spin on daily productivity with its 2-in-1 convertible design. Weighing in at 1.69 kg with a sleek finish in Gray, it\'s your versatile companion for hustles on the move', 195990.00, '1762058754_9746.jpg', 3, '2025-11-02 04:45:54', 'Laptops'),
(14, 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX - (16 GB/512 GB SSD/Windows 11 Home/6 GB Graphics/NVIDIA GeForce RTX 3050) 15IRX9 Gaming Laptop  (15.6 inch, Luna Grey, 2.38 kg, With MS Office)\r\n15.6 inch Full HD, IPS, 300 Nits Brightness, Anti-glare, 100% sRGB, 144Hz Refresh Rate, G-SYNC\r\nLight Laptop without Optical Disk Drive\r\nPreloaded with MS Office\r\nThe Lenovo LOQ 15IRX9 has power and portability with 13th Gen Intel Core processors and NVIDIA RTX 3050 graphics. Its hyper chamber cooling keeps performance steady and quiet. A 39.62 cm (15.6) FHD 144 Hz screen with G-SYNC and Nahimic 3D audio delivers an immersive gaming experience. Its AI Engine+ maximizes performance in real-time, and a thinner Luna Grey design, privacy shutter, and military-grade build make it ideal for gaming on the go.', 97990.00, '1762061757_6511.jpg', 6, '2025-11-02 05:35:57', 'Laptops');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT '',
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `sort_order`) VALUES
(1, 14, '1762061757_3224_1.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 1),
(2, 14, '1762061757_1343_2.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 2),
(3, 14, '1762061757_3166_3.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 3),
(4, 14, '1762061757_6697_4.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 4),
(5, 14, '1762061757_9567_5.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 5),
(6, 14, '1762061757_5767_6.jpg', 'Lenovo LOQ Intel Core i7 13th Gen 13650HX Laptop', 6),
(7, 12, '1763012345_1575_1.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 1', 1),
(8, 12, '1763012345_1575_2.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 2', 2),
(9, 12, '1763012345_1575_3.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 3', 3),
(10, 12, '1763012345_1575_4.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 4', 4),
(11, 12, '1763012345_1575_5.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 5', 5),
(12, 12, '1763012345_1575_6.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 6', 6),
(13, 12, '1763012345_1575_7.jpg', 'Samsung Galaxy Book5 Pro 360 AI PC 7', 7),
(14, 11, '1763028745_6257_1.jpg', 'Infinix 32GH3Q 80 cm Smart TV 1', 1),
(15, 11, '1763028745_6257_2.jpg', 'Infinix 32GH3Q 80 cm Smart TV 2', 2),
(16, 11, '1763028745_6257_3.jpg', 'Infinix 32GH3Q 80 cm Smart TV 3', 3),
(17, 11, '1763028745_6257_4.jpg', 'Infinix 32GH3Q 80 cm Smart TV 4', 4),
(18, 11, '1763028745_6257_5.jpg', 'Infinix 32GH3Q 80 cm Smart TV 5', 5),
(19, 10, '1763036514_1897_1.jpg', 'Reliance Jumbo 80 cm Smart TV 1', 1),
(20, 10, '1763036514_1897_2.jpg', 'Reliance Jumbo 80 cm Smart TV 2', 2),
(21, 10, '1763036514_1897_3.jpg', 'Reliance Jumbo 80 cm Smart TV 3', 3),
(22, 10, '1763036514_1897_4.jpg', 'Reliance Jumbo 80 cm Smart TV 4', 4),
(23, 10, '1763036514_1897_5.jpg', 'Reliance Jumbo 80 cm Smart TV 5', 5),
(24, 10, '1763036514_1897_6.jpg', 'Reliance Jumbo 80 cm Smart TV 6', 6),
(25, 9, '1763047012_2985_1.jpg', 'realme TechLife 80 cm Smart TV 1', 1),
(26, 9, '1763047012_2985_2.jpg', 'realme TechLife 80 cm Smart TV 2', 2),
(27, 9, '1763047012_2985_3.jpg', 'realme TechLife 80 cm Smart TV 3', 3),
(28, 9, '1763047012_2985_4.jpg', 'realme TechLife 80 cm Smart TV 4', 4),
(29, 9, '1763047012_2985_5.jpg', 'realme TechLife 80 cm Smart TV 5', 5),
(30, 9, '1763047012_2985_6.jpg', 'realme TechLife 80 cm Smart TV 6', 6),
(31, 9, '1763047012_2985_7.jpg', 'realme TechLife 80 cm Smart TV 7', 7),
(32, 9, '1763047012_2985_8.jpg', 'realme TechLife 80 cm Smart TV 8', 8),
(33, 3, '1763051822_6496_1.jpg', 'Realme P3x 1', 1),
(34, 3, '1763051822_6496_2.jpg', 'Realme P3x 2', 2),
(35, 3, '1763051822_6496_3.jpg', 'Realme P3x 3', 3),
(36, 8, '1763061280_1147_1.jpg', 'Apple iPhone 17', 1),
(37, 8, '1763061280_1147_2.jpg', 'Apple iPhone 17 2', 2),
(38, 8, '1763061280_1147_3.jpg', 'Apple iPhone 17 3', 3),
(39, 8, '1763061280_1147_4.jpg', 'Apple iPhone 17 4', 4),
(40, 4, '1763071250_9857_1.jpg', 'Edge 60 Fusion 5G', 1),
(41, 4, '1763071250_9857_2.jpg', 'Edge 60 Fusion 5G', 2),
(42, 4, '1763071250_9857_3.jpg', 'Edge 60 Fusion 5G', 3),
(43, 4, '1763071250_9857_4.jpg', 'Edge 60 Fusion 5G', 4),
(44, 4, '1763071250_9857_5.jpg', 'Edge 60 Fusion 5G', 5),
(45, 4, '1763071250_9857_6.jpg', 'Edge 60 Fusion 5G', 6),
(46, 5, '1763082145_4596_1.jpg', 'Oppo K13 5G', 1),
(47, 5, '1763082145_4596_2.jpg', 'Oppo K13 5G', 2),
(48, 5, '1763082145_4596_3.jpg', 'Oppo K13 5G', 3),
(49, 5, '1763082145_4596_4.jpg', 'Oppo K13 5G', 4),
(50, 5, '1763082145_4596_5.jpg', 'Oppo K13 5G', 5),
(51, 5, '1763082145_4596_6.jpg', 'Oppo K13 5G', 6),
(52, 6, '1763097850_7035_1.jpg', 'Samsung Galaxy S24 5G', 1),
(53, 6, '1763097850_7035_2.jpg', 'Samsung Galaxy S24 5G', 2),
(54, 6, '1763097850_7035_3.jpg', 'Samsung Galaxy S24 5G', 3),
(55, 6, '1763097850_7035_4.jpg', 'Samsung Galaxy S24 5G', 4),
(56, 7, '1763174135_2514_1.jpg', 'Nothing Phone 2 Pro', 1),
(57, 7, '1763174135_2514_2.jpg', 'Nothing Phone 2 Pro', 2),
(58, 7, '1763174135_2514_3.jpg', 'Nothing Phone 2 Pro', 3),
(59, 7, '1763174135_2514_4.jpg', 'Nothing Phone 2 Pro', 4),
(60, 7, '1763174135_2514_5.jpg', 'Nothing Phone 2 Pro', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Monalisa45', 'monalisaroy@gmail.com', '$2y$10$uPTJbJ6oxEToX6mExhoRcOC1ppoztx8tjLxwpE.CYGmzTP9Y4.fya', '2025-10-31 02:31:52'),
(2, 'Shuvam45', 'shuvam45@gmail.com', '$2y$10$e35B/44kDv56T.z.y4ywnOpj35bHGOHCQX3.fOjokVTQfn9V0VGxK', '2025-11-04 03:41:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
