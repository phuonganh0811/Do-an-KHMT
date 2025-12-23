<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh Nhạc Châu</title>
    <style>
        html {
            scroll-behavior: smooth;
        }

        #truyen-moi {
            scroll-margin-top: 89px;
        }

        #truyen-hot {
            scroll-margin-top: 90px;
        }

        #de-cu {
            scroll-margin-top: 90px;
        }

        .user-menu {
            position: relative;
            display: flex;
            align-items: center;
        }

        .user-avatar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #f1f5f9;
            border-radius: 2rem;
            padding: 0.4rem 0.8rem;
            border: 1px solid #f1f1f1;
            cursor: pointer;
            transition: background 0.3s ease;
            color: #F472B6;
        }

        .user-avatar:hover {
            background: #fef2f8;
        }

        .user-avatar svg {
            color: #F472B6;
        }

        .user-avatar img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .lucide lucide-pen mr-1 {
            color: white;
        }

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

        .menu-item.active1 span {
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
            top: 105%;
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

        .user-menu:hover .dropdown1,
        .user-menu .dropdown1:hover {
            display: block;
        }

        .dropdown1 {
            position: absolute;
            /* dính sát dưới menu cha */
            right: 0;
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
            top: 65%;
            margin-top: 16px;
        }

        /* .dropdown1, */
        .dropdown1 a {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .dropdown1 a:hover {
            color: #F472B6;
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
            /* mr-1 */
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

        /* Responsive: Ẩn menu và buttons trên mobile, chỉ hiện hamburger */
        @media (max-width: 768px) {

            .menu,
            .buttons {
                display: none;
            }

            .header {
                padding: 0.5rem;
                /* max-md:px-4 */
            }
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        .dropdown2 {
            color: #F472B6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dropdown3 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #6b7280;
            font-size: 12px;
            padding-top: 5px;
        }

        hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            /* Màu xám rất nhạt (Tailwind slate-200) */
            margin: 1rem 0;
            opacity: 0.6;
            /* Làm nhạt thêm một chút */
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-left">
            <a class="logo" href="TrangChucopy.php">
                <img alt="ZhihuComic-logo-img" loading="lazy" width="180" height="96" decoding="async"
                    src="Ảnh/app-logo-1.png" />
            </a>
            <!-- Menu di chuyển vào đây để gần logo -->
            <ul class="menu">
                <li class="menu-item">
                    <a href="TrangChucopy.php" class="a1">
                        <span class="active1">Trang chủ</span> <!-- Thêm class "active" nếu đang ở trang này -->
                    </a>
                    <ul class="dropdown">
                        <li class="dropdown-item">
                            <a href="#truyen-moi">
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
                            <a href="#truyen-hot">
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
                            <a href="#truyen-hot">
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
                    <a href="timkiem.php" class="a1">
                        <span>Tìm truyện</span>
                    </a>
                    <ul class="dropdown">
                        <li class="dropdown-item">
                            <a href="timkiem.php">
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
                            <a href="timkiem.php">
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
            <?php if (isset($_SESSION["user_id"])): ?>

                <!-- ĐÃ ĐĂNG NHẬP -->
                <div class="user-menu">
                    <div class="user-avatar">
                        <div class="w-10 h-10 bg-slate-50 rounded-full grid place-items-center overflow-hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-circle-user-round text-primary">
                                <path d="M18 20a6 6 0 0 0-12 0"></path>
                                <circle cx="12" cy="10" r="4"></circle>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down text-primary
                    group-hover:rotate-180 transition-transform duration-300">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </div>

                    <!-- Dropdown -->
                    <ul class="dropdown1">
                        <li class="dropdown2">
                            <strong><?= htmlspecialchars($_SESSION["ten_hien_thi"]) ?></strong>
                        </li>
                        <li class="dropdown3">
                            <?= htmlspecialchars($_SESSION["email"] ?? '') ?>
                        </li>

                        <hr>

                        <li class="dropdown-item">
                            <a href="/tai_khoan.php">Thông tin tài khoản</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="dang_truyen_form.php">Đăng truyện</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="quanlytruyen.php">Quản lý truyện</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="/follow.php">Follow</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="/yeu_thich.php">Truyện yêu thích</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="/truyen_da_mua.php">Truyện đã mua</a>
                        </li>

                        <li class="dropdown-item">
                            <a href="nap_diem.php">Nạp</a>
                        </li>

                        <?php if ($_SESSION["vai_tro"] === "quan_tri"): ?>
                            <hr>
                            <li class="dropdown-item">
                                <a href="/dashboard.php">⚙ Quản trị hệ thống</a>
                            </li>
                        <?php endif; ?>

                        <hr>

                        <li class="dropdown-item">
                            <a href="dang_xuat.php" class="logout">Đăng xuất</a>
                        </li>
                    </ul>
                </div>

            <?php else: ?>

                <!-- CHƯA ĐĂNG NHẬP -->
                <div class="buttons">
                    <a href="dang_nhap.php">
                        <button class="btn btn-login">Đăng nhập</button>
                    </a>

                    <a href="dang_ky.php">
                        <button class="btn btn-register">Đăng ký</button>
                    </a>

                    <a href="dang_nhap.php">
                        <button class="btn btn-upload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-pen mr-1">
                                <path d="M13 17l5-5-5-5M6 17l5-5-5-5"></path>
                            </svg>
                            Đăng truyện
                        </button>
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </header>