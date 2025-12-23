<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'connect.php';
require 'auth.php';
require_login();

$user_id = $_SESSION['user_id'];

$so_tien = $_POST['so_tien'] ?? null;
$ma_nap = null;

if ($so_tien) {
    $so_tien = (int)$so_tien;
    if ($so_tien < 10000) {
        $error = "Số tiền tối thiểu 10.000đ";
    } else {
        $ma_nap = 'NAP' . time() . rand(100,999);

        // Lưu lịch sử nạp
        $stmt = $conn->prepare("
            INSERT INTO nap_tien (id_user, so_tien, ma_nap)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iis", $user_id, $so_tien, $ma_nap);
        $stmt->execute();

        // ⭐ CỘNG ĐIỂM ĐỀ CỬ
        $stmt2 = $conn->prepare("
            UPDATE nguoi_dung
            SET diem_de_cu = diem_de_cu + ?
            WHERE id = ?
        ");
        $stmt2->bind_param("ii", $so_tien, $user_id);
        $stmt2->execute();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Nạp tiền</title>
<style>
.box { max-width: 400px; margin: 40px auto; padding: 20px; border: 1px solid #ddd }
input, button { width: 100%; padding: 10px; margin-top: 10px }
.qr { text-align: center; margin-top: 20px }
</style>
</head>
<body>

<div class="box">
<h2>Nạp tiền bằng QR</h2>

<?php if (!$ma_nap): ?>
<form method="post">
    <label>Số tiền (VNĐ)</label>
    <input type="number" name="so_tien" required min="10000">
    <button type="submit">Tạo mã nạp</button>
    <?php if (!empty($error)) echo "<p style='color:red'>$error</p>"; ?>
</form>
<?php else: ?>

<?php
// Thông tin ngân hàng
$bank = "VCB";               // Vietcombank
$stk  = "0123456789";        // STK nhận tiền
$ten  = "NGUYEN VAN A";      // Chủ tài khoản

// Link QR VietQR
$qr = "https://img.vietqr.io/image/$bank-$stk-compact2.png"
    . "?amount=$so_tien"
    . "&addInfo=$ma_nap"
    . "&accountName=" . urlencode($ten);
?>

<div class="qr">
    <p><strong>Quét mã để chuyển khoản</strong></p>
    <img src="<?= $qr ?>" width="250">

    <p>Ngân hàng: <b>Vietcombank</b></p>
    <p>STK: <b><?= $stk ?></b></p>
    <p>Chủ TK: <b><?= $ten ?></b></p>
    <p>Số tiền: <b><?= number_format($so_tien) ?>đ</b></p>
    <p>Nội dung: <b><?= $ma_nap ?></b></p>

    <p style="color:red">
        ⚠️ Chuyển khoản đúng nội dung để được cộng tiền
    </p>
</div>

<?php endif; ?>
</div>

</body>
</html>
