<?php
require 'connect.php';
require 'auth.php';
require_login();

/* 1. Nhận id_truyen */
if (!isset($_GET['id_truyen'])) {
    die("Thiếu ID truyện");
}

$id_truyen = (int)$_GET['id_truyen'];
$id_user   = $_SESSION['user_id'];

/* 2. Kiểm tra quyền + lấy truyện */
$sql = "
    SELECT id, ten_truyen
    FROM truyen
    WHERE id = ? AND id_tac_gia = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_truyen, $id_user);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();

if (!$truyen) {
    die("Truyện không tồn tại hoặc bạn không có quyền");
}

/* 3. Lấy danh sách chương */
$sql = "
    SELECT id, so_chuong, tieu_de, gia, la_tra_phi, luot_xem, ngay_tao
    FROM chuong_truyen
    WHERE id_truyen = ?
    ORDER BY so_chuong ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$chuongs = $stmt->get_result();
?>

<h2>Quản lý chương: <?= htmlspecialchars($truyen['ten_truyen']) ?></h2>

<a href="them_chuong.php?id_truyen=<?= $id_truyen ?>">➕ Thêm chương</a>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Số chương</th>
        <th>Tiêu đề</th>
        <th>Loại</th>
        <th>Giá</th>
        <th>Lượt xem</th>
        <th>Ngày tạo</th>
        <th>Thao tác</th>
    </tr>

<?php while ($c = $chuongs->fetch_assoc()): ?>
    <tr>
        <td><?= $c['so_chuong'] ?></td>
        <td><?= htmlspecialchars($c['tieu_de']) ?></td>
        <td><?= $c['la_tra_phi'] ? '<b style="color:red">VIP</b>' : 'Free' ?></td>
        <td><?= number_format($c['gia']) ?></td>
        <td><?= number_format($c['luot_xem']) ?></td>
        <td><?= $c['ngay_tao'] ?></td>
        <td>
            <a href="sua_chuong.php?id=<?= $c['id'] ?>">✏️</a> |
            <a href="xoa_chuong.php?id=<?= $c['id'] ?>&id_truyen=<?= $id_truyen ?>"
               onclick="return confirm('Xóa chương này?')">
               🗑
            </a>
        </td>
    </tr>
<?php endwhile; ?>
</table>

<br>
<a href="quan_ly_truyen.php">⬅ Quay lại quản lý truyện</a>
