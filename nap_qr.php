<?php
require 'connect.php';
require 'auth.php';
require_login();

$user_id = $_SESSION['user_id'];

$goi_nap = [
    10000 => ['dau' => 8000, 'hoa' => 8000, 'he_thong' => 2000],
    20000 => ['dau' => 16000, 'hoa' => 16000, 'he_thong' => 4000],
    50000 => ['dau' => 40000, 'hoa' => 40000, 'he_thong' => 10000],
    100000 => ['dau' => 85000, 'hoa' => 85000, 'he_thong' => 15000],
    200000 => ['dau' => 170000, 'hoa' => 170000, 'he_thong' => 30000],
    500000 => ['dau' => 450000, 'hoa' => 450000, 'he_thong' => 5000],
    1000000 => ['dau' => 900000, 'hoa' => 900000, 'he_thong' => 100000],
];
$so_tien = (int) ($_GET['so_tien'] ?? 0);
if (!$so_tien || !isset($goi_nap[$so_tien]))
    exit;

$ma_nap = 'NAP' . time() . rand(100, 999);

$stmt = $conn->prepare("
    INSERT INTO nap_tien (id_user, so_tien, ma_nap, trang_thai)
    VALUES (?, ?, ?, 'cho_duyet')
");
$stmt->bind_param("iis", $user_id, $so_tien, $ma_nap);
$stmt->execute();

$bank = "VCB";
$stk = "0123456789";
$ten = "NGUYEN VAN A";

$qr = "https://img.vietqr.io/image/$bank-$stk-compact2.png"
    . "?amount=$so_tien"
    . "&addInfo=$ma_nap"
    . "&accountName=" . urlencode($ten);
?>

<h3>Quét mã để chuyển khoản</h3>
<img src="<?= $qr ?>" width="260">

<p>Số tiền: <b><?= number_format($so_tien) ?>đ</b></p>
<p><img src="Ảnh/emoji_u1f353.png" width="14"> <?= number_format($goi_nap[$so_tien]['dau']) ?> dâu</p>
<p>🌸 <?= number_format($goi_nap[$so_tien]['hoa']) ?> hoa</p>
<p>Nội dung: <b><?= $ma_nap ?></b></p>

<p style="color:red;font-size:13px">
    ⚠️ Chuyển khoản đúng nội dung
</p>