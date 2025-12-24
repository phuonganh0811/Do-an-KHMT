-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 24, 2025 at 06:11 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doctruyen`
--

-- --------------------------------------------------------

--
-- Table structure for table `binh_luan`
--

CREATE TABLE `binh_luan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `id_truyen` bigint UNSIGNED DEFAULT NULL,
  `id_chuong` bigint UNSIGNED DEFAULT NULL,
  `id_cha` bigint UNSIGNED DEFAULT NULL,
  `noi_dung` text COLLATE utf8mb4_general_ci NOT NULL,
  `bi_an` tinyint(1) DEFAULT '0',
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chuong_truyen`
--

CREATE TABLE `chuong_truyen` (
  `id` bigint UNSIGNED NOT NULL,
  `id_truyen` bigint UNSIGNED NOT NULL,
  `so_chuong` int NOT NULL,
  `tieu_de` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `noi_dung` longtext COLLATE utf8mb4_general_ci,
  `gia` decimal(10,0) NOT NULL DEFAULT '0',
  `la_tra_phi` tinyint(1) NOT NULL DEFAULT '0',
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `luot_xem` bigint UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chuong_truyen`
--

INSERT INTO `chuong_truyen` (`id`, `id_truyen`, `so_chuong`, `tieu_de`, `slug`, `noi_dung`, `gia`, `la_tra_phi`, `ngay_tao`, `ngay_cap_nhat`, `luot_xem`) VALUES
(2, 3, 1, ' Nhà trọ Tam Sinh · Trò Chơi Kỷ Nguyên', 'c1', 'Bức tường ố vàng, từng mảng vôi lớn bong tróc, để lộ những vết tích loang lổ bên dưới. Quầy tiếp tân đơn độc đặt sát vách tường, bên trong trống rỗng, chẳng thấy một bóng người.\r\nChỉ có trên bức tường xám xịt phía sau quầy, mấy chữ to “Nhà trọ Tam Sinh” viết bằng sơn đỏ được chiếu rọi bởi chiếc đèn huỳnh quang chập chờn tiếp xúc kém trên đầu, ánh sáng trắng bệch khiến chúng càng thêm nổi bật.\r\nLúc này, trong đại sảnh trống trải tan hoang ấy thực ra cũng không hoàn toàn là trống rỗng. Vài bóng người phân ranh giới rất rõ ràng, mỗi người chiếm một góc nhỏ, hoảng hốt đảo mắt quan sát xung quanh.\r\nCuối cùng, ánh mắt họ vẫn không thể tránh khỏi mà chạm nhau, những gương mặt hoang mang, sợ hãi, cảnh giác như được khắc ra từ cùng một khuôn.\r\nKhông khí đặc quánh, chỉ còn tiếng “tách tách” yếu ớt của dòng điện trong ống đèn huỳnh quang.\r\nCuối cùng, một thanh niên ăn mặc thời thượng dường như không thể chịu nổi áp lực vô hình này nữa, giọng khô khốc run rẩy: “Đây… Đây rốt cuộc là chỗ quái quỷ nào vậy? Mấy người là ai? Tại sao tôi lại ở đây?”\r\n“Tôi không biết, tôi chỉ nhớ là mình đang tăng ca, lúc mở mắt ra thì đã ở đây rồi.”\r\nNgười đáp lời là một người đàn ông trung niên râu ria xồm xoàm, mặt đầy oán khí, cả người toát ra khí chất đặc trưng của một nô lệ tư bản dày đặc thâm niên.\r\n“Chẳng lẽ trước đó các người không nhận được thông báo sao?”\r\nNgười nói lần này là một phụ nữ ăn mặc gọn gàng, uốn tóc xoăn sóng lớn. \r\nSo với những người khác, cô ấy trông bình tĩnh hơn rất nhiều.\r\n“Thông… Thông báo gì?” Có người yếu ớt hỏi.\r\nNgười đàn ông trung niên cũng đồng thời kinh ngạc thốt lên: “Cô cũng thấy cái thông báo đó à?”\r\nCâu nói này như một viên đá ném xuống hồ nước chết, khơi dậy ký ức liên quan trong đầu mọi người.\r\n“Thông báo…”\r\n“Là cái thông báo tử vong đó sao?”\r\n“Xem ra mọi người đều nhận được.”\r\n“Tôi… Tôi còn tưởng ai đó dùng hologram đùa thôi…”\r\nTrước đó không lâu, bọn họ đều nhìn thấy một dòng thông báo từ hư không xuất hiện ngay trước mắt.\r\n【Bạn sẽ chết vì XX vào lúc XXXX ngày XX tháng XX năm XXXX. Bạn có tự nguyện tham gia ‘Trò chơi Kỷ Nguyên’ để có thêm một cơ hội kéo dài tính mạng không? Có / Không】\r\nThời gian tử vong chính xác đến từng giây.\r\nCòn nêu rõ nguyên nhân cái chết.\r\nTrong số họ, có người bấm “Có”.\r\nCó người bấm chọn “Không”.\r\nCó người cảm thấy bản thân đi làm/đi học đến mức sinh ra ảo giác, bỏ qua thông báo này mà chạy đi khám bác sĩ.\r\nNhưng bất kể có đưa ra lựa chọn hay không, kết cục cuối cùng vẫn giống nhau.\r\nBởi vì ngoài việc tự nguyện, còn có cả không tự nguyện.\r\nTất nhiên, cũng có người tin vào thông báo ấy, cố gắng thay đổi quỹ đạo hành động của mình để tránh cái chết ập tới.\r\nThế nhưng cuối cùng vẫn vì đủ loại trùng hợp mà chết đúng như lời báo trước.\r\nCó người có thể nhớ rõ cảnh tượng thảm khốc khi chết, có người chẳng có ấn tượng gì, vừa mở mắt đã đến nơi này.\r\n“Vậy là giờ chúng ta đã chết, trò chơi này cho chúng ta sống lại trong game, nhưng tôi rõ ràng vẫn còn nhiệt độ cơ thể, vẫn cảm nhận được đau đớn.”\r\n“…”\r\n“Theo như thông báo kia nói… Chúng ta phải chơi game thì mới kéo dài được mạng sống.”\r\n“Vậy… Phải chơi thế nào?”\r\n“Game thì còn chơi thế nào, chẳng phải cũng chỉ có mấy kiểu đó thôi sao, đánh quái, thăng cấp, vượt ải. Nhưng xét tình hình của chúng ta bây giờ, rất có thể trò này không quá thân thiện đâu.”\r\n“Không… Cái trò chơi quái quỷ gì… Là giả! Là giả! Tất cả đều là giả… Chắc chắn là tôi đang nằm mơ, các người đều là giả! Tôi không tin…”\r\nCậu thanh niên ăn mặc thời thượng lúc này như bị nỗi sợ vô hình xé toang lý trí, hai mắt đỏ ngầu, lắc đầu lẩm bẩm.\r\nGiây tiếp theo, cậu ta bất chấp tất cả, lao thẳng về phía cánh cửa sắt hoen rỉ không xa.\r\nTất cả diễn ra quá nhanh, mọi người thậm chí còn chưa kịp kêu lên, chỉ thấy bóng người ấy đã lao đến trước cửa, nắm lấy cánh cửa sắt, dùng hết sức kéo mạnh.\r\n“Két ——”\r\nÂm thanh rợn người xé toạc không khí.\r\nNgay khoảnh khắc cánh cửa sắt hé ra một khe hở, cơ thể cậu thanh niên “bùm” một tiếng nổ tung, máu nóng bắn tung tóe lên người đứng gần cậu ta nhất.\r\n“A!”\r\nTiếng hét kinh hoàng vang lên, kèm theo âm thanh nặng nề của miếng thịt rơi xuống đất, phá vỡ sự tĩnh lặng trong đại sảnh.\r\n“Chết… Chết người rồi!”\r\nCảnh tượng đẫm máu đột ngột này đã nghiền nát tia lý trí cuối cùng của tất cả mọi người.\r\nHọ không còn để tâm đến khoảng cách an toàn đầy phòng bị mà theo bản năng tiến lại gần nhau, nỗi sợ hãi như cỏ dại điên cuồng mọc lên trong lòng mỗi người.\r\n“Cậu ta chết rồi…” \r\n“Sao có thể như vậy.”\r\n“Xem ra chúng ta không thể rời khỏi đây.”\r\n“Giờ phải làm sao?”\r\n“Tôi không muốn chết thêm lần nữa đâu…” \r\nCó một ví dụ sống sờ sờ ngay trước mắt, không ai dám lại gần cánh cửa sắt, càng không dám nhắc đến chuyện rời đi.\r\nBọn họ chỉ là bị dọa đến ngây ra chứ không phải ngu ngốc thật sự.\r\nNgười phụ nữ tóc uốn xoăn sóng lớn cố gắng giữ bình tĩnh, lên tiếng bảo mọi người tụ lại để bàn bạc xem tiếp theo nên làm gì.\r\n“Dù là trò chơi thì chắc hẳn cũng sẽ có gợi ý hoặc NPC chỉ dẫn. Nhưng bây giờ ngoài đại sảnh này ra thì chẳng có gì hết…” \r\n“Chuyện đó… Sao anh ta không qua đây?” Cô gái đội tai thỏ lông xù run rẩy chỉ về phía bóng tối không xa.\r\n“Đâu còn ai nữa, chẳng phải chỉ có tám người chúng ta… À, không đúng, giờ là bảy.” Người nói liếc nhìn theo hướng cô gái tai thỏ chỉ: “Chẳng lẽ là cậu thanh niên vừa nãy quay lại…” \r\nNguồn sáng duy nhất trong đại sảnh phát ra từ ống đèn huỳnh quang phía trên quầy lễ tân.\r\nLuồng sáng yếu ớt đó chỉ miễn cưỡng chiếu rõ được phạm vi một mét quanh quầy.\r\nNhưng trong đại sảnh vẫn còn một khung cửa sổ, cả không gian cũng không hoàn toàn rơi vào bóng tối.\r\nNếu nhìn kỹ, vẫn có thể lờ mờ thấy một vài đường nét.\r\nVí dụ như trên chiếc ghế dài được bóng tối phác họa ra kia, quả thật có một bóng người.\r\nCô Thẩm Nguyên Hương, người phụ nữ tóc xoăn sóng to nhíu mày. Ban đầu lúc quan sát môi trường, cô ấy rõ ràng đã nhìn thấy chiếc ghế dài ở phía đó.\r\nNhưng cô ấy cũng rất chắc chắn rằng, trước đó mình không thấy bất cứ thứ gì trên ghế.\r\nBóng người này chắc chắn là mới xuất hiện.\r\n“Là người hay là ma vậy…”\r\n“Không lẽ thật sự là cậu thanh niên vừa nãy?”\r\n“Trong game còn có thể sống lại à?”\r\n“Đừng, đừng hỏi tôi, tôi tôi tôi… Tôi làm sao biết được.”\r\nMọi người thì thầm bàn tán, ai cũng thò đầu ra quan sát một hồi lâu nhưng chẳng ai dám bước tới gần.\r\n“Này! Anh là người à?” Một người gan lớn cất tiếng thử gọi về phía đó.\r\nTĩnh lặng ——\r\nKhông ai đáp lại.\r\n“Sao lại không nhúc nhích chút nào vậy, chẳng lẽ không nghe thấy chúng ta nói?”\r\n“Có cần qua đó xem thử không?”\r\n“Tôi không dám, cậu đi đi.”\r\n“Anh không dám thì tôi dám chắc? Ai biết đó là cái thứ gì chứ, lỡ nó không giống chúng ta, là NPC trong game thì sao?”\r\nHiện giờ trong đại sảnh vẫn chưa xuất hiện thứ gì khác, bọn họ cũng không biết tiếp theo phải làm gì.\r\nGiờ đây, bóng người kia là điểm bất thường duy nhất.\r\nNếu đó là NPC trong game thì họ cũng phải giao tiếp với đối phương mới có thể lấy được manh mối mới.\r\nVì thế, dưới sự trấn an của Thẩm Nguyên Hương, tuy trong lòng mọi người đều không muốn nhưng dưới tình huống đi cùng nhau mới an toàn, bọn họ chỉ đành tiến lại gần về phía chiếc ghế dài.\r\nĐến gần, bọn họ mới thấy rõ trên băng ghế quả thật có một người ngồi.\r\nLại còn là một cô gái.\r\nCô khoanh tay trước ngực, đầu hơi cúi xuống, tóc mái lòa xòa che khuất gương mặt, hoàn toàn không nhìn rõ được dung mạo.\r\nCô cứ thế ngồi im bất động ở đó.\r\nTiếng động khi bọn họ tiến lại gần hoàn toàn chẳng khiến cô chú ý.\r\n“Mọi người nói xem, có khi nào đây là kiểu cốt truyện truy tìm hung thủ trong phòng kín không? Đây là thi thể, còn hung thủ thì đang ở trong số chúng ta.” Cô gái tai thỏ vốn thích xem phim, đọc tiểu thuyết, không kìm được mà đưa ra giả thuyết của mình.\r\nMọi người theo phản xạ quay đầu nhìn cô gái tai thỏ.\r\nTrái tim cô gái tai thỏ chợt thót lại, vội xua tay, giọng run run nói: “Tôi… Tôi chỉ nói bừa thôi.”\r\nThẩm Nguyên Hương quét mắt một vòng nhìn tất cả mọi người, chỉ cảm thấy đám này chẳng ai đáng tin.\r\nCô ấy thở ra một hơi, rồi lại hít sâu một hơi, dưới ánh mắt nghi hoặc của mọi người, cô ấy đưa tay ra định thử xem “thi thể” kia còn thở không.\r\nChưa kịp dò hơi thở, cổ tay Thẩm Nguyên Hương bỗng nặng trĩu xuống.\r\nBàn tay mang theo hơi ấm, sức lực mạnh đến kinh người, như chiếc kìm sắt kẹp chặt lấy cổ tay cô ấy, cô ấy theo bản năng giật mạnh về nhưng lại hoàn toàn không nhúc nhích nổi.\r\nSống!\r\nNgay khi ý nghĩ này vừa lướt qua đầu Thẩm Nguyên Hương, một giọng nữ mang theo vài phần uể oải lười biếng xé tan màn đêm ——\r\n“Ầm ĩ xong chưa?”\r\n\r\n***\r\n***\r\nKhông phải nội dung chính · Hướng dẫn đọc cho người mới.\r\n【Hệ thống nhắc nhở: Phát hiện ý thức mới đang tải vào…】\r\n【Cảnh báo: Bạn đã bước vào thế giới nguy hiểm cao độ —— NPC khốn khổ chạy trốn trong trò chơi kinh dị.】\r\n【Cưỡng chế tiếp thu: Hướng dẫn đọc (bắt buộc)】\r\nXin hãy nghiêm túc đọc kỹ nội dung sau:\r\n1, Trước khi đọc, hãy lập tức gửi “mô-đun logic thường thức” của bạn vào khu vực an toàn, sau khi đọc xong nhớ lấy ra kịp thời.\r\n2, Thế giới này hoàn toàn hư cấu, nếu phát hiện bất cứ điểm nào tương đồng với logic đời thực, hãy lập tức nhắm mắt và bấm chuyển trang.\r\n3, Thế giới này lấy nữ chính làm trung tâm, nữ chính là chân lý, vừa ra sân đã ở đỉnh cao, xin đừng cố thay đổi quy tắc thế giới, can thiệp tình tiết… Nếu thực sự không thể chấp nhận, hãy lập tức thoát ra và xóa kênh truy cập.\r\n4, Tác phẩm này chỉ để giải trí, không chứa bất kỳ ý nghĩa phản ánh hiện thực, giáo dục đạo đức hay phê phán xã hội nào, xin đừng cố gắng phân tích nâng tầm ý nghĩa; vi phạm có thể gây quá tải tinh thần.\r\n5, Đây không phải thể loại giải đố, những độc giả ưa thích suy luận logic cao độ, xin hãy lập tức từ bỏ việc khám phá, ở lại tiếp tục sẽ gây chênh lệch kỳ vọng nghiêm trọng và tiêu hao năng lượng vô ích.\r\n6, Hãy giám sát sự tương thích giữa hệ giá trị cốt lõi của bạn và thế giới này, nếu phát hiện xung đột/khó chịu, bỏ truyện là phương án duy nhất để giảm thiệt hại.\r\n7, Cấm gửi bất kỳ đề xuất nào không liên quan đến logic tình tiết (dù tác giả cũng chẳng có logic gì).\r\n8, Khi gặp độc giả có sở thích khác biệt, xin tuân thủ nghiêm ngặt: cấm dìm, cấm công kích, cấm ép đồng hóa. Nhất định phải tôn trọng sự khác biệt về sở thích, chấp nhận và bao dung sự đa dạng.\r\n9, Hòa bình thế giới, cùng nhau tạo dựng một thế giới tươi đẹp.\r\n【 Hướng dẫn tải xong. 】\r\n▷ Nếu bạn đã đọc kỹ và đồng ý với nội dung trên, hãy bấm sang trang tiếp theo để tiếp tục đọc.\r\n▷ Nếu bạn đã đọc kỹ nhưng không đồng ý, hãy thoát truyện và trở về trang chủ.\r\n▷ Nếu bạn bỏ qua nội dung trên, mọi nhu cầu cá nhân phát sinh sau này, xin tự giải quyết.\r\n', 0, 0, '2025-12-22 20:54:09', '2025-12-23 18:42:42', 35),
(3, 3, 2, 'c2', 'c2', 'c', 0, 1, '2025-12-22 20:57:49', '2025-12-23 16:53:59', 6),
(4, 4, 1, 'Chương 1', 'chng-1', '1', 0, 0, '2025-12-22 23:28:10', '2025-12-23 12:29:43', 9),
(5, 4, 2, 'e', 'e', 'e', 300, 1, '2025-12-23 00:17:05', '2025-12-24 00:41:19', 8),
(6, 3, 3, 'r', 'r', 'r', 500, 1, '2025-12-23 00:30:52', '2025-12-24 00:40:28', 8),
(7, 3, 4, 're', 're', '4', 100, 1, '2025-12-23 00:32:36', '2025-12-24 00:40:31', 7),
(8, 5, 1, 'hehe', 'hehe', 'eeeeeeeeeeeeeeeee', 0, 0, '2025-12-23 21:10:42', '2025-12-23 21:10:42', 0),
(9, 5, 2, '2222', '2222', '2', 100, 1, '2025-12-24 00:34:14', '2025-12-24 00:40:24', 3);

