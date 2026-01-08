<?php
session_start();
require 'connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['email']) || empty($_POST['mat_khau'])) {
        die("❌ Thiếu thông tin đăng nhập"); //die in tb, dừng php
    }

    $username = trim($_POST['email']);
    $password = $_POST['mat_khau'];

    $stmt = $conn->prepare("
        SELECT id, ten_dang_nhap, email, mat_khau, ten_hien_thi, vai_tro, trang_thai
        FROM nguoi_dung
        WHERE ten_dang_nhap = ? OR email = ?
        LIMIT 1
    ");

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute(); //chạy sql
    $user = $stmt->get_result()->fetch_assoc(); //Lấy kết quả SQL → fetch_assoc() lấy 1 dòng → gán vào $user

    if (!$user) {
        die("❌ Tài khoản không tồn tại");
    }

    if (!password_verify($password, $user['mat_khau'])) {
        die("❌ Sai mật khẩu");
    }

    /* Lưu session */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
    $_SESSION['ten_hien_thi'] = $user['ten_hien_thi'];
    $_SESSION['vai_tro'] = $user['vai_tro'];
    $_SESSION["email"] = $user["email"];

    header("Location: trangchucopy.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-text {
            color: #e53935;
            font-size: 13px;
            margin-top: 6px;
        }

        body {
            margin: 0;
            background: #fff;
        }

        .auth-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ===== LEFT ===== */
        .auth-left {
            width: 55%;
            padding: 60px;
            position: relative;

            display: flex;
            flex-direction: column;
            justify-content: center;
            /* 👈 căn giữa theo chiều dọc */
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 40px;
            height: 40px;
            background: #ff69b4;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            /* căn dọc */
            justify-content: center;
            /* căn ngang */
            font-size: 18px;
            cursor: pointer;
        }


        h2 {
            color: #ff69b4;
            margin-bottom: 10px;
        }

        .desc {
            color: #777;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .form-group label span {
            color: red;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
        }

        .password-box {
            position: relative;
        }

        .password-box input {
            width: 100%;
            padding: 10px 40px 10px 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 16px;
            color: #888;
            user-select: none;
        }

        .toggle-pass:hover {
            color: #ff6aa2;
        }

        .toggle-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #ff69b4;
        }

        .login-link {
            text-align: center;
            margin: 25px 0;
        }

        .login-link a {
            color: #ff69b4;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-submit {
            width: 90%;
            margin: 20px auto 0;
            display: flex;
            /* 👈 quan trọng */
            align-items: center;
            /* căn giữa theo chiều dọc */
            justify-content: center;
            /* căn giữa toàn bộ */
            gap: 8px;
            /* khoảng cách icon – chữ */
            background: #ff69b4;
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        /* ===== RIGHT ===== */
        .auth-right {
            width: 45%;
            background: url('Ảnh/783739d636d0d4ad5c22adc099ff3b02.jpg') center/cover no-repeat;
            position: relative;
            color: #fff;
        }

        .auth-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand {
            color: #ff69b4;
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .slogan {
            font-size: 32px;
            line-height: 1.3;
            font-weight: bold;
        }

        .sub {
            margin-top: 20px;
            opacity: .9;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            left: 60px;
            font-size: 14px;
            opacity: .8;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .auth-wrapper {
                flex-direction: column;
            }

            .auth-left,
            .auth-right {
                width: 100%;
            }

            .auth-right {
                min-height: 300px;
            }
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <!-- LEFT -->
        <div class="auth-left">
            <a href="TrangChucopy.php">
                <div class="back-btn"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-undo2">
                        <path d="M9 14 4 9l5-5"></path>
                        <path d="M4 9h10.5a5.5 5.5 0 0 1 5.5 5.5a5.5 5.5 0 0 1-5.5 5.5H11"></path>
                    </svg></div>
            </a>

            <h2>Đăng nhập tài khoản</h2>
            <p class="desc">
                Hãy đăng nhập hệ thống để trải nghiệm tính năng và đăng truyện nào!
            </p>

            <form method="post">

                <div class="form-group">
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
                    <?php if (!empty($errors['email'])): ?>
                        <div class="error-text"><?= $errors['email'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Mật khẩu <span>*</span></label>
                    <div class="password-box">
                        <input type="password" name="mat_khau" class="password-input">
                        <span class="toggle-pass"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                                <path
                                    d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                </path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg></span>
                    </div>
                    <?php if (!empty($errors['mat_khau'])): ?>
                        <div class="error-text"><?= $errors['mat_khau'] ?></div>
                    <?php endif; ?>
                </div>



                <div class="login-link">
                    Bạn chưa có tài khoản?
                    <a href="dang_ky.php">Đăng ký ngay</a>
                </div>

                <button class="btn-submit"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-log-in">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" x2="3" y1="12" y2="12"></line>
                    </svg> Đăng ký</button>
            </form>
        </div>

        <!-- RIGHT -->
        <div class="auth-right">
            <div class="auth-overlay">
                <div class="brand">Thanh Nhạc Châu</div>
                <div class="slogan">
                    Thanh Nhạc Châu – Nền tảng<br>
                    đọc truyện & nghe truyện chất lượng
                </div>
                <div class="sub">
                    Thanh Nhạc Châu - Hệ sinh thái truyện tranh hàng đầu cho các độc giả!
                </div>
            </div>

            <div class="footer">
                © 2025 Thanh Nhạc Châu. All rights reserved.
            </div>
        </div>

    </div>
    <script>
        document.querySelectorAll('.toggle-pass').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.closest('.password-box').querySelector('input');

                if (input.type === 'password') {
                    input.type = 'text';
                    this.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye">
                                <path
                                    d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                                </path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
            `;
                } else {
                    input.type = 'password';
                    this.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off">
    <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"></path>
    <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
    <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"></path>
    <path d="m2 2 20 20"></path>
</svg>
            `;
                }
            });
        });
    </script>


</body>

</html>