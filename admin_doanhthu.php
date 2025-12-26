<?php
session_start();
require 'connect.php';

/* ===============================
   LẤY THÁNG CẦN XEM
================================ */
$thang = $_GET['thang'] ?? date('Y-m');
[$year, $month] = explode('-', $thang);

/* ===============================
   TỔNG DOANH THU TOÀN HỆ THỐNG
================================ */
$sqlTong = "
    SELECT 
        IFNULL(SUM(so_tien),0) AS tong,
        IFNULL(SUM(CASE WHEN id_chuong > 0 THEN so_tien ELSE 0 END),0) AS truyen,
        IFNULL(SUM(CASE WHEN id_chuong = 0 THEN so_tien ELSE 0 END),0) AS nap
    FROM doanh_thu_he_thong
";
$tong = $conn->query($sqlTong)->fetch_assoc();

/* ===============================
   DOANH THU THEO NGÀY TRONG THÁNG
================================ */
$stmt = $conn->prepare("
    SELECT 
        DATE(created_at) AS ngay,
        SUM(so_tien) AS tien
    FROM doanh_thu_he_thong
    WHERE MONTH(created_at)=? AND YEAR(created_at)=?
    GROUP BY DATE(created_at)
    ORDER BY ngay
");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$rsNgay = $stmt->get_result();

$labels = [];
$data = [];
while ($row = $rsNgay->fetch_assoc()) {
    $labels[] = $row['ngay'];
    $data[] = $row['tien'];
}

/* ===============================
   DOANH THU THEO LOẠI TRONG THÁNG
================================ */
$stmt2 = $conn->prepare("
    SELECT 
        IFNULL(SUM(CASE WHEN id_chuong > 0 THEN so_tien ELSE 0 END),0) AS truyen,
        IFNULL(SUM(CASE WHEN id_chuong = 0 THEN so_tien ELSE 0 END),0) AS nap
    FROM doanh_thu_he_thong
    WHERE MONTH(created_at)=? AND YEAR(created_at)=?
");
$stmt2->bind_param("ii", $month, $year);
$stmt2->execute();
$thangLoai = $stmt2->get_result()->fetch_assoc();
?>
<?php include "menu.php"; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý truyện</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-form {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            align-items: flex-end;
        }

        .admin-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .admin-form input[type="month"] {
            padding: 6px 8px;
        }

        /* CARD TỔNG QUAN */
        .admin-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .admin-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            background: #fff;
        }

        .admin-card h6 {
            margin: 0 0 6px;
            font-size: 14px;
        }

        /* BIỂU ĐỒ */
        .admin-charts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        canvas {
            width: 100% !important;
            height: 350px !important;
        }

        hr {
            margin: 30px 0;
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

        canvas {
            width: 100% !important;
            height: 350px !important;
        }

        .pie-box {
            width: 100%;
            max-width: 350px;
            margin: auto;
        }

        .pie-box canvas {
            height: 350px !important;
        }

        .btn-primary {
            background: #ff6fae;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include('test.php'); ?>

        <div class="main">
            <div class="main-header">
                <h2>Quản lý doanh thu hệ thống</h2>
            </div>

            <form method="get" class="admin-form">
                <div>
                    <label>Chọn tháng</label>
                    <input type="month" name="thang" value="<?= htmlspecialchars($thang) ?>">
                </div>
                <div>
                    <button type="submit" class="btn-primary">Xem thống kê</button>
                </div>
            </form>

            <!-- TỔNG QUAN -->
            <div class="admin-cards">
                <div class="admin-card">
                    <h6>Tổng doanh thu</h6>
                    <b><?= number_format($tong['tong']) ?> đ</b>
                </div>

                <div class="admin-card">
                    <h6>Doanh thu truyện</h6>
                    <b><?= number_format($tong['truyen']) ?> đ</b>
                </div>

                <div class="admin-card">
                    <h6>Doanh thu nạp tiền</h6>
                    <b><?= number_format($tong['nap']) ?> đ</b>
                </div>
            </div>

            <!-- BIỂU ĐỒ -->
            <div class="admin-charts">
                <div>
                    <h6>🔵 Tỷ lệ doanh thu (tất cả thời điểm)</h6>
                    <canvas id="pieAll" class="pie-box"></canvas>
                </div>

                <div>
                    <h6>🟢 Tỷ lệ doanh thu tháng <?= $month ?>/<?= $year ?></h6>
                    <canvas id="pieMonth" class="pie-box"></canvas>
                </div>
            </div>

            <hr>

            <h6>📅 Doanh thu theo ngày trong tháng <?= $month ?>/<?= $year ?></h6>
            <canvas id="barMonth"></canvas>
        </div>

        <script>
            /* PIE TOÀN BỘ */
            new Chart(document.getElementById('pieAll'), {
                type: 'pie',
                data: {
                    labels: ['Doanh thu truyện', 'Doanh thu nạp tiền'],
                    datasets: [{
                        data: [<?= $tong['truyen'] ?>, <?= $tong['nap'] ?>]
                    }]
                }
            });

            /* PIE THEO THÁNG */
            new Chart(document.getElementById('pieMonth'), {
                type: 'pie',
                data: {
                    labels: ['Doanh thu truyện', 'Doanh thu nạp tiền'],
                    datasets: [{
                        data: [<?= $thangLoai['truyen'] ?>, <?= $thangLoai['nap'] ?>]
                    }]
                }
            });

            /* BAR THEO NGÀY */
            new Chart(document.getElementById('barMonth'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels) ?>,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: <?= json_encode($data) ?>
                    }]
                }
            });
        </script>

    </div>
</body>

</html>