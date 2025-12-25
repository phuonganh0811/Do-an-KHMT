<?php include "menu.php"; ?>
<?php
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: dang_nhap.php');
    exit;
}

$id_user = $_SESSION['user_id'];

$sql = "
SELECT 
    t.id,
    t.ten_truyen,
    t.slug,
    t.anh_bia,
    nd.ten_hien_thi AS tac_gia
FROM truyen_yeu_thich yt
JOIN truyen t ON yt.id_truyen = t.id
LEFT JOIN nguoi_dung nd ON t.id_tac_gia = nd.id
WHERE yt.id_nguoi_dung = ?
ORDER BY yt.ngay_tao DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$dsTruyenYeuThich = [];
while ($row = $result->fetch_assoc()) {
    $dsTruyenYeuThich[] = $row;
}

$soLuong = count($dsTruyenYeuThich);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background: #f7f8fc;
            padding-top: 45px;
            /* bằng chiều cao header */
        }

        .container {
            display: flex;
            gap: 24px;
            max-width: 1300px;
            margin: 0 auto;
            margin-top: 70px;

            /* margin-top: 30px;
            display: flex;
            gap: 30px;
            padding: 30px; */
        }

        .content {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 30px;
        }

        .favorite-wrapper {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            min-height: 400px;
        }

        .favorite-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .favorite-header h2 {
            font-size: 22px;
            color: #666;
        }

        .btn-back {
            border: 2px solid #ff5fa2;
            color: #ff5fa2;
            padding: 8px 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
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

        .btn-read {
            background: #ff5fa2;
            color: #fff;
            padding: 12px 22px;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 600;
        }

        .favorite-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        .favorite-item {
            border: 1px solid #f0f0f0;
            border-radius: 16px;
            overflow: hidden;
            transition: .2s;
            width: 230px;
            height: 350px;
        }

        .favorite-item:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
        }

        .favorite-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .favorite-item .info {
            padding: 12px;
            padding-top: 0px;
        }

        .favorite-item h4 {
            font-size: 16px;
            margin-bottom: 6px;
        }

        .favorite-item p {
            font-size: 13px;
            color: #777;
        }

        .btn-read-sm {
            /* display: inline-block; */
            margin-top: 8px;
            background: #ff5fa2;
            color: #fff;
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            text-decoration: none;
            width: 100%;
            display: flex;
            justify-content: center;
            font-weight: 550;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include('test.php'); ?>
        <!-- MAIN CONTENT -->
        <main class="content">
            <div class="favorite-header">
                <h2>Truyện yêu thích (<?= $soLuong ?>)</h2>
                <a href="TrangChucopy.php" class="btn-back">← Quay lại</a>
            </div>

            <?php if ($soLuong == 0): ?>
                <!-- CHƯA CÓ TRUYỆN -->
                <div class="favorite-empty">
                    <img src="Ảnh/no-data.webp" alt="empty" style="width: 130px;">
                    <h3>Chưa có dữ liệu nào!</h3>
                    <p>Rất tiếc! Hiện tại chưa có dữ liệu!</p>
                    <a href="index.php" class="btn-read">
                        ▶ Đọc truyện ngay
                    </a>
                </div>
            <?php else: ?>
                <!-- CÓ TRUYỆN -->
                <div class="favorite-list">
                    <?php foreach ($dsTruyenYeuThich as $truyen): ?>
                        <div class="favorite-item">
                            <img src="<?= htmlspecialchars($truyen['anh_bia']) ?>" alt="">
                            <div class="info">
                                <h4><?= htmlspecialchars($truyen['ten_truyen']) ?></h4>
                                <p>Tác giả: <?= htmlspecialchars($truyen['tac_gia']) ?></p>
                                <a href="truyen.php?slug=<?= $truyen['slug'] ?>" class="btn-read-sm">
                                    Đọc ngay
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>

</html>