<?php include "menu.php"; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php';
require 'auth.php';
require_login();

$user_id = $_SESSION['user_id'];

/* Gói nạp & khuyến mãi */
$goi_nap = [
    10000 => ['dau' => 8000, 'hoa' => 8000, 'he_thong' => 2000],
    20000 => ['dau' => 16000, 'hoa' => 16000, 'he_thong' => 4000],
    50000 => ['dau' => 40000, 'hoa' => 40000, 'he_thong' => 10000],
    100000 => ['dau' => 85000, 'hoa' => 85000, 'he_thong' => 15000],
    200000 => ['dau' => 170000, 'hoa' => 170000, 'he_thong' => 30000],
    500000 => ['dau' => 450000, 'hoa' => 450000, 'he_thong' => 5000],
    1000000 => ['dau' => 900000, 'hoa' => 900000, 'he_thong' => 100000],
];

$so_tien = isset($_GET['so_tien']) ? (int) $_GET['so_tien'] : 0;
$ma_nap = null;

/* Khi bấm "Nạp ngay" */
if ($so_tien && isset($goi_nap[$so_tien])) {

    $ma_nap = 'NAP' . time() . rand(100, 999);

    $stmt = $conn->prepare("
        INSERT INTO nap_tien (id_user, so_tien, ma_nap, trang_thai)
        VALUES (?, ?, ?, 'cho_duyet')
    ");
    $stmt->bind_param("iis", $user_id, $so_tien, $ma_nap);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Nạp điểm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        .user-stats h4{
            margin-bottom: 20px;
        }
        /* ================= BIẾN MÀU ================= */
        :root {
            --pink: #ff6fae;
            --pink-dark: #ff4f9a;
            --pink-light: #ffe6f1;
            --bg: #f5f6fa;
            --border: #e5e7eb;
            --text: #333;
            --gray: #888;
        }

        /* ================= RESET ================= */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: "Segoe UI", sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* ================= LAYOUT ================= */
        .wrapper {
            max-width: 1300px;
            margin: 30px auto;
            display: flex;
            gap: 24px;
            margin-top: 130px;
        }

        /* ================= USER CARD ================= */


        /* ================= MAIN ================= */
        .main {
            flex: 1;
            background: white;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
        }

        /* ================= HEADER ================= */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .main-header h2 {
            font-size: 22px;
        }

        .back {
            color: var(--pink);
            text-decoration: none;
            font-weight: 500;
            border: 1px solid var(--pink);
            padding: 6px 12px;
            border-radius: 10px;
        }

        /* ================= STEPS ================= */
        .steps {
            background: #f1f5f9;
            border-radius: 14px;
            padding: 18px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
        }

        .step.active {
            color: var(--pink);
            font-weight: 600;
        }

        .step span {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--pink);
            color: white;
        }

        /* ================= TAB ================= */
        .tabs {
            display: flex;
            background: #f3f4f6;
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 16px;
        }

        .tab {
            flex: 1;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 500;
        }

        .tab.active {
            background: var(--pink);
            color: white;
        }

        /* ================= GUIDE ================= */
        .guide {
            background: #f3f4f6;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 14px;
            margin-bottom: 18px;
        }

        /* ================= TABLE ================= */
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: var(--pink-light);
            color: var(--pink-dark);
            padding: 12px;
            text-align: left;
        }

        .table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--border);
        }

        .badge {
            background: #dcfce7;
            color: #166534;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
            display: inline-block;
        }

        .pay {
            background: #e0e7ff;
            color: #1e40af;
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 13px;
            display: inline-block;
        }

        .action {
            background: var(--pink);
            color: white;
            padding: 8px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-block;
        }

        /* popup */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .6);
            z-index: 999;
        }

        .modal-content {
            background: #fff;
            width: 360px;
            margin: 8% auto;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        .close {
            float: right;
            font-size: 22px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <?php include('test.php'); ?>


        <!-- MAIN -->
        <div class="main">
            <div class="main-header">
                <h2>Gói nạp</h2>
                <a href="#" class="back">← Quay lại</a>
            </div>

            <div class="steps">
                <div class="step active"><span>✓</span> 1. Chọn số tiền</div>
                <div class="step active"><span>✓</span> 2. Xác nhận</div>
                <div class="step active"><span>3</span> Chuyển khoản</div>
            </div>

            <div class="tabs">
                <div class="tab active">Chọn gói nạp</div>
            </div>

            <div class="guide">
                ℹ️ Hướng dẫn: Chọn gói nạp bên dưới và nhấn <b>Nạp ngay</b>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Số tiền</th>
                        <th>Số dâu</th>
                        <th>Số hoa (tặng)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    foreach ($goi_nap as $tien => $diem): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= number_format($tien) ?> VND</td>
                            <td>+ <?= number_format($diem['dau']) ?> <img src="Ảnh/emoji_u1f353.png" width="14"></td>
                            <td>+ <?= number_format($diem['hoa']) ?> 🌸</td>
                            <td>
                                <a href="javascript:void(0)" class="action" data-tien="<?= $tien ?>"
                                    onclick="napTien(this)">
                                    Nạp ngay
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>
    </div>
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeQR()">×</span>
            <div id="qrContent"></div>
        </div>
    </div>
    <script>
        function napTien(btn) {
            const soTien = btn.dataset.tien;

            fetch("nap_qr.php?so_tien=" + soTien)
                .then(res => res.text())
                .then(html => {
                    document.getElementById("qrContent").innerHTML = html;
                    document.getElementById("qrModal").style.display = "block";
                });
        }

        function closeQR() {
            document.getElementById("qrModal").style.display = "none";
        }
    </script>

</body>

</html>