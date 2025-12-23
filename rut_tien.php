<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'connect.php';
require 'auth.php';
require_login();

$id_nguoi_dung = $_SESSION['user_id'];

// Lấy số dư
$nd = $conn->query("
    SELECT so_du 
    FROM nguoi_dung 
    WHERE id = $id_nguoi_dung
")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_tien = (int)$_POST['so_tien'];
    $ngan_hang = $_POST['ngan_hang'];
    $stk = $_POST['so_tai_khoan'];
    $ten = $_POST['ten_chu_tk'];

    if ($so_tien <= 0) {
        $err = "Số tiền không hợp lệ";
    } elseif ($so_tien > $nd['so_du']) {
        $err = "Số dư không đủ";
    } else {
        $conn->query("
            INSERT INTO rut_tien
            (id_nguoi_dung, so_tien, ngan_hang, so_tai_khoan, ten_chu_tk)
            VALUES
            ($id_nguoi_dung, $so_tien, '$ngan_hang', '$stk', '$ten')
        ");
        $ok = "✅ Đã gửi yêu cầu rút tiền, chờ admin duyệt";
    }
}
?>

<h2>Rút tiền</h2>
<p>Số dư hiện tại: <b><?= number_format($nd['so_du']) ?>đ</b></p>

<?php if (!empty($err)) echo "<p style='color:red'>$err</p>"; ?>
<?php if (!empty($ok)) echo "<p style='color:green'>$ok</p>"; ?>

<form method="post">
    <input type="number" name="so_tien" placeholder="Số tiền muốn rút" required>
    <input type="text" name="ngan_hang" placeholder="Ngân hàng" required>
    <input type="text" name="so_tai_khoan" placeholder="Số tài khoản" required>
    <input type="text" name="ten_chu_tk" placeholder="Tên chủ tài khoản" required>
    <button type="submit">Gửi yêu cầu</button>
</form>
