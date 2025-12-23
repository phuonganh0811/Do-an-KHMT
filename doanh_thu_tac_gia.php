<?php
require 'connect.php';
require 'auth.php';
require_login();

$id_tac_gia = $_SESSION['user_id'] ?? 0;
if (!$id_tac_gia) {
    die("Bạn chưa đăng nhập");
}


$sql = "
SELECT 
    t.id AS id_truyen,
    t.ten_truyen,
    t.che_do_chia_luong,
    COUNT(mc.id) AS tong_luot_mua,
    SUM(
        ct.gia *
        CASE
            WHEN t.che_do_chia_luong = 'doc_quyen' THEN 0.9
            ELSE 0.7
        END
    ) AS doanh_thu_tac_gia
FROM truyen t
JOIN chuong_truyen ct ON ct.id_truyen = t.id
JOIN mua_chuong mc ON mc.id_chuong = ct.id
WHERE t.id_tac_gia = ?
GROUP BY t.id
ORDER BY doanh_thu_tac_gia DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_tac_gia);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Doanh thu tác giả</title>
<style>
body { font-family: Arial; background:#f5f5f5; padding:20px }
.truyen { background:#fff; padding:15px; margin-bottom:15px; border-radius:8px }
.money { color:#e53935; font-weight:bold; font-size:18px }
</style>
</head>
<body>

<h2>📊 Doanh thu tác giả</h2>

<?php if ($result->num_rows === 0): ?>
    <p>Chưa có lượt mua nào.</p>
<?php endif; ?>

<?php while ($row = $result->fetch_assoc()): ?>
<div class="truyen">
    <h3><?= htmlspecialchars($row['ten_truyen']) ?></h3>
    <p>Lượt mua: <?= $row['tong_luot_mua'] ?></p>
    <p>Chế độ chia: 
        <?= $row['che_do_chia_luong'] === 'doc_quyen' ? 'Độc quyền (90%)' : 'Không độc quyền (70%)' ?>
    </p>
    <p class="money">
        <?= number_format($row['doanh_thu_tac_gia']) ?> đ
    </p>
</div>
<?php endwhile; ?>

</body>
</html>
