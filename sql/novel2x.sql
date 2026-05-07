-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 06, 2026 at 11:19 PM
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
-- Database: `novel2x`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(3, 'Lãng mạn - Hài hước / Học đường', '.'),
(4, 'Đời thường / Trò chơi', '.'),
(5, 'Trinh thám / Tâm lý', '.'),
(8, 'Kinh dị / Kỳ ảo ', '.'),
(9, 'Đời thường / Lát cắt cuộc sống', '.'),
(10, 'Khoa học viễn tưởng', '.'),
(11, 'Lịch sử', '.');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `fullname`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Shiina Mahiru', '123@gmail.com', '...', 'Niceヾ(≧▽≦*)o', 0, '2026-03-17 15:55:46'),
(2, 'Miku', 'Miku@gmail.com', '...', 'Good job(●\'◡\'●)', 0, '2026-03-17 16:00:48'),
(3, 'Sakura', 'Sakura@gmail.com', '...', 'Nice(❁´◡`❁)', 0, '2026-03-17 20:36:10'),
(4, 'Lucy', 'Lucy@gmail.com', 'Experience', 'Nice:D', 0, '2026-03-18 16:14:07'),
(5, 'Lucy', 'Lucy@gmail.com', '.', '1', 0, '2026-05-02 16:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `fullname_guest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_guest` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('chờ xác nhận','đã xác nhận','đang giao','đã giao','huỷ') COLLATE utf8mb4_unicode_ci DEFAULT 'đã giao',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `cancel_request` tinyint(1) NOT NULL DEFAULT '0',
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `shipping_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `fullname_guest`, `email_guest`, `order_date`, `total_price`, `status`, `cancel_reason`, `cancel_request`, `shipping_address`, `shipping_phone`, `note`) VALUES
(1, 5, 'Lucy', 'Lucy@gmail.com', '2026-03-28 15:17:00', '780000.00', 'đang giao', NULL, 0, NULL, NULL, NULL),
(2, 6, 'Inori', 'inori@gmail.com', '2026-03-30 13:47:59', '2151000.00', 'đã xác nhận', NULL, 0, NULL, NULL, NULL),
(3, 7, 'Iriya', 'iriya@gmail.com', '2026-03-31 12:23:38', '180000.00', 'huỷ', 'Thay đổi số lượng trong một sản phẩm', 0, NULL, NULL, NULL),
(4, 5, 'Lucy', 'Lucy@gmail.com', '2026-05-06 14:57:07', '452800.00', 'chờ xác nhận', NULL, 0, 'Nghĩa Trang', '0836363636', 'nhu cc'),
(5, 5, 'Lucy', 'Lucy@gmail.com', '2026-05-06 14:57:33', '1831600.00', 'huỷ', 'lòng tôi tan nát khi nhận ra tôi là gay', 0, 'Nghĩa Trang', '0836363636', 'dm'),
(6, 5, 'Lucy', 'Lucy@gmail.com', '2026-05-06 14:58:15', '768200.00', 'đã giao', NULL, 0, 'Nghĩa Trang', '0836363636', 'nhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu ccnhu cc'),
(7, 6, 'Inori', 'inori@gmail.com', '2026-05-06 15:14:20', '768200.00', 'huỷ', 'Muốn đổi địa chỉ giao hàng', 0, 'Nghĩa Địa', '0846333592', ''),
(8, 6, 'Inori', 'inori@gmail.com', '2026-05-06 15:59:42', '460400.00', 'huỷ', 'Thay đổi màu sắc', 0, 'Nghĩa Địa', '0846333592', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `price_at_buy` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_id`, `product_id`, `quantity`, `price_at_buy`) VALUES
(1, 7, 2, '165000.00'),
(1, 8, 1, '450000.00'),
(2, 1, 1, '85000.00'),
(2, 6, 2, '808000.00'),
(2, 8, 1, '450000.00'),
(3, 3, 1, '85000.00'),
(3, 4, 1, '95000.00'),
(4, 12, 1, '452800.00'),
(5, 17, 2, '768200.00'),
(5, 19, 1, '116600.00'),
(5, 20, 1, '178600.00'),
(6, 17, 1, '768200.00'),
(7, 17, 1, '768200.00'),
(8, 18, 1, '460400.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ja` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT '50',
  `description` text COLLATE utf8mb4_unicode_ci,
  `description_en` text COLLATE utf8mb4_unicode_ci,
  `description_ja` text COLLATE utf8mb4_unicode_ci,
  `image_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('hiện','ẩn') COLLATE utf8mb4_unicode_ci DEFAULT 'hiện'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `name_en`, `name_ja`, `price`, `stock_quantity`, `description`, `description_en`, `description_ja`, `image_id`, `status`) VALUES
(1, 3, 'Thiên Sứ Nhà Bên - Vol 1', NULL, NULL, '85000.00', 9, 'Thiên sứ nhà bên (Otonari no Tenshi-sama) kể về Amane, một nam sinh sống một mình, tình cờ đưa ô cho Mahiru người sỡ hữu thành tích mọi mặt nhất trường vào một ngày mưa. Nhờ hành động đó, Mahiru bắt đầu chăm sóc, nấu ăn cho Amane, từ đó cả hai phát triển mối quan hệ tình cảm, chữa lành những vết thương tâm lý và dần thay đổi bản thân theo hướng tích cực hơn', NULL, NULL, 'thiensu_v1.jpg', 'hiện'),
(2, 3, 'Thiên Sứ Nhà Bên - Vol 2', NULL, NULL, '95000.00', 10, 'Mối quan hệ giữa hai người dần trở nên thân thiết hơn.', NULL, NULL, 'thiensu_v2.jpg', 'hiện'),
(3, 3, 'Arya Bàn Bên - Vol 1', NULL, NULL, '85000.00', 10, '\"Arya Bàn Bên Thỉnh Thoảng Lại Trêu Ghẹo Tôi Bằng Tiếng Nga\" là câu chuyện tình học đường hài hước giữa Alisa Mikhailovna Kujō (Arya), nữ sinh người Nhật gốc Nga, tài năng, và người bạn cùng lớp Kuze Masachika. Arya thường dùng tiếng Nga để che giấu sự thẹn thùng khi trêu chọc Kuze, không biết rằng cậu thực chất hiểu được ngôn ngữ này, tạo nên những tình những tình huống ngại ngùng.', NULL, NULL, 'arya_v1.jpg', 'hiện'),
(4, 3, 'Arya Bàn Bên - Vol 2', NULL, NULL, '95000.00', 10, 'Masachika liệu có hiểu được những gì Arya đang nói?', NULL, NULL, 'arya_v2.jpg', 'hiện'),
(5, 4, 'Netoge no Yome ga Ninki Idol datta ken ~Cool-kei no kanojo wa genjitsu demo yome no tsumori de iru~ - Vol 1', NULL, NULL, '110000.00', 10, 'Câu chuyện tình cờ giữa Ayanokouji Kazuto – một nam sinh bình thường và Mizuki Rinka – một thần tượng nổi tiếng. Cả hai vốn là \"Cặp đôi\" trong một tựa game online, nhưng sự thật chỉ được phơi bày khi họ quyết định gặp mặt ngoài đời thực. Mối quan hệ của họ dần bước ra khỏi màn hình máy tính. Tình cảm của họ sẽ đi về đâu khi ranh giới giữa game và đời thực dần xóa nhòa?', NULL, NULL, 'idol_v1.jpg', 'hiện'),
(6, 5, 'Another Monster', NULL, NULL, '808000.00', 8, '\"Cuốn tiểu thuyết trinh thám tâm lý không thể thiếu đối với bất kỳ fan hâm mộ nào của bộ truyện Monster. Dưới góc nhìn của nhà báo Werner Weber, độc giả sẽ được dẫn dắt qua những góc khuất chưa từng được tiết lộ trong Manga/Anime. Từ những bí mật tại Kinderheim 511 đến quá khứ xa xăm tại Tiệp Khắc, mỗi trang sách là một manh mối đưa bạn đến gần hơn với chân dung của \'Kẻ không tên\'. Một tuyệt tác lấp đầy những khoảng trống cuối cùng của huyền thoại.\"', NULL, NULL, 'monster.jpg', 'hiện'),
(7, 8, 'Another Episode S', NULL, NULL, '165000.00', 8, 'Another Episode S không chỉ là một cuốn ngoại truyện, mà là chìa khóa mở ra những góc khuất tâm linh chưa từng được kể về nữ chính Misaki Mei. Trong khi Sakakibara đang phải chống chọi với lời nguyền tại Yomiyama, Mei đã có một chuyến nghỉ hè đầy ám ảnh tại một vùng biệt thự hẻo lánh – nơi cô gặp gỡ một \"linh hồn\" đang tìm kiếm chính xác cái chết của mình.', NULL, NULL, 'Another Episode S.jpg', 'hiện'),
(8, 8, 'KIZUMONOGATARI: Wound Tale', NULL, NULL, '450000.00', 8, 'Kizumonogatari là phần tiền truyện đầy kịch tính mở đầu cho toàn bộ dòng đời của nam sinh Koyomi Araragi trong series Monogatari đình đám của tác giả NISIOISIN. Câu chuyện đưa độc giả quay ngược về kỳ nghỉ xuân định mệnh, nơi Araragi tình cờ bắt gặp Kiss-shot Acerola-orion Heart-under-blade – một nữ vương ma cà rồng huyền thoại đang thoi thóp trong tình trạng mất sạch tứ chi. Để cứu lấy mạng sống của cô, anh đã chấp nhận đánh đổi nhân tính của mình để trở thành một thuộc hạ ma cà rồng, từ đó dấn thân vào những cuộc đối đầu nghẹt thở với ba thợ săn quái vật sừng sỏ nhằm lấy lại các bộ phận cơ thể cho chủ nhân. Với lối dẫn truyện sắc sảo, những màn đối thoại thông minh đầy triết lý xen lẫn yếu tố siêu nhiên kỳ ảo, tác phẩm không chỉ giải mã nguồn gốc sức mạnh của Araragi mà còn khắc họa sâu sắc ranh giới mong manh giữa con người và quái vật, giữa sự hy sinh và lòng ích kỷ.', NULL, NULL, 'kizumonogatari01.jpg', 'hiện'),
(9, NULL, 'Another 2001 Tankobon Hardcover', NULL, NULL, '424500.00', 10, 'Phần mới nhất của loạt truyện kinh dị và bí ẩn học đường cực kỳ nổi tiếng cuối cùng cũng đã được xuất bản!\r\n\r\nNó đã bắt đầu.\r\nLẽ ra chuyện này không nên xảy ra... nhưng tại sao?\r\n\r\n\r\n\r\nAi sẽ là \"nạn nhân\" của năm nay?\r\n\r\nBa năm đã trôi qua kể từ \"thảm họa\" năm 1998, cướp đi sinh mạng của nhiều người.\r\nTrong số những học sinh sẽ trở thành thành viên của Lớp 3-3 trường Trung học cơ sở Yomikita mùa xuân này có Sou, cậu bé đã gặp Misaki Mei vào mùa hè ba năm trước.\r\nNăm nay, Sou và các bạn cùng lớp đã thực hiện những \"biện pháp\" đặc biệt để chuẩn bị cho \"hiện tượng\" \"người chết\" xâm nhập vào lớp, nhưng một sự cố nào đó đã khiến mọi thứ đi sai hướng, và bức màn cuối cùng cũng vén lên hé lộ một bi kịch!\r\nNỗi kinh hoàng của những cái chết vô nghĩa liên tiếp và bí ẩn ngày càng sâu sắc.\r\nSou và Mei sẽ đối mặt với \"thảm họa\" kinh hoàng nhất trong lịch sử \"hiện tượng Yomiyama\" như thế nào?!\r\n\r\nKiệt tác được mong chờ từ lâu của Ayatsuji Yukito với 1200 trang!', NULL, NULL, '717Z8guDGOL._SY425_.jpg', 'hiện'),
(10, NULL, 'Hell\'s Paradise: Jigokuraku, Vol. 6 (6)', NULL, NULL, '380600.00', 10, 'Ngay cả một ninja bất khả chiến bại cũng có thể không sống sót nổi ở Thiên Đường Địa Ngục!\r\n\r\nGabimaru là một ninja bị kết án tử hình, chỉ có một cơ hội duy nhất để gặp lại vợ mình – bằng cách tìm ra thuốc trường sinh bất lão trên một hòn đảo huyền bí và giao nộp nó cho tướng quân. Cản trở anh ta là những tù nhân khác và những con thú hung dữ lang thang trên đảo, ăn thịt hoặc giết chết bất cứ ai chúng gặp.\r\n\r\nHai chiến binh dũng mãnh nhất – Gabimaru Kẻ Rỗng Thù và Aza Chobe Vua Cướp – cuối cùng cũng chạm trán và giao chiến. Hai người đàn ông này ngang tài ngang sức và cực kỳ nguy hiểm… điều đó có nghĩa là cuộc đấu tay đôi của họ sẽ là một trận đấu kinh điển!', NULL, NULL, '81E+FavdGzL._SY385_.jpg', 'hiện'),
(11, NULL, 'Another エピソードS (角川文庫)', NULL, NULL, '153400.00', 10, '.', NULL, NULL, '513dqxTz1CL._SY445_SX342_ML2_.jpg', 'hiện'),
(12, NULL, 'Hell\'s Paradise: Jigokuraku, Vol. 2 (2)', 'Hell\'s Paradise: Jigokuraku, Vol. 2', '地獄楽 2', '452800.00', 9, 'Ngay cả một ninja bất khả chiến bại cũng có thể không sống sót nổi ở Thiên đường Địa ngục!\n\nGabimaru là một ninja bị kết án tử hình, chỉ có một cơ hội duy nhất để gặp lại vợ mình – bằng cách tìm ra thuốc trường sinh bất lão trên một hòn đảo siêu nhiên và giao nó cho tướng quân. Cản trở anh ta là những tù nhân khác và những con quái thú đáng sợ lang thang trên đảo, ăn thịt hoặc giết chết\n\nbất cứ ai chúng gặp. Khi Gabimaru và đao phủ của anh ta khám phá hòn đảo, họ bị tấn công bởi những sinh vật giống thần thánh nhưng lại hành xử như quỷ dữ. Đây là thánh địa hay chính là địa ngục?', NULL, NULL, '81uqY2PZQ0L._SL1500_.jpg', 'hiện'),
(13, NULL, 'Another(下) (角川文庫)', 'Another (Part 2)', 'Another (下)', '160300.00', 10, '\"Hãy cẩn thận. Mọi chuyện có thể đã bắt đầu rồi.\"\r\n\r\nKoichi theo đuổi bí ẩn cùng cô gái xinh đẹp và bí ẩn, Misaki Mei. Nhưng \"hiện tượng\" vẫn tiếp diễn mà họ không thể tìm ra sự thật. Và sự thật nào đang chờ đợi họ tại chuyến cắm trại định mệnh của lớp trong kỳ nghỉ hè!? Một kết thúc gây sốc cho bộ phim kinh dị đỉnh cao đánh dấu sự kết thúc của thập niên 2000.', NULL, NULL, '415ICRO8pYL._SY445_SX342_ML2_.jpg', 'hiện'),
(14, NULL, '殺人鬼 ‐‐覚醒篇 (角川文庫 あ 45-5)', NULL, NULL, '164000.00', 10, 'Vào một mùa hè những năm 90, một nhóm \"Thành viên TC\" tụ tập tại Futabayama, chỉ để rồi bị một sát thủ bất ngờ xuất hiện và sát hại dã man từng người một... Một bữa tiệc địa ngục không có hồi kết. Điều bất ngờ kinh hoàng nào đang ẩn giấu dưới bề mặt?', NULL, NULL, '41JW63K3TFL._SY445_SX342_ML2_.jpg', 'hiện'),
(15, NULL, 'Another(上) (角川文庫)', NULL, NULL, '167600.00', 10, 'Koichi Sakakibara, một học sinh chuyển trường đến lớp 3-3 của trường THCS Yomiyama North, cảm thấy bất an về bầu không khí trong lớp, dường như tràn ngập sự sợ hãi. Cậu bị thu hút bởi Misaki Mei, một cô gái xinh đẹp trong lớp, người toát lên một khí chất bí ẩn, và cố gắng tiếp cận cô ấy, nhưng sự bí ẩn càng thêm sâu sắc. Sau đó, lớp trưởng Sakuragi lại chết một cách dã man! Chuyện gì đang thực sự xảy ra trong \"thế giới\" này!?', NULL, NULL, '41-MlfibiPL._SY445_SX342_ML2_.jpg', 'hiện'),
(16, NULL, '黄昏の囁き 〈新装改訂版〉 (講談社文庫 あ 52-32)', NULL, NULL, '156700.00', 10, 'Nghi ngờ về cái chết đột ngột của anh trai mình, sinh viên y khoa Shoji nhờ sự giúp đỡ của Urabe, người từng là giáo viên dạy thêm, để tìm ra sự thật đằng sau vụ việc.\r\n\"Này, chơi nào\"... Ai là kẻ sát nhân gây ra những tội ác tàn bạo bằng những lời nói bí ẩn này?\r\nSự thật kinh hoàng nào ẩn sau những ký ức tuổi thơ mà Shoji đã chôn giấu trong lòng?\r\nPhần thứ ba trong loạt truyện nổi tiếng này, cùng với loạt truyện \"Biệt thự\", nay đã có phiên bản được chỉnh sửa mới.', NULL, NULL, '71S5E6fNYKL._SY385_.jpg', 'hiện'),
(17, NULL, 'Summertime Rendering Volume 1', NULL, NULL, '768200.00', 9, '\"Phim hoạt hình siêu nhiên của năm, Phim hoạt hình bí ẩn hoặc tâm lý của năm, Nam chính của năm, Nữ phụ của năm.\" Nghe tin người bạn thời thơ ấu Ushio qua đời, Shinpei trở về quê nhà trên hòn đảo xa xôi Hitogashima để dự đám tang. Cậu không hề biết rằng đó là khởi đầu của một mùa hè đầy bí ẩn và kinh dị! Không ai chuẩn bị cho những cuộc phiêu lưu xuyên thời gian đầy mạo hiểm, thách thức cả sự sống và cái chết!\"', NULL, NULL, '81romt2TExL._SY385_.jpg', 'hiện'),
(18, NULL, 'Hell\'s Paradise: Jigokuraku, Vol. 1', NULL, NULL, '460400.00', 10, 'Gabimaru Rỗng Thù là một trong những sát thủ tàn bạo nhất từng xuất thân từ làng ninja Iwagakure. Hắn ta tàn nhẫn và hiệu quả, nhưng một sự phản bội đã khiến hắn bị kết án tử hình. Hắn chỉ có một hy vọng duy nhất—để giành lại tự do, hắn phải đến một hòn đảo bí ẩn từ lâu và tìm lại một loại thần dược có thể giúp tướng quân bất tử. Thất bại không phải là một lựa chọn. Trên hòn đảo này, thiên đường và địa ngục chỉ cách nhau một khoảng rất nhỏ.', NULL, NULL, '81keV50g-yL._SY385_.jpg', 'hiện'),
(19, NULL, '霧越邸殺人事件<完全改訂版>(上) (角川文庫)', NULL, NULL, '116600.00', 10, 'Từ Yukito Ayatsuji, tác giả của \"Một Thế Giới Khác,\" lại đến một kiệt tác khác. Phiên bản được chỉnh sửa hoàn toàn nay đã có mặt!\r\n\r\n\"Dinh thự Kirigoe,\" một dinh thự bí ẩn theo phong cách phương Tây nằm ẩn mình trong dãy núi Shinshu. Những cư dân đáng ngờ chào đón đoàn kịch \"Lều Đen\" khi họ đến thăm. Một loạt hiện tượng khó hiểu xảy ra bên trong dinh thự... Trong \"nhà nghỉ trên núi bị bao phủ bởi bão tuyết\" hẻo lánh này, một loạt vụ án mạng hấp dẫn sớm được hé lộ!', NULL, NULL, '510mPBayuPL._SY445_SX342_ML2_.jpg', 'hiện'),
(20, NULL, '緋色の囁き 〈新装改訂版〉 (講談社文庫 あ 52-30)', NULL, NULL, '178600.00', 10, 'Ai mới là \"phù thủy\" thực sự?\r\n\r\nMột nữ sinh chết trong \"căn phòng cấm\" của Học viện Nữ sinh Seishin danh tiếng, để lại lời nói bí ẩn \"phù thủy\".\r\nĐiều này đánh dấu sự khởi đầu của một loạt vụ án mạng vừa đẹp đẽ vừa tàn khốc. Bí mật của \"ký ức đỏ\" ẩn sâu trong trái tim của nữ sinh chuyển trường Saeko.\r\nKẻ giết người tấn công các nữ sinh mỗi đêm là ai?\r\n\r\nPhần đầu tiên của loạt truyện \"Những lời thì thầm\", nhuốm màu máu và sự điên loạn, nay đã có phiên bản mới được chỉnh sửa mà người đọc mong chờ từ lâu.', NULL, NULL, '7152p6BuI0L._SY385_.jpg', 'hiện');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`) VALUES
(1, 10, '81yGWblIaBL._SY385_.jpg'),
(2, 18, '91PL6hpLuzL._SY385_.jpg'),
(3, 12, '91UowNs0l1L._SL1500_.jpg'),
(5, 20, '51+rrFMzeuL._SY385_.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cart_data` text COLLATE utf8mb4_unicode_ci,
  `fullname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default_avatar.png',
  `login_attempts` int(11) DEFAULT '0',
  `last_attempt_time` datetime DEFAULT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  `is_locked` tinyint(1) DEFAULT '0',
  `locked_at` datetime DEFAULT NULL,
  `locked_reason` text COLLATE utf8mb4_unicode_ci,
  `last_activity` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `created_at`, `cart_data`, `fullname`, `phone`, `address`, `avatar`, `login_attempts`, `last_attempt_time`, `reset_token`, `reset_expire`, `is_locked`, `locked_at`, `locked_reason`, `last_activity`) VALUES
(1, 'NCT@gmail.com', '$2y$10$HdrI0LcaGtUjmlLQ6R7Po.QZQvErexvWGJAhnncNM6PIXyBIZDcc2', 'admin', '2026-03-27 21:49:32', NULL, 'Admin NCT', '', '', '7377cbd09d553ca19df0b8b19f8293c5.jpg', 5, '2026-04-14 18:34:11', NULL, NULL, 0, NULL, NULL, '2026-05-07 06:18:22'),
(2, 'Sakura@gmail.com', '$2y$10$3FAH.CNsWhdXS9aFRvMc/OIBS0wnYVL2ym1L7Jj7LGDTCbP4CPz/.', 'customer', '2026-03-27 21:49:32', NULL, 'Sakura', '', NULL, 'default_avatar.png', 0, NULL, NULL, NULL, 0, NULL, NULL, '2026-04-16 22:06:57'),
(3, 'Miku@gmail.com', '$2y$10$.sAfxGLdLu2Eln7hH60y7ua9d.vevHfEVhMXHnoN/jmxG6fETJHVy', 'customer', '2026-03-27 21:49:32', NULL, 'Hatsune Miku', '', NULL, 'default_avatar.png', 0, NULL, NULL, NULL, 0, NULL, NULL, '2026-05-02 20:12:33'),
(4, '1234@gmail.com', '$2y$10$FExjlkMVuLsNPUxMZokP3uh4RR2iGoo/H5otrzxkFKGGAVVSKUdzC', 'customer', '2026-03-27 21:49:32', NULL, 'Shinto', '', NULL, 'default_avatar.png', 0, NULL, NULL, NULL, 0, NULL, NULL, '2026-05-02 20:16:32'),
(5, 'Lucy@gmail.com', '$2y$10$sQsu0pmLVjnJFCAWaK/YkOByJS6yZ.ThqGvqJZkvvwu8t7pTDKiPa', 'customer', '2026-03-27 21:49:32', NULL, 'Lucy', '0836363636', 'Nghĩa Trang', '5a9c9cfee81db24795db14fcfe78eb64.jpg', 0, NULL, '211367', '2026-04-14 16:41:52', 0, NULL, NULL, '2026-05-07 06:17:50'),
(6, 'inori@gmail.com', '$2y$10$KcxylTDFUA5XCPJp4LDK9OP1DdAAWgzU93i.oPfRF8E/RHW9mdqXm', 'customer', '2026-03-30 13:45:10', '{\"17\":1}', 'Inori', '0846333592', 'Nghĩa Địa', '9d856426a844121b024ecb53b91db896.png', 0, NULL, '456554', '2026-04-14 16:04:29', 0, NULL, NULL, '2026-05-06 23:02:02'),
(7, 'iriya@gmail.com', '$2y$10$pV08HHOoMF.RJ6diqflFNeywJKU1WT4oTRJ6jLfUvxirk9HzKdNPW', 'customer', '2026-03-31 12:22:52', NULL, 'Iriya', '0876374412', '77 Massachusetts Avenue/Cambridge/Massachusetts/USA', '8b275064177f31c5b44ff2f6027e313e.jpg', 0, NULL, NULL, NULL, 0, NULL, NULL, '2026-05-02 20:09:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
