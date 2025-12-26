<?php include "menu.php"; ?>
<?php
require 'connect.php';
require 'auth.php';
require_login();

// Lấy ID tác giả hiện tại
$id_tac_gia = (int) $_SESSION['user_id'];

// Search truyện
$keyword = trim($_GET['search'] ?? '');

// Pagination
$perPage = 20;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Query tổng số truyện
$sql_count = "
    SELECT COUNT(DISTINCT t.id) AS total
    FROM truyen t
    WHERE t.ten_truyen LIKE ? 
      AND t.id_tac_gia = ?
";
$stmt_count = $conn->prepare($sql_count);
$like = '%' . $keyword . '%';
$stmt_count->bind_param("si", $like, $id_tac_gia);
$stmt_count->execute();
$row = $stmt_count->get_result()->fetch_assoc();
$totalRows = (int) ($row['total'] ?? 0);
$totalPages = max(1, ceil($totalRows / $perPage));

// ⚠️ Chặn page vượt giới hạn
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $perPage;
}

// Query danh sách truyện
$sql = "
SELECT 
    t.id,
    t.ten_truyen,
    t.slug,
    t.anh_bia,
    IFNULL(SUM(ct.luot_xem), 0) AS luot_xem,
    t.ngay_tao,
    t.trang_thai,
    nd.ten_hien_thi AS tac_gia,
    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS the_loai,
    COUNT(DISTINCT ct.id) AS tong_chuong
FROM truyen t
LEFT JOIN nguoi_dung nd ON t.id_tac_gia = nd.id
LEFT JOIN truyen_the_loai ttl ON ttl.id_truyen = t.id
LEFT JOIN the_loai tl ON tl.id = ttl.id_the_loai
LEFT JOIN chuong_truyen ct ON ct.id_truyen = t.id
WHERE t.ten_truyen LIKE ? 
  AND t.id_tac_gia = ?
GROUP BY t.id
ORDER BY t.ngay_tao DESC
LIMIT ?, ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siii", $like, $id_tac_gia, $offset, $perPage);
$stmt->execute();
$truyen_list = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý truyện</title>
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
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include('test.php'); ?>

        <div class="main">
            <div class="main-header">
                <h2>Quản lý Truyện</h2>
            </div>

            <div class="search-box">
                <form method="get" style="margin-bottom:20px;">
                    <input type="text" name="search" placeholder="Tìm kiếm tên truyện..."
                        value="<?= htmlspecialchars($keyword) ?>">
                </form>
                <a href="dang_truyen_form.php" class="btn full1">Thêm Truyện Mới</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên truyện</th>
                        <th>Số chương</th>
                        <th>Lượt xem</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($truyen = $truyen_list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $truyen['id'] ?></td>
                            <td>
                                <?php if (!empty($truyen['anh_bia'])): ?>
                                    <img src="<?= htmlspecialchars($truyen['anh_bia']) ?>" alt="Ảnh bìa" class="cover-img">
                                <?php else: ?>
                                    <span class="no-cover">Không có</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="truyen.php?slug=<?= urlencode($truyen['slug']) ?>" class="truyen-link">
                                    <?= htmlspecialchars($truyen['ten_truyen']) ?>
                                </a>
                            </td>
                            <td><?= $truyen['tong_chuong'] ?></td>
                            <td><?= number_format($truyen['luot_xem']) ?></td>
                            <td><?= htmlspecialchars($truyen['trang_thai']) ?></td>
                            <td>
                                <a href="sua_truyen_form.php?id=<?= $truyen['id'] ?>" class="btn">Sửa</a>
                                <a href="quan_ly_chuong.php?id_truyen=<?= $truyen['id'] ?>" class="btn">Chương</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="margin-top:20px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                        $url = '';
                        if ($i > 1) {
                            $url = "?page=$i";
                        }
                        if ($keyword !== '') {
                            $url .= ($url === '' ? '?' : '&') . "search=" . urlencode($keyword);
                        }
                        ?>
                        <a href="<?= $url ?>" class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </div>

    </div>

</body>

</html>