-- --------------------------------------------------------

--
-- Table structure for table `doanh_thu_he_thong`
--

CREATE TABLE `doanh_thu_he_thong` (
  `id` bigint NOT NULL,
  `id_chuong` bigint NOT NULL,
  `so_tien` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doanh_thu_he_thong`
--

INSERT INTO `doanh_thu_he_thong` (`id`, `id_chuong`, `so_tien`, `created_at`) VALUES
(1, 6, 250, '2025-12-22 17:31:14'),
(2, 7, 50, '2025-12-22 17:33:07'),
(3, 0, 2000, '2025-12-23 11:01:09'),
(4, 7, 30, '2025-12-23 11:13:43'),
(5, 6, 150, '2025-12-23 11:16:09'),
(6, 0, 2000, '2025-12-23 11:29:04'),
(7, 6, 150, '2025-12-23 11:29:48'),
(8, 7, 30, '2025-12-23 11:42:52'),
(9, 9, 30, '2025-12-23 17:34:25'),
(10, 9, 30, '2025-12-23 17:40:02'),
(11, 5, 90, '2025-12-23 17:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `donate`
--

CREATE TABLE `donate` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_gui` bigint UNSIGNED NOT NULL,
  `id_tac_gia` bigint UNSIGNED NOT NULL,
  `id_truyen` bigint UNSIGNED DEFAULT NULL,
  `id_chuong` bigint UNSIGNED DEFAULT NULL,
  `so_tien` decimal(13,2) NOT NULL,
  `loi_nhan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thoi_gian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `giao_dich`
--

CREATE TABLE `giao_dich` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `loai` enum('nap_tien','mua_chuong','donate','rut_tien','hoan_tien','phi_he_thong') COLLATE utf8mb4_general_ci NOT NULL,
  `id_lien_quan` bigint UNSIGNED DEFAULT NULL,
  `so_tien` decimal(13,2) NOT NULL,
  `so_du_truoc` decimal(13,2) NOT NULL,
  `so_du_sau` decimal(13,2) NOT NULL,
  `ghi_chu` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `thoi_gian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mua_chuong`
--

CREATE TABLE `mua_chuong` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `id_chuong` bigint UNSIGNED NOT NULL,
  `so_tien` decimal(10,2) NOT NULL,
  `thoi_gian` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ma_giao_dich` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mua_chuong`
--

INSERT INTO `mua_chuong` (`id`, `id_nguoi_dung`, `id_chuong`, `so_tien`, `thoi_gian`, `ma_giao_dich`) VALUES
(1, 2, 3, 0.00, '2025-12-23 00:03:18', 'GD_694979d6bb476'),
(2, 2, 5, 300.00, '2025-12-23 00:17:15', 'GD_69497d1b0e470'),
(3, 3, 5, 300.00, '2025-12-23 00:24:56', 'GD_69497ee8465c6'),
(4, 3, 6, 500.00, '2025-12-23 00:31:14', 'GD_69498062adf86'),
(5, 3, 7, 100.00, '2025-12-23 00:33:07', 'GD_694980d3d1e33'),
(6, 2, 7, 100.00, '2025-12-23 18:13:43', 'GD_694a796747e24'),
(7, 2, 6, 500.00, '2025-12-23 18:16:09', 'GD_694a79f9a0b0d'),
(8, 4, 6, 500.00, '2025-12-23 18:29:48', 'GD_694a7d2c36959'),
(9, 4, 7, 100.00, '2025-12-23 18:42:52', 'GD_694a803c0d0e2'),
(10, 2, 9, 100.00, '2025-12-24 00:34:25', 'GD_694ad2a16c674'),
(11, 4, 9, 100.00, '2025-12-24 00:40:02', 'GD_694ad3f2b0d01'),
(12, 4, 5, 300.00, '2025-12-24 00:41:19', 'GD_694ad43fa53c1');

-- --------------------------------------------------------

--
-- Table structure for table `nap_tien`
--

CREATE TABLE `nap_tien` (
  `id` bigint UNSIGNED NOT NULL,
  `id_user` bigint UNSIGNED NOT NULL,
  `so_tien` int NOT NULL,
  `ma_nap` varchar(50) NOT NULL,
  `trang_thai` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `nap_tien`
--

INSERT INTO `nap_tien` (`id`, `id_user`, `so_tien`, `ma_nap`, `trang_thai`, `created_at`) VALUES
(1, 2, 10000, 'NAP1766421886656', 'da_duyet', '2025-12-22 16:44:46'),
(2, 2, 10000, 'NAP1766470202562', 'da_duyet', '2025-12-23 06:10:02'),
(3, 3, 10000, 'NAP1766485643732', 'da_duyet', '2025-12-23 10:27:23'),
(4, 3, 10000, 'NAP1766485644780', 'da_duyet', '2025-12-23 10:27:24'),
(5, 3, 10000, 'NAP1766485646925', 'da_duyet', '2025-12-23 10:27:26'),
(6, 3, 1000000, 'NAP1766485651660', 'da_duyet', '2025-12-23 10:27:31'),
(7, 3, 500000, 'NAP1766485655514', 'da_duyet', '2025-12-23 10:27:35'),
(8, 3, 500000, 'NAP1766486105104', 'da_duyet', '2025-12-23 10:35:05'),
(9, 3, 10000, 'NAP1766486106334', 'da_duyet', '2025-12-23 10:35:06'),
(10, 3, 10000, 'NAP1766486318928', 'da_duyet', '2025-12-23 10:38:38'),
(11, 3, 10000, 'NAP1766486344579', 'da_duyet', '2025-12-23 10:39:04'),
(12, 3, 10000, 'NAP1766487660131', 'da_duyet', '2025-12-23 11:01:00'),
(13, 4, 10000, 'NAP1766489330572', 'da_duyet', '2025-12-23 11:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `id` bigint UNSIGNED NOT NULL,
  `ten_dang_nhap` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `mat_khau` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ten_hien_thi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vai_tro` enum('nguoi_dung','quan_tri') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'nguoi_dung',
  `trang_thai` tinyint(1) NOT NULL DEFAULT '1',
  `tai_khoan_nhan_tien` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `so_du` int NOT NULL DEFAULT '0',
  `diem_de_cu` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`id`, `ten_dang_nhap`, `email`, `mat_khau`, `ten_hien_thi`, `avatar`, `vai_tro`, `trang_thai`, `tai_khoan_nhan_tien`, `ngay_tao`, `ngay_cap_nhat`, `so_du`, `diem_de_cu`) VALUES
