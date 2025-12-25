<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .user-menu {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 30px;
        }
        .user-menu a{
            color: #888;
            font-weight: 500;
        }

        .avatar-svg {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #F472B6;
        }

        .account-link {
            display: flex !important;
            align-items: center;
            /* căn icon & chữ cùng hàng */
            gap: 10px;
            /* khoảng cách icon – chữ */
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            color: #333;
            background: #f6f7fb;
            text-decoration: none !important;
            transition: 0.2s;
        }

        .account-link svg {
            color: #ff6fae;
            flex-shrink: 0;
            /* không bị co */
        }

        .account-link:hover {
            background: #ffe1ee;
            color: #ff6fae;
        }

        .btn-primary1 {
            background: #ff6fae;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-outline1 {
            background: white;
            border: 1.5px solid #ff6fae;
            color: #ff6fae;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        .user-sidebar {
            flex-shrink: 0;
            left: 30px;
            top: 120px;
            width: 300px;
        }

        .user-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        .user-info {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }

        .user-actions {
            display: flex;
            gap: 10px;
            margin: 12px 0;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            text-align: center;
        }

        .btn.pink {
            background: #ff6fae;
            color: white;
        }

        .btn.full {
            display: block;
            background: #ff6fae;
            color: white;
            margin-top: 12px;
        }

        .user-stats .stat {
            background: #f6f7fb;
            padding: 8px;
            border-radius: 10px;
            margin-bottom: 6px;
        }

        .stat {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f6f7fb;
            padding: 10px;
            border-radius: 10px;
        }

        .stat .icon {
            width: 24px;
            height: 24px;
        }

        .stat-text .label {
            color: #8b8b8b;
            /* 🌸 hồng */
            font-weight: 650;
            /* 👈 in đậm */
        }

        .stat-text {
            font-size: 14px;
            line-height: 1.4;
        }

        .stat-text b {
            font-size: 15px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .user-text h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.2;
        }

        .user-text p {
            margin: 2px 0 0;
            /* sát hơn với tên */
            font-size: 12px;
            color: #8b8b8b;
            /* màu nhạt */
        }

        hr {
            border: none;
            border-top: 1px solid #e5e7eb;
            /* Màu xám rất nhạt (Tailwind slate-200) */
            margin: 1rem 0;
            opacity: 0.6;
            /* Làm nhạt thêm một chút */
        }

        .user-stats h4 {
            font-size: 14px;
        }

        .btn.full {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require 'connect.php';

    $user_id = $_SESSION['user_id'] ?? 0;

    $user = null;
    if ($user_id) {
        $stmt = $conn->prepare("
    SELECT ten_hien_thi, email, so_du, diem_de_cu, avatar
    FROM nguoi_dung
    WHERE id = ?
");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();


        $avatar = !empty($user['avatar'])
            ? $user['avatar']
            : '/assets/avatar-default.png';
    }
    ?>

    <?php if ($user): ?>
        <div class="user-sidebar">
            <div class="user-card">
                <div class="user-info">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= htmlspecialchars($user['avatar']) ?>" class="avatar">
                    <?php else: ?>
                        <div class="avatar avatar-svg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-circle-user-round text-primary">
                                <path d="M18 20a6 6 0 0 0-12 0"></path>
                                <circle cx="12" cy="10" r="4"></circle>
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                        </div>
                    <?php endif; ?>

                    <div class="user-text">
                        <h4><?= htmlspecialchars($user['ten_hien_thi']) ?></h4>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>



                <div class="user-actions">
                    <a href="thongtin.php">
                        <button class="btn-outline1">Cập nhật</button> </a>
                    <a href="dang_truyen_form.php">
                        <button class="btn-primary1">Đăng truyện</button> </a>
                </div>

                <hr>

                <div class="user-stats">
                    <h4>Thông số của bạn</h4>
                    <div class="stat">
                        <img src="Ảnh/award-01.png" width="20" class="icon">
                        <div class="stat-text"> <span class="label">Dâu</span> <img src="Ảnh/emoji_u1f353.png" width="14">
                            <br><b><?= number_format($user['so_du']) ?> điểm </b>
                        </div>
                    </div>
                    <div class="stat">
                        <img src="Ảnh/award-01.png" width="20" class="icon">
                        <div class="stat-text"> <span class="label">Hoa</span> 🌸
                            <br><b><?= number_format($user['diem_de_cu']) ?> điểm </b>
                        </div>
                    </div>
                </div>

                <a href="nap_diem.php" class="btn full"> Nạp điểm ngay</a>
            </div>

            <div class="user-card user-menu">
                <a href="thongtin.php" class="account-link"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-user-round-icon lucide-user-round">
                        <circle cx="12" cy="8" r="5" />
                        <path d="M20 21a8 8 0 0 0-16 0" />
                    </svg> Thông tin tài khoản</a>
                <a href="dang_truyen_form.php" class="account-link"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-square-pen-icon lucide-square-pen">
                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path
                            d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z" />
                    </svg> Đăng truyện</a>
                <a href="quanlytruyen.php" class="account-link"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-book-check-icon lucide-book-check">
                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H19a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H6.5a1 1 0 0 1 0-5H20" />
                        <path d="m9 9.5 2 2 4-4" />
                    </svg> Quản lý truyện</a>
                <a href="nap_diem.php" class="account-link"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-badge-dollar-sign-icon lucide-badge-dollar-sign">
                        <path
                            d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                        <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8" />
                        <path d="M12 18V6" />
                    </svg> Gói nạp</a>
                <a href="rut_tien.php" class="account-link"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-bitcoin-icon lucide-bitcoin">
                        <path
                            d="M11.767 19.089c4.924.868 6.14-6.025 1.216-6.894m-1.216 6.894L5.86 18.047m5.908 1.042-.347 1.97m1.563-8.864c4.924.869 6.14-6.025 1.215-6.893m-1.215 6.893-3.94-.694m5.155-6.2L8.29 4.26m5.908 1.042.348-1.97M7.48 20.364l3.126-17.727" />
                    </svg> Rút</a>
            </div>

        </div>
    <?php endif; ?>



</body>

</html>