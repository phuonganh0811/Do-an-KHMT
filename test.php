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

        .account-link {
            display: block !important;
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            color: #333;
            background: #f6f7fb;
            text-decoration: none !important;
            transition: 0.2s;
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
        SELECT ten_hien_thi, email, so_du, diem_de_cu
        FROM nguoi_dung
        WHERE id = ?
    ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    }
    ?>

    <?php if ($user): ?>
        <div class="user-sidebar">
            <div class="user-card">
                <div class="user-info">
                    <img src="/assets/avatar-default.png" class="avatar">
                    <div class="user-text">
                        <h4><?= htmlspecialchars($user['ten_hien_thi']) ?></h4>
                        <p><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>

                <div class="user-actions">
                    <a href="/">
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
                        <div class="stat-text"> <span class="label">Dâu</span> 🌸
                            <br><b><?= number_format($user['diem_de_cu']) ?> điểm </b>
                        </div>
                    </div>
                </div>

                <a href="nap_diem.php" class="btn full"> Nạp điểm ngay</a>
            </div>

            <div class="user-card user-menu">
                <a href="thong_tin_tai_khoan.php" class="account-link">👤 Thông tin tài khoản</a>
                <a href="dang_truyen_form.php" class="account-link">✍️ Đăng truyện</a>
                <a href="truyen_da_mua.php" class="account-link">📚 Truyện đã mua</a>
                <a href="nap_diem.php" class="account-link">💰 Nạp</a>
            </div>

        </div>
    <?php endif; ?>



</body>

</html>