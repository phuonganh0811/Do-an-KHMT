<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'connect.php';
require 'auth.php';

/* kiểm tra đăng nhập */
if (!function_exists('require_login')) {
    die("❌ Không tồn tại require_login()");
}
require_login();

/* kiểm tra quyền admin */
if (!isset($_SESSION['vai_tro']) || $_SESSION['vai_tro'] !== 'quan_tri') {
    die("❌ Không có quyền admin");
}

/* ===== PHÂN TRANG ===== */
$limit = 15; // 15 dòng / trang
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/* ===== ĐẾM TỔNG DÒNG ===== */
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM nap_tien WHERE trang_thai = 'cho_duyet'");
$totalRow = $totalResult->fetch_assoc();
$totalRecords = $totalRow['total'];
$totalPages = ceil($totalRecords / $limit);

/* ===== LẤY DỮ LIỆU THEO TRANG ===== */
$sql = "
    SELECT 
        n.id,
        n.so_tien,
        n.ma_nap,
        n.trang_thai,
        n.created_at,
        u.ten_dang_nhap
    FROM nap_tien n
    JOIN nguoi_dung u ON u.id = n.id_user
    WHERE n.trang_thai = 'cho_duyet'
    ORDER BY n.created_at DESC
    LIMIT $limit OFFSET $offset
";

$result = $conn->query($sql);

if (!$result) {
    die("❌ Lỗi SQL: " . $conn->error);
}

?>


<?php include "menu.php"; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý rút tiền</title>
    <style>
        .user-stats h4 {
            margin-bottom: 20px;
        }

        .truyen-link {
            color: #ff6fae;
            font-weight: 600;
            text-decoration: none;
        }

        .truyen-link:hover {
            text-decoration: underline;
        }

        .cover-img {
            width: 60px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #eee;
        }

        .no-cover {
            color: #999;
            font-size: 13px;
        }

        .pagination {
            display: flex;
            justify-content: center;
        }

        .page-item {
            padding: 8px 14px;
            margin: 0 4px;
            border-radius: 10px;
            border: 1px solid #ff6fae;
            background: #fff;
            color: #ff6fae;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .page-item:hover {
            background: #fce4ec;
        }

        .page-item.active {
            background: #ff6fae;
            color: #fff;
            font-weight: bold;
        }

        .search-box {
            position: relative;
            margin-top: 10px;
        }

        .search-box input {
            width: 320px;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: #ff80bf;
            box-shadow: 0 0 6px rgba(255, 128, 191, 0.4);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .btn.full1 {
            width: 20%;
            background: #ff6fae;
            color: white;
            margin-top: 12px;
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
            margin-top: 30px;
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

        .favorite-empty {
            text-align: center;
            margin-top: 60px;
        }

        .favorite-empty img {
            width: 180px;
            opacity: 0.7;
        }

        .favorite-empty h3 {
            margin-top: 20px;
            font-size: 20px;
        }

        .favorite-empty p {
            color: #888;
            margin: 8px 0 20px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include('test.php'); ?>

        <div class="main">
            <div class="main-header">
                <h2>Danh sách nạp chờ duyệt</h2>
            </div>
            <?php if ($result->num_rows == 0): ?>
                <div class="favorite-empty">
                    <img src="Ảnh/no-data.webp" alt="empty" style="width: 130px;">
                    <h3>Chưa có dữ liệu nào!</h3>
                    <p>Rất tiếc! Hiện tại chưa có dữ liệu!</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Số tiền</th>
                            <th>Mã nạp</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['ten_dang_nhap']) ?></td>
                                <td><?= number_format($row['so_tien']) ?>đ</td>
                                <td><?= htmlspecialchars($row['ma_nap']) ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <a href="duyet_nap.php?id=<?= $row['id'] ?>">Duyệt</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <!-- Pagination -->

            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="margin-top:20px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>