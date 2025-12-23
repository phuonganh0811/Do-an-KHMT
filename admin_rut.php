<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'connect.php';
require 'auth.php';
require_login();

$ds = $conn->query("
    SELECT r.*, n.ten_dang_nhap
    FROM rut_tien r
    JOIN nguoi_dung n ON n.id = r.id_nguoi_dung
    ORDER BY r.id DESC
");
?>

<h2>Quản lý rút tiền</h2>

<table border="1" cellpadding="6">
<tr>
    <th>User</th>
    <th>Số tiền</th>
    <th>Ngân hàng</th>
    <th>STK</th>
    <th>Trạng thái</th>
    <th>Hành động</th>
</tr>

<?php while ($r = $ds->fetch_assoc()): ?>
<tr>
    <td><?= $r['ten_dang_nhap'] ?></td>
    <td><?= number_format($r['so_tien']) ?>đ</td>
    <td><?= $r['ngan_hang'] ?></td>
    <td><?= $r['so_tai_khoan'] ?></td>
    <td><?= $r['trang_thai'] ?></td>
    <td>
        <?php if ($r['trang_thai'] === 'cho_duyet'): ?>
            <a href="duyet_rut.php?id=<?= $r['id'] ?>"
               onclick="return confirm('Xác nhận duyệt rút tiền?')">
               Duyệt
            </a>
        <?php endif; ?>
    </td>
</tr>
<?php endwhile; ?>
</table>