(2, 'pa', 'a@a.com', '$2y$10$Xz.P7ZeelqY0gXXquJa5oe/IGfyTNmtxpVk/JHkjN/dr74P1bPStm', 'a', 'uploads/avatar/2.jpg', 'quan_tri', 1, NULL, '2025-12-22 17:51:32', '2025-12-24 11:38:40', 15640, 5000),
(3, 'q', 'gigi@g.com', '$2y$10$qjj5K/A9pWEomjbjrZEl3.FGcO2PpCttvEW3g25PgmEVdE3oCLT6i', 'hihi', NULL, 'nguoi_dung', 1, NULL, '2025-12-23 00:24:14', '2025-12-24 00:49:53', 3017100, 8000),
(4, 'a', 'aa@a.com', '$2y$10$.M/8cnVSVqk686q7FX2Ewe2oEfI4fmOf8emyFenrlyzxUSjypoReC', 'AA', NULL, 'nguoi_dung', 1, NULL, '2025-12-23 18:28:25', '2025-12-24 00:41:19', 7000, 8000),
(5, 'v1qq27@gmail.com', 'v1qq27@gmail.com', '$2y$10$8A9sPPMtSrLTE3YaZJMg..o0Lszl3ysImsHGFo9hn0BHDAjrfNutq', 'pa', NULL, 'nguoi_dung', 1, NULL, '2025-12-24 11:03:10', '2025-12-24 11:03:10', 0, 0),
(6, 'v127@gmail.com', 'v127@gmail.com', '$2y$10$DYTKAqf5TlDZGXglfbmWEerxA5pi4FGZC.QCxSwqM0nuRskNHye9u', 'pa', NULL, 'nguoi_dung', 1, NULL, '2025-12-24 11:03:15', '2025-12-24 12:51:48', 0, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `rut_tien`
--

CREATE TABLE `rut_tien` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `so_tien` int NOT NULL,
  `ngan_hang` varchar(100) DEFAULT NULL,
  `so_tai_khoan` varchar(50) DEFAULT NULL,
  `ten_chu_tk` varchar(100) DEFAULT NULL,
  `trang_thai` enum('cho_duyet','da_duyet','tu_choi') DEFAULT 'cho_duyet',
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rut_tien`
--

INSERT INTO `rut_tien` (`id`, `id_nguoi_dung`, `so_tien`, `ngan_hang`, `so_tai_khoan`, `ten_chu_tk`, `trang_thai`, `ngay_tao`) VALUES
(1, 2, 5000, '12', '12', '12', 'da_duyet', '2025-12-23 00:12:53'),
(2, 3, 50000, 'ABBANK - Ngân hàng TMCP An Bình', 'rrr', 'rrr', 'da_duyet', '2025-12-24 00:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `thanh_toan`
--

CREATE TABLE `thanh_toan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED DEFAULT NULL,
  `ma_giao_dich_ngoai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nha_cung_cap` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `so_tien` decimal(13,2) NOT NULL,
  `trang_thai` enum('khoi_tao','thanh_cong','that_bai','dang_xu_ly') COLLATE utf8mb4_general_ci NOT NULL,
  `du_lieu` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `the_loai`
--

CREATE TABLE `the_loai` (
  `id` bigint UNSIGNED NOT NULL,
  `ten_the_loai` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `the_loai`
--

INSERT INTO `the_loai` (`id`, `ten_the_loai`, `slug`) VALUES
(1, 'Bách Hợp', 'bach-hop'),
(2, 'Đam Mỹ', 'dam-my'),
(3, 'Ngôn Tình', 'ngon-tinh'),
(4, 'Hiện Đại', 'hien-dai'),
(5, 'Hệ Thống', 'he-thong'),
(6, 'BE', 'be'),
(7, 'Cận Đại', 'can-dai'),
(8, 'Cổ Đại', 'co-dai'),
(9, 'Cơ Giáp', 'co-giap'),
(10, 'Cung Đấu', 'cung-dau'),
(11, 'Điền Văn', 'dien-van'),
(12, 'Đồng Nhân', 'dong-nhan'),
(13, 'Đô Thị', 'do-thi'),
(14, 'Gia Đấu', 'gia-dau'),
(15, 'Hài Hước', 'hai-huoc'),
(16, 'Hào Môn Thế Gia', 'hao-mon-the-gia'),
(17, 'HE', 'he'),
(18, 'Huyền Huyễn', 'huyen-huyen'),
(19, 'Kinh Dị', 'kinh-di'),
(20, 'Làm Giàu', 'lam-giau'),
(21, 'Làm Ruộng', 'lam-ruong'),
(22, 'Linh Dị', 'linh-di'),
(23, 'Mạt Thế', 'mat-the'),
(24, 'Mỹ Thực', 'my-thuc'),
(25, 'Nam Chủ', 'nam-chu'),
(26, 'Ngọt Sủng', 'ngot-sung'),
(27, 'Ngược', 'nguoc'),
(28, 'Ngược Tra', 'nguoc-tra'),
(29, 'NP', 'np'),
(30, 'Nữ Chủ', 'nu-chu'),
(31, 'Nữ Phụ', 'nu-phu'),
(32, 'OE', 'oe'),
(33, 'Phản Diện', 'phan-dien'),
(34, 'Phương Tây', 'phuong-tay'),
(35, 'Quân Nhân', 'quan-nhan'),
(36, 'SE', 'se'),
(37, 'Showbiz', 'showbiz'),
(38, 'Sinh Tồn', 'sinh-ton'),
(39, 'Tâm Linh', 'tam-linh'),
(40, 'Thanh Mai Trúc Mã', 'thanh-mai-truc-ma'),
(41, 'Thanh Xuân Vườn Trường', 'thanh-xuan-vuon-truong'),
(42, 'Thập Niên', 'thap-nien'),
(43, 'Trùng Sinh', 'trung-sinh'),
(44, 'Vô Hạn Lưu', 'vo-han-luu'),
(45, 'Xuyên Không', 'xuyen-khong');

-- --------------------------------------------------------

--
-- Table structure for table `truyen`
--

CREATE TABLE `truyen` (
  `id` bigint UNSIGNED NOT NULL,
  `id_tac_gia` bigint UNSIGNED DEFAULT NULL,
  `ten_truyen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tom_tat` text COLLATE utf8mb4_general_ci,
  `trang_thai` enum('dang_ra','hoan_thanh','tam_dung') COLLATE utf8mb4_general_ci DEFAULT 'dang_ra',
  `diem_de_cu` int NOT NULL DEFAULT '0',
  `anh_bia` varchar(512) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slug` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `luot_xem` bigint UNSIGNED NOT NULL DEFAULT '0',
  `gia_truyen` int DEFAULT '0',
  `che_do_chia_luong` enum('doc_quyen','khong_doc_quyen') COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `truyen`
--

INSERT INTO `truyen` (`id`, `id_tac_gia`, `ten_truyen`, `tom_tat`, `trang_thai`, `diem_de_cu`, `anh_bia`, `ngay_tao`, `ngay_cap_nhat`, `slug`, `luot_xem`, `gia_truyen`, `che_do_chia_luong`) VALUES
(3, 2, 'NPC Khốn Khổ Chạy Trốn Trong Trò Chơi Kinh Dị', 'Sau khi bị kéo vào một trò chơi kinh dị mang tên “Trò Chơi Kỷ Nguyên”, Kim Yếm phải đối mặt với một khởi đầu đầy lúng túng:\r\nTin tốt: Nhặt được một cơ thể miễn phí.\r\nTin xấu: Không có trí nhớ kèm theo, mà nguyên chủ thì lại cực kỳ mê thu gom đồng nát, nghèo đến mức tính mạng như chỉ mành treo chuông, lại còn rắc rối quấn thân.\r\nĐây là một trò chơi sinh tử tàn khốc.\r\nKẻ qua ải thì sống.\r\nKim Yếm, nghèo đến mức tâm hồn an nhiên, cảm thấy sống hay không sống cũng chẳng quan trọng lắm, điều quan trọng là… Mấy con quái trong trò chơi dường như yêu cô đến chết đi sống lại, rất khẩn thiết cần tình yêu của cô.\r\nThế là Kim Yếm bắt đầu trở thành một nhân viên chăm sóc xoa dịu.', 'dang_ra', 10000, 'uploads/cover/cover_694a64f536d6a.jpg', '2025-12-22 19:42:23', '2025-12-24 12:51:48', 'npc-khốn-khổ-chạy-trốn-trong-trò-chơi-kinh-dị', 0, 0, 'khong_doc_quyen'),
(4, 2, '1', '1', 'dang_ra', 0, 'uploads/cover/cover_6949717c72512.jpg', '2025-12-22 23:27:40', '2025-12-23 16:22:28', '1', 0, 0, 'khong_doc_quyen'),
(5, 2, 'e', 'e', 'dang_ra', 0, 'uploads/cover/cover_694a606268492.jpg', '2025-12-23 16:26:58', '2025-12-23 16:26:58', 'e', 0, 0, 'khong_doc_quyen');

-- --------------------------------------------------------

--
-- Table structure for table `truyen_the_loai`
--

CREATE TABLE `truyen_the_loai` (
  `id_truyen` bigint UNSIGNED NOT NULL,
  `id_the_loai` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `truyen_the_loai`
--

INSERT INTO `truyen_the_loai` (`id_truyen`, `id_the_loai`) VALUES
(4, 3),
(5, 6),
(4, 7),
(3, 19),
(3, 44);

-- --------------------------------------------------------

--
-- Table structure for table `truyen_yeu_thich`
--

CREATE TABLE `truyen_yeu_thich` (
  `id` bigint UNSIGNED NOT NULL,
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `id_truyen` bigint UNSIGNED NOT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vi_tien`
--

CREATE TABLE `vi_tien` (
  `id_nguoi_dung` bigint UNSIGNED NOT NULL,
  `so_du` decimal(13,2) NOT NULL DEFAULT '0.00',
  `so_tien_giu` decimal(13,2) NOT NULL DEFAULT '0.00',
  `cap_nhat_luc` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nguoi_dung` (`id_nguoi_dung`),
  ADD KEY `id_truyen` (`id_truyen`),
  ADD KEY `id_chuong` (`id_chuong`),
  ADD KEY `id_cha` (`id_cha`);

--
-- Indexes for table `chuong_truyen`
--
ALTER TABLE `chuong_truyen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_truyen_chuong` (`id_truyen`,`so_chuong`),
  ADD UNIQUE KEY `uq_truyen_slug` (`id_truyen`,`slug`);

--
-- Indexes for table `doanh_thu_he_thong`
--
ALTER TABLE `doanh_thu_he_thong`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donate`
--
ALTER TABLE `donate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nguoi_gui` (`id_nguoi_gui`),
  ADD KEY `id_tac_gia` (`id_tac_gia`),
  ADD KEY `id_truyen` (`id_truyen`),
  ADD KEY `id_chuong` (`id_chuong`);

--
-- Indexes for table `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nguoi_dung` (`id_nguoi_dung`);

--
-- Indexes for table `mua_chuong`
--
ALTER TABLE `mua_chuong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_mua` (`id_nguoi_dung`,`id_chuong`),
  ADD UNIQUE KEY `uniq_mua` (`id_nguoi_dung`,`id_chuong`),
  ADD KEY `id_chuong` (`id_chuong`);

--
-- Indexes for table `nap_tien`
--
ALTER TABLE `nap_tien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ma_nap` (`ma_nap`);

--
-- Indexes for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uk_username` (`ten_dang_nhap`),
  ADD UNIQUE KEY `uk_email` (`email`),
  ADD KEY `vai_tro` (`vai_tro`);

--
-- Indexes for table `rut_tien`
--
ALTER TABLE `rut_tien`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rut_nd` (`id_nguoi_dung`);

--
-- Indexes for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `the_loai`
--
ALTER TABLE `the_loai`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `truyen`
--
ALTER TABLE `truyen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `id_tac_gia` (`id_tac_gia`);
ALTER TABLE `truyen` ADD FULLTEXT KEY `ft_truyen_ten_tomtat` (`ten_truyen`,`tom_tat`);

--
-- Indexes for table `truyen_the_loai`
--
ALTER TABLE `truyen_the_loai`
  ADD PRIMARY KEY (`id_truyen`,`id_the_loai`),
  ADD KEY `fk_ttl_the_loai` (`id_the_loai`);

--
-- Indexes for table `truyen_yeu_thich`
--
ALTER TABLE `truyen_yeu_thich`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_fav` (`id_nguoi_dung`,`id_truyen`),
  ADD KEY `id_truyen` (`id_truyen`);

--
-- Indexes for table `vi_tien`
--
ALTER TABLE `vi_tien`
  ADD PRIMARY KEY (`id_nguoi_dung`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `binh_luan`
--
ALTER TABLE `binh_luan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chuong_truyen`
--
ALTER TABLE `chuong_truyen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doanh_thu_he_thong`
--
ALTER TABLE `doanh_thu_he_thong`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `donate`
--
ALTER TABLE `donate`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `giao_dich`
--
ALTER TABLE `giao_dich`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mua_chuong`
--
ALTER TABLE `mua_chuong`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nap_tien`
--
ALTER TABLE `nap_tien`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rut_tien`
--
ALTER TABLE `rut_tien`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `thanh_toan`
--
ALTER TABLE `thanh_toan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `the_loai`
--
ALTER TABLE `the_loai`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `truyen`
--
ALTER TABLE `truyen`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `truyen_yeu_thich`
--
ALTER TABLE `truyen_yeu_thich`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `binh_luan`
--
ALTER TABLE `binh_luan`
  ADD CONSTRAINT `binh_luan_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_ibfk_2` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_ibfk_3` FOREIGN KEY (`id_chuong`) REFERENCES `chuong_truyen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `binh_luan_ibfk_4` FOREIGN KEY (`id_cha`) REFERENCES `binh_luan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chuong_truyen`
--
ALTER TABLE `chuong_truyen`
  ADD CONSTRAINT `chuong_truyen_ibfk_1` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donate`
--
ALTER TABLE `donate`
  ADD CONSTRAINT `donate_ibfk_1` FOREIGN KEY (`id_nguoi_gui`) REFERENCES `nguoi_dung` (`id`),
  ADD CONSTRAINT `donate_ibfk_2` FOREIGN KEY (`id_tac_gia`) REFERENCES `nguoi_dung` (`id`),
  ADD CONSTRAINT `donate_ibfk_3` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`),
  ADD CONSTRAINT `donate_ibfk_4` FOREIGN KEY (`id_chuong`) REFERENCES `chuong_truyen` (`id`);

--
-- Constraints for table `giao_dich`
--
ALTER TABLE `giao_dich`
  ADD CONSTRAINT `giao_dich_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mua_chuong`
--
ALTER TABLE `mua_chuong`
  ADD CONSTRAINT `mua_chuong_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mua_chuong_ibfk_2` FOREIGN KEY (`id_chuong`) REFERENCES `chuong_truyen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rut_tien`
--
ALTER TABLE `rut_tien`
  ADD CONSTRAINT `fk_rut_nd` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`);

--
-- Constraints for table `truyen`
--
ALTER TABLE `truyen`
  ADD CONSTRAINT `fk_truyen_tac_gia` FOREIGN KEY (`id_tac_gia`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `truyen_ibfk_1` FOREIGN KEY (`id_tac_gia`) REFERENCES `nguoi_dung` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `truyen_the_loai`
--
ALTER TABLE `truyen_the_loai`
  ADD CONSTRAINT `fk_ttl_the_loai` FOREIGN KEY (`id_the_loai`) REFERENCES `the_loai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ttl_truyen` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `truyen_the_loai_ibfk_1` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `truyen_the_loai_ibfk_2` FOREIGN KEY (`id_the_loai`) REFERENCES `the_loai` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `truyen_the_loai_ibfk_3` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `truyen_the_loai_ibfk_4` FOREIGN KEY (`id_the_loai`) REFERENCES `the_loai` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `truyen_yeu_thich`
--
ALTER TABLE `truyen_yeu_thich`
  ADD CONSTRAINT `truyen_yeu_thich_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `truyen_yeu_thich_ibfk_2` FOREIGN KEY (`id_truyen`) REFERENCES `truyen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vi_tien`
--
ALTER TABLE `vi_tien`
  ADD CONSTRAINT `vi_tien_ibfk_1` FOREIGN KEY (`id_nguoi_dung`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
