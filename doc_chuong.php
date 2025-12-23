<?php
require 'connect.php';
session_start();

/* ====== LẤY SLUG ====== */
$slug = $_GET['slug'] ?? '';
if (!$slug)
    die("Thiếu slug");

$user_id = $_SESSION['user_id'] ?? 0;

/* ====== 1. LẤY THÔNG TIN CHƯƠNG (CÓ ID TRUYỆN, SỐ CHƯƠNG) ====== */
$stmt = $conn->prepare("
    SELECT id, id_truyen, so_chuong, tieu_de, slug, gia, la_tra_phi, ngay_tao, luot_xem
    FROM chuong_truyen
    WHERE slug = ?
    LIMIT 1
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$chuong = $stmt->get_result()->fetch_assoc();
if (!$chuong)
    die("Chương không tồn tại");

$id_chuong = (int) $chuong['id'];
$id_truyen = (int) $chuong['id_truyen'];
$so_chuong = (int) $chuong['so_chuong'];
$gia = (float) $chuong['gia'];
$la_tra_phi = (int) $chuong['la_tra_phi'];

/* ====== 2. KIỂM TRA ĐÃ MUA CHƯƠNG ====== */
$da_mua = false;

if ($la_tra_phi == 1 && $user_id > 0) {
    $stmt = $conn->prepare("
        SELECT id 
        FROM mua_chuong
        WHERE id_nguoi_dung = ? AND id_chuong = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $user_id, $id_chuong);
    $stmt->execute();
    $da_mua = $stmt->get_result()->num_rows > 0;
}

/* ====== 3. NẾU ĐƯỢC ĐỌC → LẤY NỘI DUNG ====== */
$noi_dung = null;

if ($la_tra_phi == 0 || $da_mua) {
    $stmt = $conn->prepare("
        SELECT noi_dung 
        FROM chuong_truyen
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_chuong);
    $stmt->execute();
    $noi_dung = $stmt->get_result()->fetch_assoc()['noi_dung'];

    // Tăng lượt xem
    $conn->query("
        UPDATE chuong_truyen 
        SET luot_xem = luot_xem + 1 
        WHERE id = $id_chuong
    ");
}

/* ====== 4. LẤY CHƯƠNG TRƯỚC / SAU ====== */
$chuong_truoc_slug = null;
$chuong_sau_slug = null;

// Chương trước
$stmt = $conn->prepare("
    SELECT slug 
    FROM chuong_truyen
    WHERE id_truyen = ? AND so_chuong < ?
    ORDER BY so_chuong DESC
    LIMIT 1
");
$stmt->bind_param("ii", $id_truyen, $so_chuong);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if ($r)
    $chuong_truoc_slug = $r['slug'];

// Chương sau
$stmt = $conn->prepare("
    SELECT slug 
    FROM chuong_truyen
    WHERE id_truyen = ? AND so_chuong > ?
    ORDER BY so_chuong ASC
    LIMIT 1
");
$stmt->bind_param("ii", $id_truyen, $so_chuong);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if ($r)
    $chuong_sau_slug = $r['slug'];
?>

<?php include "menu.php"; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($chuong['tieu_de']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin-top: 60px;
            background: #ffffff;
            color: #222;
        }

        /* Khung trang */
        .page {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px 80px;
        }

        /* Header */
        .chapter-header h1 {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .tags {
            margin-bottom: 15px;
        }

        .tag {
            display: inline-block;
            background: #ff6fae;
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            margin-right: 6px;
        }

        .meta {
            color: #777;
            font-size: 14px;
            display: flex;
            gap: 20px;
        }

        .views {
            background: #ffe6f0;
            color: #ff4d94;
            padding: 4px 10px;
            border-radius: 12px;
        }

        /* Nội dung */
        .chapter-content {
            margin-top: 40px;
            font-size: 18px;
            line-height: 1.8;
        }

        .chapter-content h2 {
            margin-top: 40px;
            font-size: 22px;
        }

        .chapter-content blockquote {
            margin: 25px 0;
            padding-left: 20px;
            border-left: 4px solid #ff6fae;
            font-style: italic;
            color: #444;
        }

        .italic {
            font-style: italic;
        }

        /* Điều hướng */
        .chapter-nav {
            margin-top: 60px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 12px 28px;
            background: #ff6fae;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        .btn.outline {
            background: white;
            color: #ff6fae;
            border: 2px solid #ff6fae;
        }

        .chapter-content {
            line-height: 1.8;
            white-space: pre-line;
            /* giữ xuống dòng nhưng bỏ khoảng trắng đầu dòng */
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="chapter-header">
            <h1><?= htmlspecialchars($chuong['tieu_de']) ?></h1>
            <div class="meta">
                <span>⏰ <?= date('d/m/Y', strtotime($chuong['ngay_tao'])) ?></span>
                <span class="views">👁 <?= number_format($chuong['luot_xem']) ?></span>
            </div>
        </div>

        <?php if (!$la_tra_phi || $da_mua): ?>

            <!-- ✅ ĐƯỢC ĐỌC -->
            <div class="chapter-content">
                <?= nl2br(htmlspecialchars($noi_dung)) ?>
            </div>

        <?php else: ?>

            <!-- 🔒 CHƯƠNG BỊ KHÓA -->
            <div class="chapter-content" style="text-align:center">
                <h2>🔒 Chương này là chương trả phí</h2>
                <p>Giá: <b><?= number_format($gia) ?> đậu</b></p>

                <?php if (!$user_id): ?>
                    <a href="dang_nhap.php" class="btn">Đăng nhập để mở khóa</a>
                <?php else: ?>
                    <form action="mua_chuong.php" method="POST">
                        <input type="hidden" name="id_chuong" value="<?= $id_chuong ?>">
                        <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">
                        <button type="submit" class="btn">🔓 Mở khóa chương</button>
                    </form>
                <?php endif; ?>
            </div>

        <?php endif; ?>


        <div class="chapter-nav">
            <?php if ($chuong_truoc_slug): ?>
                <a href="doc_chuong.php?slug=<?= htmlspecialchars($chuong_truoc_slug) ?>" class="btn outline">← Chương
                    trước</a>
            <?php else: ?>
                <button class="btn outline" disabled>← Chương trước</button>
            <?php endif; ?>

            <?php if ($chuong_sau_slug): ?>
                <a href="doc_chuong.php?slug=<?= htmlspecialchars($chuong_sau_slug) ?>" class="btn">Chương sau →</a>
            <?php else: ?>
                <button class="btn" disabled>Chương sau →</button>
            <?php endif; ?>
        </div>


    </div>
</body>

</html>