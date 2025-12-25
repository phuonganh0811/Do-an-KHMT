<?php include "menu.php"; ?>
<?php
require 'connect.php';
require 'auth.php';
require_login();

$id_tac_gia = $_SESSION['user_id'];

/* =========================
   1️⃣ TỔNG LƯỢT XEM (tất cả truyện của tác giả)
   = tổng lượt xem các chương
========================= */
$sql_view = "
SELECT COALESCE(SUM(c.luot_xem),0) AS tong_luot_xem
FROM truyen t
LEFT JOIN chuong_truyen c ON c.id_truyen = t.id
WHERE t.id_tac_gia = ?
";
$stmt = $conn->prepare($sql_view);
$stmt->bind_param("i", $id_tac_gia);
$stmt->execute();
$tong_luot_xem = $stmt->get_result()->fetch_assoc()['tong_luot_xem'];

/* =========================
   2️⃣ TỔNG THU NHẬP
   = tổng tiền mua chương của truyện tác giả
========================= */
$sql_income = "
SELECT 
    COALESCE(
        SUM(
            CASE 
                WHEN t.che_do_chia_luong = 'doc_quyen'
                    THEN c.gia * 0.9
                ELSE
                    c.gia * 0.7
            END
        ), 0
    ) AS tong_thu_nhap_tac_gia
FROM mua_chuong mc
JOIN chuong_truyen c ON mc.id_chuong = c.id
JOIN truyen t ON c.id_truyen = t.id
WHERE t.id_tac_gia = ?
";
$stmt = $conn->prepare($sql_income);
$stmt->bind_param("i", $id_tac_gia);
$stmt->execute();
$tong_thu_nhap = $stmt->get_result()->fetch_assoc()['tong_thu_nhap_tac_gia'];

/* =========================
   3️⃣ SỐ DƯ & TIỀN CÓ THỂ RÚT
   = so_du - 50.000
========================= */
$user = $conn->query("
    SELECT so_du 
    FROM nguoi_dung 
    WHERE id = $id_tac_gia
")->fetch_assoc();

$so_du = (int) $user['so_du'];
$so_tien_duoc_rut = max(0, $so_du - 50000);

/* =========================
   4️⃣ TỔNG TIỀN ĐÃ RÚT
========================= */
$sql_rut = "
SELECT COALESCE(SUM(so_tien),0) AS tong_da_rut
FROM rut_tien
WHERE id_nguoi_dung = ?
AND trang_thai = 'da_duyet'
";
$stmt = $conn->prepare($sql_rut);
$stmt->bind_param("i", $id_tac_gia);
$stmt->execute();
$tong_da_rut = $stmt->get_result()->fetch_assoc()['tong_da_rut'];

/* =========================
   5️⃣ XỬ LÝ RÚT TIỀN
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_tien = (int) $_POST['so_tien'];
    $ngan_hang = trim($_POST['ngan_hang']);
    $stk = trim($_POST['so_tai_khoan']);
    $ten = trim($_POST['ten_chu_tk']);

    if ($so_tien <= 0) {
        $err = "❌ Số tiền không hợp lệ";
    } elseif ($so_tien > $so_tien_duoc_rut) {
        $err = "❌ Số tiền vượt quá hạn mức được rút";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO rut_tien
            (id_nguoi_dung, so_tien, ngan_hang, so_tai_khoan, ten_chu_tk)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iisss", $id_tac_gia, $so_tien, $ngan_hang, $stk, $ten);
        $stmt->execute();

        $ok = "✅ Đã gửi yêu cầu rút tiền, chờ admin duyệt";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Rút tiền</title>
    <style>
        .divider-wrap {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 24px;
        }

        .divider {
            width: 100px;
            /* ~ w-32 */
            height: 1px;
            background-color: #d1d5db;
        }


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
            align-items: center;
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
                    <span>✓</span> <br>
                    1. Nhập số tiền
                </div>
                <div class="divider-wrap">
                    <div class="divider"></div>
                </div>
                <div class="step active">
                    <span>✓</span> <br>
                    2. Xác nhận rút tiền
                </div>
                <div class="divider-wrap">
                    <div class="divider"></div>
                </div>
                <div class="step active">
                    <span>3</span> <br>
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
                        <b>
                            <div class="value"><?= number_format($tong_luot_xem) ?></div>
                        </b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon green">$</div>
                    <div>
                        <h4>Tổng thu nhập (VND)</h4>
                        <b>
                            <div class="value"><?= number_format($tong_thu_nhap) ?> VND</div>
                        </b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon blue">💵</div>
                    <div>
                        <h4>Số tiền được rút (VND)</h4>
                        <b>
                            <div class="value"><?= number_format($so_tien_duoc_rut) ?> VND</div>
                        </b>
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-icon orange">💳</div>
                    <div>
                        <h4>Số tiền đã rút (VND)</h4>
                        <b>
                            <div class="value"><?= number_format($tong_da_rut) ?> VND</div>
                        </b>
                    </div>
                </div>
            </div>

            <!-- FORM -->
            <form method="post" class="withdraw-form">
                <h4>Nhập thông tin rút tiền</h4>

                <div class="form-grid">
                    <div class="form-group">
                        <label>Ngân hàng *</label>
                        <select name="ngan_hang" required>
                            <option value="">-- Chọn ngân hàng --</option>
                            <option value="ABBANK - Ngân hàng TMCP An Bình">
                                ABBANK - Ngân hàng TMCP An Bình
                            </option>
                            <option value="Vietcombank">Vietcombank</option>
                            <option value="Techcombank">Techcombank</option>
                            <option value="BIDV">BIDV</option>
                            <option value="MB Bank">MB Bank</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tên chủ thẻ *</label>
                        <input type="text" name="ten_chu_tk" placeholder="THANH NHẠC CHÂU" required>
                    </div>

                    <div class="form-group">
                        <label>Số tài khoản *</label>
                        <input type="text" name="so_tai_khoan" placeholder="101234567..." required>
                    </div>

                    <div class="form-group">
                        <label>Số tiền *</label>
                        <input type="number" name="so_tien" min="50000" max="<?= $so_tien_duoc_rut ?>" value="50000"
                            required>
                    </div>
                </div>

                <div class="note">
                    ⚠️ Số tiền rút tối thiểu <b>50.000 VND</b><br>
                    💰 Có thể rút tối đa: <b><?= number_format($so_tien_duoc_rut) ?> VND</b>
                </div>

                <?php if (!empty($err)): ?>
                    <div style="color:red; margin-top:10px;">
                        <?= $err ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($ok)): ?>
                    <div style="color:green; margin-top:10px;">
                        <?= $ok ?>
                    </div>
                <?php endif; ?>

                <div class="form-actions">
                    <button type="reset" class="btn-cancel">Hủy bỏ</button>
                    <button type="submit" class="btn-confirm" <?= $so_tien_duoc_rut < 50000 ? 'disabled' : '' ?>>
                        Xác nhận
                    </button>
                </div>
            </form>


        </div>
</body>

</html>