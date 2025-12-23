<?php include "menu.php"; ?>
<?php
require 'auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Rút tiền</title>
    <style>
        :root {
            --pink: #ff6fae;
            --pink-dark: #ff4f9a;
            --pink-light: #ffe6f1;
            --bg: #f5f6fa;
            --border: #e5e7eb;
            --text: #333;
            --gray: #888;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        .user-stats h4 {
            margin-bottom: 20px;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .main {
            flex: 1;
            background: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }


        /* Header */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .main-header h2 {
            font-size: 22px;
            margin: 0;
        }

        .main-header p {
            margin: 5px 0 0;
            color: #888;
            font-size: 14px;
        }

        /* Steps */
        .steps {
            background: #f1f5f9;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            text-align: center;
            flex: 1;
            font-size: 14px;
            color: #999;
        }

        .step.active {
            color: #ff6aa2;
            font-weight: 600;
        }

        .step span {
            display: inline-flex;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #ff6aa2;
            color: #fff;
            align-items: center;
            justify-content: center;
            margin-bottom: 6px;
        }

        /* Statistic */
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .stat-header h3 {
            margin: 0;
        }

        .stat-header a {
            color: #ff6aa2;
            text-decoration: none;
            font-size: 14px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-box {
            background: #fff;
            border-radius: 14px;

            /* Viền nổi */
            border: 1px solid #eee;

            /* Đổ bóng */
            box-shadow:
                0 4px 10px rgba(0, 0, 0, 0.06),
                0 1px 3px rgba(0, 0, 0, 0.05);

            padding: 18px;
            display: flex;
            align-items: center;
            gap: 15px;

            transition: all 0.25s ease;
        }


        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .pink {
            background: #ff7ab8;
        }

        .green {
            background: #7ceaa5;
        }

        .blue {
            background: #9ac7ff;
        }

        .orange {
            background: #ffc07a;
        }

        .stat-box h4 {
            margin: 0;
            font-size: 14px;
            color: #888;
        }

        .stat-box b {
            font-size: 25px;
            color: #666;
        }

        /* Form */
        /* .withdraw-form {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
        } */
        .withdraw-form {
            background: #fff;
            border-radius: 16px;

            /* Viền */
            border: 1px solid #eee;

            /* Đổ bóng */
            box-shadow:
                0 6px 18px rgba(0, 0, 0, 0.08),
                0 2px 6px rgba(0, 0, 0, 0.05);

            padding: 24px;
            margin-top: 20px;
        }

        .withdraw-form h4 {
            margin: 0 0 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-group label {
            font-size: 13px;
            color: #555;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-top: 6px;
        }

        .note {
            background: #fff7c7;
            padding: 10px;
            border-radius: 6px;
            font-size: 13px;
            margin-top: 15px;
            color: #777;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-cancel {
            background: white;
            border: 1px solid #ff6aa2;
            color: #ff6aa2;
            padding: 8px 18px;
            border-radius: 6px;
        }

        .btn-confirm {
            background: #ff6aa2;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
        }

        .wrapper {
            max-width: 1300px;
            margin: 30px auto;
            display: flex;
            gap: 24px;
            margin-top: 130px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include 'test.php'; ?>

        <div class="main">
            <div class="main-header">
                <div>
                    <h2>Rút tiền</h2>
                    <p>Cùng khám phá tính năng rút tiền tại hệ thống</p>
                </div>
            </div>

            <div class="steps">
                <div class="step active">
                    <span>✓</span>
                    1. Nhập số tiền
                </div>
                <div class="step active">
                    <span>✓</span>
                    2. Xác nhận rút tiền
                </div>
                <div class="step active">
                    <span>3</span>
                    3. Admin duyệt giao dịch
                </div>
            </div>

            <!-- STAT -->
            <div class="stat-header">
                <h3>Thông số thống kê của bạn</h3>
                <a href="#">→ Xem lịch sử</a>
            </div>

            <div class="stat-grid">
                <div class="stat-box">
                    <div class="stat-icon pink">👁</div>
                    <div>
                        <h4>Tổng lượt xem</h4>
                        <b>0</b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon green">$</div>
                    <div>
                        <h4>Tổng thu nhập (VND)</h4>
                        <b>0 VND</b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon blue">💵</div>
                    <div>
                        <h4>Số tiền được rút (VND)</h4>
                        <b>0 VND</b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon orange">💳</div>
                    <div>
                        <h4>Số tiền đã rút (VND)</h4>
                        <b>0 VND</b>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <div class="withdraw-form">
                <h4>Nhập thông tin rút tiền</h4>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Ngân hàng *</label>
                        <select>
                            <option>ABBANK - Ngân hàng TMCP An Bình</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tên chủ thẻ *</label>
                        <input type="text" placeholder="THANH NHAC CHAU">
                    </div>

                    <div class="form-group">
                        <label>Số tài khoản *</label>
                        <input type="text" placeholder="101234567...">
                    </div>

                    <div class="form-group">
                        <label>Số tiền (Max 0 VND) *</label>
                        <input type="number" value="0">
                    </div>
                </div>

                <div class="note">
                    ⚠️ Lưu ý số tiền được rút phải tối thiểu 50.000 VND
                </div>

                <div class="form-actions">
                    <button class="btn-cancel">Hủy bỏ</button>
                    <button class="btn-confirm">Xác nhận</button>
                </div>
            </div>

        </div>

    </div>
</body>

</html>