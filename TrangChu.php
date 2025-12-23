<?php
require 'connect.php';
require 'time.php';
require 'modules/truyen.php';
$sql = "SELECT id, ten_truyen, anh_bia, slug, ngay_cap_nhat FROM truyen ORDER BY id DESC";
$truyens1 = $conn->query($sql);

$truyens = getTruyenMoiCapNhat($conn, 6);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZhihuComic - Trang chủ</title>
    <style>
        /* Import font Segoe UI (nếu cần, hoặc dùng font hệ thống) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .a1 {
            font-weight: 600;
        }

        /* CSS cho header */
        .header {
            position: fixed;
            top: 0;
            width: 100%;
            background: white;
            box-sizing: border-box;
            padding: 6px 24px 6px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            /* shadow-lg */
            z-index: 30;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            /* gap-6 */
        }

        .logo {
            height: 100%;
            padding: 0.5rem 0;
            /* py-2 */
        }

        .logo img {
            height: 100%;
            cursor: pointer;
        }

        /* Banner di chuyển (tích hợp PHP) */
        .banner {
            width: 100%;
            overflow: hidden;
            background: #f0f0f0;
            white-space: nowrap;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .banner-content {
            display: inline-block;
            animation: scroll 15s linear infinite;
            /* Tốc độ cuộn */
        }

        @keyframes scroll {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Menu (di chuyển vào header-left để gần logo) */
        .menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            /* gap-6 */
        }

        .menu-item {
            position: relative;
            color: #333;
            /* text-text-main */
            font-size: 0.875rem;
            /* text-sm */
            font-family: 'Segoe UI', sans-serif;
            /* Font Segoe UI */
            cursor: pointer;
            transition: all 0.3s ease;
            list-style: none;
        }

        .menu-item:hover {
            color: #F472B6;
            /* hover:text-primary */
        }

        .menu-item a {
            text-decoration: none;
            color: inherit;
        }

        .menu-item.active span {
            padding-bottom: 0.5rem;
            /* pb-2 */
            color: #F472B6;
            /* text-primary */
            font-weight: 600;
            /* font-semibold */
            border-bottom: 3px solid #F472B6;
            /* border-b-[3px] border-primary */
        }

        .dropdown {
            position: absolute;
            top: 100%;
            /* dính sát dưới menu cha */
            left: 0;
            background: white;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            padding: 1rem;
            width: max-content;
            display: none;
            transition: all 0.3s ease;
            max-height: 400px;
            overflow-y: auto;
            z-index: 100;
            list-style: none;
        }

        .menu-item:hover .dropdown {
            display: block;
            /* Hiển thị khi hover */
        }

        .dropdown-item {
            padding: 0.75rem;
            /* p-3 */
            background: #f1f5f9;
            /* bg-slate-100 */
            border-radius: 0.375rem;
            /* rounded-md */
            margin-bottom: 0.75rem;
            /* mb-3 */
            min-width: 340px;
            /* min-w-[340px] */
            transition: box-shadow 0.3s ease;
        }

        .dropdown-item:hover {
            background: #fef2f8;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            /* gap-2 */
            color: #F472B6;
            /* text-primary */
            text-decoration: none;
        }

        .dropdown-item span {
            font-weight: 500;
            /* font-medium */
            color: #666;
            /* text-text-secondary */
        }

        /* Buttons bên phải */
        .header-right {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            /* gap-3 */
        }

        .header-right a {
            text-decoration: none;
            color: inherit;
        }

        .buttons {
            display: flex;
            gap: 0.75rem;
            /* gap-3 */
        }

        .btn {
            border-radius: 0.375rem;
            /* rounded-md */
            font-weight: 500;
            /* font-medium */
            font-family: 'Segoe UI', sans-serif;
            /* Font Segoe UI */
            transition: all 0.2s ease-in-out;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            /* shadow-sm */
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            height: 2.5rem;
            /* h-10 */
            font-size: 0.875rem;
            /* text-sm */
            padding: 0 1rem;
            /* px-4 */
            text-decoration: none;
            /* Bỏ underline cho link */
            cursor: pointer;
            border: none;
        }

        .btn:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* hover:shadow-md */
        }

        .btn-login {
            background: #F472B6;
            /* bg-primary */
            color: white;
        }

        .btn-login:hover {
            background: #E11D48;
            /* hover:bg-primary-dark */
        }

        .btn-register {
            background: white;
            color: #F472B6;
            /* text-primary */
            border: 1px solid #F472B6;
            /* border border-primary */
        }

        .btn-upload {
            background: #F87171;
            /* bg-red-400 */
            color: white;
        }

        .btn-upload:hover {
            background: #B91C1C;
            /* hover:bg-red-700 */
        }

        .btn-upload svg {
            margin-right: 0.25rem;
            color: white;
        }

        .hamburger {
            display: flex;
            width: 2.5rem;
            /* size-10 */
            height: 2.5rem;
            background: #f1f5f9;
            /* bg-slate-100 */
            border-radius: 50%;
            /* rounded-full */
            justify-content: center;
            align-items: center;
            border: none;
            cursor: pointer;
        }

        .hamburger svg {
            color: #F472B6;
            /* text-primary */
        }

        /* Responsive: Ẩn menu và buttons trên mobile, chỉ hiện hamburger */
        @media (max-width: 768px) {

            .menu,
            .buttons {
                display: none;
            }

            .header {
                padding: 1rem;
                margin: 2rem;
            }
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-left">
            <a class="logo" href="/">
                <img alt="ZhihuComic-logo-img" loading="lazy" width="180" height="96" decoding="async"
                    src="Ảnh/app-logo-1.png" />
            </a>
            <!-- Menu di chuyển vào đây để gần logo -->
            <ul class="menu">
                <li class="menu-item">
                    <a href="/" class="a1">
                        <span class="active">Trang chủ</span> <!-- Thêm class "active" nếu đang ở trang này -->
                    </a>
                    <ul class="dropdown">
                        <li class="dropdown-item">
                            <a href="#last-completed-comic-section">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-heart">
                                    <path
                                        d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                                    </path>
                                </svg>
                                <span>Truyện mới</span>
                            </a>
                        </li>
                        <li class="dropdown-item">
                            <a href="#section-hot-comics">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-search">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                                <span>Truyện hot</span>
                            </a>
                        </li>
                        <li class="dropdown-item">
                            <a href="#section-top-outstanding">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-circle-check-big">
                                    <path d="M21.801 10A10 10 0 1 1 17 3.335"></path>
                                    <path d="m9 11 3 3L22 4"></path>
                                </svg>
                                <span>Truyện đề cử</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="/comic/search" class="a1">
                        <span>Tìm truyện</span>
                    </a>
                    <ul class="dropdown">
                        <li class="dropdown-item">
                            <a href="/comic/search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-search">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                                <span>Tìm kiếm truyện</span>
                            </a>
                        </li>
                        <li class="dropdown-item">
                            <a href="/comic/search">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-tags">
                                    <path d="m15 5 6.3 6.3a2.4 2.4 0 0 1 0 3.4L17 19"></path>
                                    <path
                                        d="M9.586 5.586A2 2 0 0 0 8.172 5H3a1 1 0 0 0-1 1v5.172a2 2 0 0 0 .586 1.414L8.29 18.29a2.426 2.426 0 0 0 3.42 0l3.58-3.58a2.426 2.426 0 0 0 0-3.42z">
                                    </path>
                                    <circle cx="6.5" cy="9.5" r=".5" fill="currentColor"></circle>
                                </svg>
                                <span>Tìm theo thể loại</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="menu-item">
                    <a href="/notification" class="a1">
                        <span>Khám phá</span>
                    </a>
                    <ul class="dropdown">
                        <li class="dropdown-item">
                            <a href="/notification">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-bell">
                                    <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                                    <path
                                        d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326">
                                    </path>
                                </svg>
                                <span>Thông báo</span>
                            </a>
                        </li>
                        <li class="dropdown-item">
                            <a href="/term-condition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-scroll-text">
                                    <path d="M15 12h-5"></path>
                                    <path d="M15 8h-5"></path>
                                    <path d="M19 17V5a2 2 0 0 0-2-2H4"></path>
                                    <path
                                        d="M8 21h12a2 2 0 0 0 2-2v-1a1 1 0 0 0-1-1H11a1 1 0 0 0-1 1v1a2 2 0 1 1-4 0V5a2 2 0 1 0-4 0v2a1 1 0 0 0 1 1h3">
                                    </path>
                                </svg>
                                <span>Điều khoản sử dụng</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="header-right">
            <div class="buttons">
                <a href="dang_nhap.php">
                    <button class="btn btn-login" aria-label="Đăng nhập">Đăng nhập</button>
                </a>
                <a href="dang_ky.php">
                    <button class="btn btn-register" aria-label="Đăng ký">Đăng ký</button>
                </a>
                <a href="dang_truyen_form.php">
                    <button class="btn btn-upload" aria-label="Đăng truyện">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-pen mr-1">
                            <path d="M13 17l5-5-5-5M6 17l5-5-5-5"></path>
                        </svg>
                        Đăng truyện
                    </button>
                </a>
            </div>
        </div>
    </header>
</body>
<?php include "partials/menu.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .truyen-link {
            text-decoration: none;
            color: black;
        }

        .truyen-link:hover {
            color: gray;
            /* đổi màu khi hover chuột, tùy chọn */
        }


        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .banner {
            position: relative;
            width: 100%;
            height: 550px;
            overflow: hidden;
            margin-top: 80px;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
        }

        .slide.active {
            opacity: 1;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 1rem;
        }

        .overlay h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .overlay p {
            font-style: italic;
            font-size: 1.1rem;
            max-width: 700px;
            margin-bottom: 1.5rem;
        }

        .overlay button {
            background-color: #ff4f8b;
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
        }

        .overlay button:hover {
            background-color: #ff6fa3;
        }

        /* Nút điều hướng */
        .nav-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.3);
            border: none;
            padding: 10px 15px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background 0.3s;
        }

        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        /* Chấm tròn dưới banner */
        .dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 12px;
            height: 12px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
        }

        .dot.active {
            background-color: white;
            width: 16px;
            height: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .TruyenMoiCapNhat {
            width: 100%;
            overflow: hidden;
            white-space: nowrap;
        }

        .TruyenMoiCapNhat-content {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            /* Chiều rộng giới hạn để thụt lề */
            width: 100%;
            margin: 30px auto;
            /* Căn giữa màn hình */
            padding-left: 30px;
            padding-right: 30px;
            box-sizing: border-box;
            /* Rất quan trọng */
        }


        .TruyenDocNhieuNhat {
            width: 100%;
            overflow: hidden;
            background: #f1f5f9;
            white-space: nowrap;
        }

        .TruyenDocNhieuNhat-content {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            /* Chiều rộng giới hạn để thụt lề */
            width: 100%;
            margin: 30px auto;
            /* Căn giữa màn hình */
            padding-left: 30px;
            padding-right: 30px;
            box-sizing: border-box;
            /* Rất quan trọng */
        }

        .gioithieu {
            display: flex;
            justify-content: space-between;
            /* text trái, button phải */
            align-items: center;
            width: 100%;
            margin-bottom: 20px;
        }

        .text {
            display: flex;
            flex-direction: column;
            /* text1 trên text2 */
        }

        .text1 {
            color: #f472b6;
            font-size: 24px;
            font-weight: 700;
        }

        .text2 {
            color: #6b7280;
            font-size: 14px;
            margin-top: 4px;
        }

        .XemTatCa {
            display: flex;
            align-items: center;
        }

        .XemTatCa a {
            text-decoration: none;
            color: inherit;
        }

        .lucide-move-right {
            margin-right: 0.25rem;
            color: #F472B6;
        }

        .all {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 1px solid #F472B6;
            background: white;
            color: #F472B6;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            font-family: 'Segoe UI', sans-serif;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .all:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            /* hover:shadow-md */
        }

        .buttons {
            display: inline-flex;
            /* hoặc flex, nhưng inline-flex tốt hơn */
            gap: 0.75rem;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        h2 {
            color: #ec4899;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .grid-truyen {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            /* gap: 20px; */
            justify-items: center;
            /* Tránh tràn lề phải */
            box-sizing: border-box;
        }

        .truyen-card {
            background: white;
            width: 130px;
            /* hoặc 120/140 tuỳ bạn muốn vừa 8 cột */
            display: flex;
            flex-direction: column;
            height: 230px;
            /* tổng chiều cao card — giảm nếu vẫn thấy dài */
            overflow: hidden;
            /* cắt phần thừa nếu có */
            box-sizing: border-box;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .truyen-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .truyen-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            /* border-top-left-radius: 12px;
            border-top-right-radius: 12px; */
        }



        .truyen-info {
            padding: 5px 10px;
        }

        .truyen-info h3 {
            font-size: 14px;
            font-weight: 600;
            margin: 6px 0 4px;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .truyen-stats {
            font-size: 12px;
            color: #6b7280;
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
            font-weight: 600;
        }

        .st1 {
            display: flex;
            align-items: center;
            margin-right: 0.5rem;
        }

        .lucide-book-open-check {
            margin-right: 0.25rem;
        }

        .lucide-eye {
            margin-right: 0.25rem;
        }

        /* === Tag góc === */
        .tag {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #ec4899;
            color: white;
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
        }

        .truyen-card .wrapper {
            position: relative;
        }

        @media (max-width: 1400px) {
            .grid-truyen {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        @media (max-width: 1200px) {
            .grid-truyen {
                grid-template-columns: repeat(6, 1fr);
            }
        }

        @media (max-width: 992px) {
            .grid-truyen {
                grid-template-columns: repeat(5, 1fr);
            }
        }

        @media (max-width: 768px) {
            .grid-truyen {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 576px) {
            .grid-truyen {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .wrapper1 {
            position: relative;
        }

        .time-tag {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #f472b6;
            /* hồng nhạt */
            color: white;
            font-weight: 600;
            font-size: 12px;
            border-radius: 9999px;
            padding: 2px 10px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .time-tag::before {
            content: "⏱";
            /* icon đồng hồ */
            font-size: 12px;
        }

        footer {
            background-color: #f1f5f9;
            padding: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .footer-container {
            max-width: 1170px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3rem;
            /* color: #6b7280; */
        }

        .footer-logo img {
            width: 180px;
            height: 60px;
            object-fit: contain;
            border-radius: 8px;
        }

        .footer-logo ul {
            list-style: none;
            margin-top: 15px;
            padding: 0;
        }

        .footer-logo a,
        .footer-logo p {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            transition: color 0.2s;
            color: #6b7280;
            font-size: 14px;
        }

        .footer-logo a:hover,
        .footer-logo p:hover,
        .footer-column a:hover {
            color: #ec4899;
            /* hồng nhạt */
        }

        .footer-column h3 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-column li {
            margin-bottom: 10px;
            font-size: 14px;
        }

        .footer-column a {
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
            color: #6b7280
        }

        .footer-bottom {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            padding-top: 20px;
            color: #475569;
            font-size: 0.95rem;
        }

        .footer-bottom span {
            color: #ec4899;
            font-weight: 600;
        }

        svg {
            flex-shrink: 0;
            color: #475569;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .footer-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .footer-container {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-logo a,
            .footer-logo p,
            .footer-column a {
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="banner">
        <button class="nav-btn prev">◀</button>
        <button class="nav-btn next">▶</button>
        <div class="dots"></div>
    </div>

    <script>
        const banners = [
            {
                title: "Khi Tiểu Thư Thật Giả Cuồng Kiểm Soát Tranh...",
                desc: "Ngay ngày đầu tiên trở về nhà họ Tưởng, tôi đã biết kẻ thù của mình không phải là tiểu kim giả kia mà là người chị...",
                button: "Đọc truyện ngay",
                image: "Ảnh/hinh-mau-hong-25.jpg"
            },
            {
                title: "Nữ Chính Hồi Sinh Với Hệ Thống Phản Công",
                desc: "Cô ấy từng là kẻ yếu đuối, nhưng lần này, cô trở lại với sức mạnh và trí tuệ không ai sánh bằng.",
                button: "Xem ngay",
                image: "Ảnh/hinh-mau-hong-25.jpg"
            },
            {
                title: "Công Tước Hắc Ám Và Cô Gái Ánh Trăng",
                desc: "Một mối tình cấm kỵ giữa ánh sáng và bóng tối, nơi định mệnh không thể chối bỏ.",
                button: "Đọc ngay",
                image: "Ảnh/hinh-nen-mau-hong-cute-cho-may-tinh-13.png.webp"
            }
        ];

        const bannerContainer = document.querySelector(".banner");
        const dotsContainer = document.querySelector(".dots");

        // Tạo slide
        banners.forEach((b, i) => {
            const slide = document.createElement("div");
            slide.className = "slide";
            if (i === 0) slide.classList.add("active");
            slide.style.backgroundImage = `url(${b.image})`;
            slide.innerHTML = `
            <div class="overlay">
              <h1>${b.title}</h1>
              <p>${b.desc}</p>
              <button>${b.button}</button>
            </div>
          `;
            bannerContainer.appendChild(slide);

            const dot = document.createElement("div");
            dot.className = "dot";
            if (i === 0) dot.classList.add("active");
            dot.addEventListener("click", () => showSlide(i));
            dotsContainer.appendChild(dot);
        });

        const slides = document.querySelectorAll(".slide");
        const dots = document.querySelectorAll(".dot");
        let current = 0;

        function showSlide(i) {
            slides[current].classList.remove("active");
            dots[current].classList.remove("active");
            current = i;
            slides[current].classList.add("active");
            dots[current].classList.add("active");
        }

        document.querySelector(".prev").onclick = () => {
            showSlide((current - 1 + slides.length) % slides.length);
        };

        document.querySelector(".next").onclick = () => {
            showSlide((current + 1) % slides.length);
        };

        // Tự động chuyển slide sau 5s
        setInterval(() => {
            showSlide((current + 1) % slides.length);
        }, 5000);
    </script>

    <div class="TruyenMoiCapNhat">
        <div class="TruyenMoiCapNhat-content">
            <div class="gioithieu">
                <!-- Text trái -->
                <div class="text">
                    <div class="text1">Truyện mới cập nhật</div>
                    <div class="text2">Khám phá truyện mới nhất cùng chúng tôi!</div>
                </div>

                <!-- Button phải -->
                <div class="XemTatCa">
                    <a href="/comic/search">
                        <button class="all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-move-right">
                                <path d="M18 8L22 12L18 16"></path>
                                <path d="M2 12H22"></path>
                            </svg>
                            Xem tất cả
                        </button>
                    </a>
                </div>
            </div>

            <div class="grid-truyen">

                <?php while ($row = $truyens->fetch_assoc()): ?>
                    <div class="truyen-card">
                        <div class="wrapper1">
                            <!-- Link ảnh -->
                            <a href="truyen.php?slug=<?= urlencode($row['slug']); ?>">
                                <img src="<?= $row['anh_bia']; ?>" alt="<?= htmlspecialchars($row['ten_truyen']); ?>">
                            </a>
                            <div class="time-tag"><?= timeAgo($row['ngay_cap_nhat']); ?></div>
                        </div>

                        <div class="truyen-info">
                            <!-- Link tiêu đề truyện -->
                            <h3>
                                <a href="truyen.php?slug=<?= urlencode($row['slug']); ?>" class="truyen-link">
                                    <?= htmlspecialchars($row['ten_truyen']); ?>
                                </a>
                            </h3>

                            <div class="truyen-stats">

                                <div class="st1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-book-open-check">
                                        <path d="M12 21V7"></path>
                                        <path d="m16 12 2 2 4-4"></path>
                                        <path
                                            d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                        </path>
                                    </svg>
                                    <span>??</span> <!-- số chương -->
                                </div>

                                <div class="st1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="lucide lucide-eye">
                                        <path
                                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                        </path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span>0</span> <!-- lượt xem -->
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>

        </div>
    </div>

    <div class="TruyenDocNhieuNhat">
        <div class="TruyenDocNhieuNhat-content">
            <div class="gioithieu">
                <!-- Text trái -->
                <div class="text">
                    <div class="text1">Top truyện hấp dẫn nhất</div>
                    <div class="text2">Truyện đang được đọc nhiều nhất!</div>
                </div>

                <!-- Button phải -->
                <div class="XemTatCa">
                    <a href="/comic/search">
                        <button class="all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-move-right">
                                <path d="M18 8L22 12L18 16"></path>
                                <path d="M2 12H22"></path>
                            </svg>
                            Xem tất cả
                        </button>
                    </a>
                </div>
            </div>

            <div class="grid-truyen">
                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 1</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 1</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 3</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 4</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 5</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 6</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 7</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 8</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 9</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 10</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 11</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 12</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 13</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 14</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 15</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper"><img
                            src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="tag">Top 16</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="TruyenMoiCapNhat">
        <div class="TruyenMoiCapNhat-content">
            <div class="gioithieu">
                <!-- Text trái -->
                <div class="text">
                    <div class="text1">Truyện được đề cử</div>
                    <div class="text2">Danh sách truyện được đề cử!</div>
                </div>

                <!-- Button phải -->
                <div class="XemTatCa">
                    <a href="/comic/search">
                        <button class="all">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-move-right">
                                <path d="M18 8L22 12L18 16"></path>
                                <path d="M2 12H22"></path>
                            </svg>
                            Xem tất cả
                        </button>
                    </a>
                </div>
            </div>

            <div class="grid-truyen">
                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="truyen-card">
                    <div class="wrapper1">
                        <img src="https://i.pinimg.com/736x/5c/bf/32/5cbf32d4f9e7b7e3221b4b7c67e0662f.jpg" alt="">
                        <div class="time-tag">Vừa xong</div>
                    </div>
                    <div class="truyen-info">
                        <h3>Hôn Ước Ngọt Ngào</h3>
                        <div class="truyen-stats">
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-book-open-check">
                                    <path d="M12 21V7"></path>
                                    <path d="m16 12 2 2 4-4"></path>
                                    <path
                                        d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                    </path>
                                </svg>
                                <span>24</span>
                            </div>
                            <div class="st1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-eye">
                                    <path
                                        d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                    </path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span>15560</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <!-- Cột 1 -->
            <div class="footer-logo">
                <a href="#"><img src="Ảnh/app-logo-1.png" alt="Thanh Nhạc Châu Logo"></a>
                <ul>
                    <a href="https://facebook.com" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z" />
                        </svg>
                        Thanh Nhạc Châu
                    </a>
                    <p>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                        thanhnhacchau@gmail.com
                    </p>
                </ul>
            </div>

            <!-- Cột 2 -->
            <div class="footer-column">
                <h3>Về chúng tôi</h3>
                <ul>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-info">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M12 16v-4"></path>
                                <path d="M12 8h.01"></path>
                            </svg> Giới thiệu</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-phone">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                </path>
                            </svg> Liên hệ</a></li>
                    <li><a href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-save">
                                <path
                                    d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z">
                                </path>
                                <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"></path>
                                <path d="M7 3v4a1 1 0 0 0 1 1h7"></path>
                            </svg> Điều khoản sử dụng
                        </a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-bell">
                                <path d="M10.268 21a2 2 0 0 0 3.464 0"></path>
                                <path
                                    d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326">
                                </path>
                            </svg> Tin tức - Thông báo</a></li>
                </ul>
            </div>

            <!-- Cột 3 -->
            <div class="footer-column">
                <h3>Khám phá</h3>
                <ul>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-search">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg> Tìm truyện</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-book-open-check">
                                <path d="M12 21V7"></path>
                                <path d="m16 12 2 2 4-4"></path>
                                <path
                                    d="M22 6V4a1 1 0 0 0-1-1h-5a4 4 0 0 0-4 4 4 4 0 0 0-4-4H3a1 1 0 0 0-1 1v13a1 1 0 0 0 1 1h6a3 3 0 0 1 3 3 3 3 0 0 1 3-3h6a1 1 0 0 0 1-1v-1.3">
                                </path>
                            </svg> Truyện mới</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-flame">
                                <path
                                    d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z">
                                </path>
                            </svg> Truyện hot</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-star">
                                <path
                                    d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                </path>
                            </svg> Truyện đề cử</a></li>
                </ul>
            </div>

            <!-- Cột 4 -->
            <div class="footer-column">
                <h3>Hỗ trợ</h3>
                <ul>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-circle-help">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <path d="M12 17h.01"></path>
                            </svg> Hướng dẫn sử dụng</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-facebook">
                                <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                            </svg> Thanh Nhạc Châu</a></li>
                    <li><a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-mail">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg> thanhnhacchau@gmail.com</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            © 2025 <span>Thanh Nhạc Châu</span>. All rights reserved.
        </div>
    </footer>
</body>

</html>