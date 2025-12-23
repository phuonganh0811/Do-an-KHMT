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

/* truy vấn dữ liệu */
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
";


$result = $conn->query($sql);
if (!$result) {
    die("❌ Lỗi SQL: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Duyệt nạp tiền</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px
        }
    </style>
</head>

<body>

    <h2>DANH SÁCH NẠP CHỜ DUYỆT</h2>

    <?php if ($result->num_rows == 0): ?>
        <p>Không có yêu cầu nào</p>
    <?php else: ?>

        <table>
            <tr>
                <th>User</th>
                <th>Số tiền</th>
                <th>Mã nạp</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>

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
        </table>

    <?php endif; ?>

</body>

</html>