-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 26, 2026 at 10:39 AM
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
(6, 'Another Monster', '808000.00', '\"Cuốn tiểu thuyết trinh thám tâm lý không thể thiếu đối với bất kỳ fan hâm mộ nào của bộ truyện Monster. Dưới góc nhìn của nhà báo Werner Weber, độc giả sẽ được dẫn dắt qua những góc khuất chưa từng được tiết lộ trong Manga/Anime. Từ những bí mật tại Kinderheim 511 đến quá khứ xa xăm tại Tiệp Khắc, mỗi trang sách là một manh mối đưa bạn đến gần hơn với chân dung của \'Kẻ không tên\'. Một tuyệt tác lấp đầy những khoảng trống cuối cùng của huyền thoại.\"', 'monster.jpg'),
(7, 'Another Episode S', '165000.00', 'Another Episode S không chỉ là một cuốn ngoại truyện, mà là chìa khóa mở ra những góc khuất tâm linh chưa từng được kể về nữ chính Misaki Mei. Trong khi Sakakibara đang phải chống chọi với lời nguyền tại Yomiyama, Mei đã có một chuyến nghỉ hè đầy ám ảnh tại một vùng biệt thự hẻo lánh – nơi cô gặp gỡ một \"linh hồn\" đang tìm kiếm chính xác cái chết của mình.', 'Another Episode S.jpg'),
(8, 'KIZUMONOGATARI: Wound Tale', '450000.00', 'Kizumonogatari là phần tiền truyện đầy kịch tính mở đầu cho toàn bộ dòng đời của nam sinh Koyomi Araragi trong series Monogatari đình đám của tác giả NISIOISIN. Câu chuyện đưa độc giả quay ngược về kỳ nghỉ xuân định mệnh, nơi Araragi tình cờ bắt gặp Kiss-shot Acerola-orion Heart-under-blade – một nữ vương ma cà rồng huyền thoại đang thoi thóp trong tình trạng mất sạch tứ chi. Để cứu lấy mạng sống của cô, anh đã chấp nhận đánh đổi nhân tính của mình để trở thành một thuộc hạ ma cà rồng, từ đó dấn thân vào những cuộc đối đầu nghẹt thở với ba thợ săn quái vật sừng sỏ nhằm lấy lại các bộ phận cơ thể cho chủ nhân. Với lối dẫn truyện sắc sảo, những màn đối thoại thông minh đầy triết lý xen lẫn yếu tố siêu nhiên kỳ ảo, tác phẩm không chỉ giải mã nguồn gốc sức mạnh của Araragi mà còn khắc họa sâu sắc ranh giới mong manh giữa con người và quái vật, giữa sự hy sinh và lòng ích kỷ.', 'kizumonogatari01.jpg');

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
