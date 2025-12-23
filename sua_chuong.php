<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
require 'auth.php';
require_login();

/* 1. Kiểm tra id chương */
if (!isset($_GET['id'])) {
    die("Thiếu ID chương");
}

$id_chuong = (int)$_GET['id'];
$id_user   = $_SESSION['user_id'];

/* 2. Lấy chương + kiểm tra quyền */
$sql = "
    SELECT c.*, t.id AS id_truyen
    FROM chuong_truyen c
    JOIN truyen t ON c.id_truyen = t.id
    WHERE c.id = ? AND t.id_tac_gia = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_chuong, $id_user);
$stmt->execute();
$chuong = $stmt->get_result()->fetch_assoc();

if (!$chuong) {
    die("Chương không tồn tại hoặc bạn không có quyền");
}

/* 3. Xử lý update */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de    = $_POST['tieu_de'];
    $noi_dung   = $_POST['noi_dung'];
    $gia        = $_POST['gia'];
    $la_tra_phi = $_POST['la_tra_phi'];

    $sql = "
        UPDATE chuong_truyen
        SET tieu_de = ?, noi_dung = ?, gia = ?, la_tra_phi = ?
        WHERE id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdii",
        $tieu_de,
        $noi_dung,
        $gia,
        $la_tra_phi,
        $id_chuong
    );
    $stmt->execute();

    header("Location: quan_ly_chuong.php?id_truyen=".$chuong['id_truyen']);
    exit;
}
?>

<!-- ================== HTML FORM ================== -->

<h2>Sửa chương <?= $chuong['so_chuong'] ?></h2>

<form method="post">
    <label>Tiêu đề</label><br>
    <input type="text" name="tieu_de"
           value="<?= htmlspecialchars($chuong['tieu_de']) ?>"
           style="width: 400px"><br><br>

    <label>Loại chương</label><br>
    <select name="la_tra_phi">
        <option value="0" <?= $chuong['la_tra_phi'] == 0 ? 'selected' : '' ?>>
            Miễn phí
        </option>
        <option value="1" <?= $chuong['la_tra_phi'] == 1 ? 'selected' : '' ?>>
            Trả phí
        </option>
    </select><br><br>

    <label>Giá</label><br>
    <input type="number" name="gia"
           value="<?= $chuong['gia'] ?>"><br><br>

    <label>Nội dung</label><br>
    <textarea name="noi_dung" rows="12" cols="100"><?= htmlspecialchars($chuong['noi_dung']) ?></textarea>
    <br><br>

    <button type="submit">💾 Lưu thay đổi</button>
    <a href="quan_ly_chuong.php?id_truyen=<?= $chuong['id_truyen'] ?>">Hủy</a>
</form>
