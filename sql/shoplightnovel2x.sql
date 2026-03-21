-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 21, 2026 at 10:59 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoplightnovel2x`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin NCT', 'NCT@gmail.com', '123456', 1, '2026-03-14 16:51:53');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `fullname`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Shiina Mahiru', '123@gmail.com', '...', 'Niceヾ(≧▽≦*)o', 0, '2026-03-17 15:55:46'),
(2, 'Miku', 'Miku@gmail.com', '...', 'Good job(●\'◡\'●)', 0, '2026-03-17 16:00:48'),
(3, 'Sakura', 'Sakura@gmail.com', '...', 'Nice(❁´◡`❁)', 0, '2026-03-17 20:36:10'),
(4, 'Lucy', 'Lucy@gmail.com', 'Experience', 'Nice:D', 0, '2026-03-18 16:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `product_id` varchar(255) NOT NULL,
  `total_price` int(11) DEFAULT NULL,
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `fullname`, `email`, `product_id`, `total_price`, `order_time`) VALUES
(1, 'Khách ẩn danh', 'Sakura@gmail.com', '5', 110000, '2026-03-21 07:28:28'),
(2, 'Khách ẩn danh', 'Sakura@gmail.com', '7, 2, 4', 300000, '2026-03-21 07:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `image_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `image_id`) VALUES
(1, 'Thiên Sứ Nhà Bên - Vol 1', '85000.00', 'Thiên sứ nhà bên (Otonari no Tenshi-sama) kể về Amane, một nam sinh sống một mình, tình cờ đưa ô cho Mahiru người sỡ hữu thành tích mọi mặt nhất trường vào một ngày mưa. Nhờ hành động đó, Mahiru bắt đầu chăm sóc, nấu ăn cho Amane, từ đó cả hai phát triển mối quan hệ tình cảm, chữa lành những vết thương tâm lý và dần thay đổi bản thân theo hướng tích cực hơn', 'thiensu_v1.jpg'),
(2, 'Thiên Sứ Nhà Bên - Vol 2', '95000.00', 'Mối quan hệ giữa hai người dần trở nên thân thiết hơn.', 'thiensu_v2.jpg'),
(3, 'Arya Bàn Bên - Vol 1', '85000.00', '\"Arya Bàn Bên Thỉnh Thoảng Lại Trêu Ghẹo Tôi Bằng Tiếng Nga\" là câu chuyện tình học đường hài hước giữa Alisa Mikhailovna Kujō (Arya), nữ sinh người Nhật gốc Nga, tài năng, và người bạn cùng lớp Kuze Masachika. Arya thường dùng tiếng Nga để che giấu sự thẹn thùng khi trêu chọc Kuze, không biết rằng cậu thực chất hiểu được ngôn ngữ này, tạo nên những tình những tình huống ngại ngùng.', 'arya_v1.jpg'),
(4, 'Arya Bàn Bên - Vol 2', '95000.00', 'Masachika liệu có hiểu được những gì Arya đang nói?', 'arya_v2.jpg'),
(5, 'Netoge no Yome ga Ninki Idol datta ken ~Cool-kei no kanojo wa genjitsu demo yome no tsumori de iru~ - Vol 1', '110000.00', 'Câu chuyện tình cờ giữa Ayanokouji Kazuto – một nam sinh bình thường và Mizuki Rinka – một thần tượng nổi tiếng. Cả hai vốn là \"Cặp đôi\" trong một tựa game online, nhưng sự thật chỉ được phơi bày khi họ quyết định gặp mặt ngoài đời thực. Mối quan hệ của họ dần bước ra khỏi màn hình máy tính. Tình cảm của họ sẽ đi về đâu khi ranh giới giữa game và đời thực dần xóa nhòa?', 'idol_v1.jpg'),
(6, 'Kanojo, okarishimasu - Vol 1', '110000.00', ' Mối quan hệ giả này dần nảy sinh tình cảm thật, kéo theo hàng loạt rắc rối hài hước và lãng mạn.\r\n', 'thue_v1.jpg'),
(7, 'Saekano - Vol 1', '110000.00', 'LightNovel Saekano Hay tên đầy đủ(English):\"How to Raise a Boring Girlfriend\" kể về Aki Tomoya, một học sinh trung học quyết tâm tạo ra một game hẹn hò (Bishōjo game) dựa trên cuộc gặp gỡ định mệnh với Katō Megumi. Cậu thành lập nhóm sản xuất cùng các nữ chính khác để biến Megumi thành \"nữ chính hoàn hảo\" trong game, dẫn đến nhiều tình huống hài hước và lãng mạn. ', 'saekano_v1.jpg'),
(8, 'Arya Bàn Bên - Vol 3', '95000.00', ' Tập trung vào sự phát triển tình cảm của Arya và Masachika sau các sự kiện hội học sinh. Cặp đôi dần thấu hiểu nhau hơn.', 'arya_v3.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `login_time`, `ip_address`) VALUES
(1, 'Sakura', 'Sakura@gmail.com', 'Sakura123456', '2026-03-18 03:12:01', '::1'),
(2, 'Hatsune Miku', 'Miku@gmail.com', 'Miku123456', '2026-03-18 06:35:50', '::1'),
(3, 'Shinto', '1234@gmail.com', 'Shinto123', '2026-03-18 08:33:30', '::1'),
(4, 'Lucy', 'Lucy@gmail.com', 'lucy@1234', '2026-03-18 22:56:32', '::1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
