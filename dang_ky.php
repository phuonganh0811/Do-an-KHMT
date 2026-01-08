<?php
require 'connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); //Bật chế độ báo lỗi  cho MySQLi

$errors = []; //Khởi tạo mảng chứa lỗi, hiển thị tất cả lỗi
$showErrorPopup = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //xử lý khi form được submit bằng POST

    $ten_hien_thi = trim($_POST['ten_hien_thi'] ?? ''); //trim() xóa khoảng trắng đầu & cuối, ten_hien_thi lấy từ form đăng ký, ?? kiểm tra null
    $email = trim($_POST['email'] ?? '');
    $mat_khau = $_POST['mat_khau'] ?? '';
    $mat_khau_2 = $_POST['mat_khau_2'] ?? '';

    /* ===== VALIDATE ===== */
    if ($ten_hien_thi === '') {
        $errors['ten_hien_thi'] = "Vui lòng nhập tên đầy đủ!";
    }

    if ($email === '') {
        $errors['email'] = "Vui lòng nhập email!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email không hợp lệ!";
    }

    if (strlen($mat_khau) < 6) {
        $errors['mat_khau'] = "Mật khẩu phải từ 6 ký tự!";
    }

    if ($mat_khau_2 !== $mat_khau) {
        $errors['mat_khau_2'] = "Mật khẩu nhập lại không khớp!";
    }

    /* ===== CHECK EMAIL TỒN TẠI ===== */
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
        $check->bind_param("s", $email); // gán giá trị cho ?
        $check->execute(); //chạy câu SQL
        $check->store_result(); //Lưu kết quả query vào bộ nhớ

        if ($check->num_rows > 0) { //kiểm tra mail tồn tại ko, có -> lỗi
            $errors[] = 'exist';
        }
        $check->close();
    }

    /* ===== CHỈ HIỆN POPUP KHI EMAIL TRÙNG ===== */
    if (in_array('exist', $errors, true)) { //Ktr lỗi exist trong $errors, có hiện popup
        $showErrorPopup = true;
    }


    /* ===== INSERT ===== */
    if (empty($errors)) {
        $hash = password_hash($mat_khau, PASSWORD_BCRYPT); //Mã hóa mật khẩu trước khi lưu vào database

        $stmt = $conn->prepare("
            INSERT INTO nguoi_dung (ten_dang_nhap, email, mat_khau, ten_hien_thi)
            VALUES (?, ?, ?, ?)
        "); //Chuẩn bị SQL
        $stmt->bind_param("ssss", $email, $email, $hash, $ten_hien_thi); // gán giá trị cho ?
        $stmt->execute(); //thực thi lệnh
        $stmt->close(); //Đóng statement

        header("Location: dang_nhap.php?registered=1");
        exit;
    }
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

        .popup-error {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #f44336;
            color: #fff;
            padding: 18px 26px;
            border-radius: 14px;
            min-width: 320px;
            z-index: 9999;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
            animation: slideDown .4s ease;
        }

        .popup-title {
            font-weight: 700;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .popup-text {
            font-size: 14px;
            opacity: .95;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }

            to {
                opacity: 1;
                transform: translate(-50%, 0);
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

            <h2>Đăng ký tài khoản</h2>
            <p class="desc">
                Hãy đăng ký tài khoản của bạn để trải nghiệm tính năng và đăng truyện nào!
            </p>

            <form method="post">
                <div class="form-group">
                    <label>Tên đầy đủ <span>*</span></label>
                    <input type="text" name="ten_hien_thi" value="<?= htmlspecialchars($ten_hien_thi ?? '') ?>">
                    <!-- Ktr lỗi ten_hien_thi trong $errors, có hiện lỗi -->
                    <?php if (!empty($errors['ten_hien_thi'])): ?>
                        <div class="error-text"><?= $errors['ten_hien_thi'] ?></div>
                    <?php endif; ?>
                </div>

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
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off">
                                <path
                                    d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49">
                                </path>
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                                <path
                                    d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143">
                                </path>
                                <path d="m2 2 20 20"></path>
                            </svg></span>
                    </div>
                    <?php if (!empty($errors['mat_khau'])): ?>
                        <div class="error-text"><?= $errors['mat_khau'] ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label>Nhập lại mật khẩu <span>*</span></label>
                    <div class="password-box">
                        <input type="password" name="mat_khau_2" class="password-input">
                        <span class="toggle-pass"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off">
                                <path
                                    d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49">
                                </path>
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"></path>
                                <path
                                    d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143">
                                </path>
                                <path d="m2 2 20 20"></path>
                            </svg></span>
                    </div>
                    <?php if (!empty($errors['mat_khau_2'])): ?>
                        <div class="error-text"><?= $errors['mat_khau_2'] ?></div>
                    <?php endif; ?>
                </div>


                <div class="login-link">
                    Bạn đã có tài khoản?
                    <a href="dang_nhap.php">Đăng nhập ngay</a>
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
        document.querySelectorAll('.toggle-pass').forEach(btn => { //lấy tất cả phần tử có class toggle-pass, (cái mắt) forEach(btn :lặp từng nút
            btn.addEventListener('click', function () { //bấm mắt sẽ chạy
                const input = this.closest('.password-box').querySelector('input'); //tìm password-box gần nhất, tìm <input> bên trong box đó đảm bảo mắt nào điều khiển input đó

                if (input.type === 'password') { //Đang pass, ấn sẽ hiện, mắt mở
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

    <?php if ($showErrorPopup): ?>
        <div class="popup-error" id="popupError">
            <div class="popup-title">Đăng ký không thành công!</div>
            <div class="popup-text">
                Email đã tồn tại!
            </div>
        </div>
    <?php endif; ?>
    <script>
        setTimeout(() => {
            const popup = document.getElementById('popupError');
            if (popup) {
                popup.style.opacity = '0';
                popup.style.transition = 'opacity .4s';
                setTimeout(() => popup.remove(), 400);
            }
        }, 3000);
    </script>

</body>

</html>