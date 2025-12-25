<?php
require 'connect.php';
require 'time.php';
session_start();

/* ====== LẤY SLUG ====== */
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    die("Thiếu slug");
}

$user_id = $_SESSION['user_id'] ?? 0;

/* ====== 1. LẤY THÔNG TIN CHƯƠNG ====== */
$stmt = $conn->prepare("
    SELECT id, id_truyen, so_chuong, tieu_de, slug, gia, la_tra_phi, ngay_tao, luot_xem
    FROM chuong_truyen
    WHERE slug = ?
    LIMIT 1
");
$stmt->bind_param("s", $slug);
$stmt->execute();
$chuong = $stmt->get_result()->fetch_assoc();

if (!$chuong) {
    die("Chương không tồn tại");
}

$id_chuong = (int) $chuong['id'];
$id_truyen = (int) $chuong['id_truyen'];
$so_chuong = (int) $chuong['so_chuong'];
$gia = (float) $chuong['gia'];
$la_tra_phi = (int) $chuong['la_tra_phi'];

/* ====== 1.1 LẤY THÔNG TIN TRUYỆN + TÁC GIẢ + THỂ LOẠI ====== */
$stmt = $conn->prepare("
    SELECT 
        t.ten_truyen,
        t.slug AS slug_truyen,
        u.ten_hien_thi AS tac_gia,
        GROUP_CONCAT(tl.ten_the_loai SEPARATOR ', ') AS the_loai
    FROM truyen t
    LEFT JOIN nguoi_dung u ON t.id_tac_gia = u.id
    LEFT JOIN truyen_the_loai ttl ON t.id = ttl.id_truyen
    LEFT JOIN the_loai tl ON ttl.id_the_loai = tl.id
    WHERE t.id = ?
    GROUP BY t.id
    LIMIT 1
");
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();

/* ====== BIẾN DÙNG CHO GIAO DIỆN ====== */
$ten_truyen = $truyen['ten_truyen'] ?? '';
$slug_truyen = $truyen['slug_truyen'] ?? '';
$tac_gia = $truyen['tac_gia'] ?? 'Đang cập nhật';
$the_loai = $truyen['the_loai'] ?? '';
$thoi_gian_dang = timeAgo($chuong['ngay_tao']);

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
if ($r) {
    $chuong_truoc_slug = $r['slug'];
}

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
if ($r) {
    $chuong_sau_slug = $r['slug'];
}
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            margin-top: 120px;
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
            display: inline-flex;
            align-items: center;
            /* căn giữa theo chiều dọc */
            gap: 6px;
            /* khoảng cách icon – chữ */
            line-height: 1;
        }

        /* Nội dung */
        .chapter-content {
            margin-top: 70px;
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

        .chapter-header {
            margin-bottom: 25px;
        }

        .chapter-header h1 {
            font-size: 32px;
            font-weight: 700;
        }

        .story-name a {
            color: #6b7280;
            text-decoration: none;
            font-size: 16px;
        }

        .story-name {
            margin-bottom: 5px;
        }

        .story-meta {
            margin: 10px 0;
        }

        .tag {
            display: inline-block;
            background: #ff7ab8;
            color: #fff;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 13px;
            margin-right: 6px;
        }

        .chapter-info {
            display: flex;
            gap: 15px;
            font-size: 14px;
            color: #6b7280;
        }

        .views {
            background: #ffe6f0;
            color: #ff4d94;
            padding: 4px 10px;
            border-radius: 12px;
        }

        .btn-favorite {
            display: inline-flex;
            align-items: center;
            /* căn giữa theo chiều dọc */
            gap: 6px;
            /* khoảng cách icon – chữ */
            line-height: 1;
        }
    </style>
</head>

<body>

    <div class="page">

        <div class="chapter-header">

            <h1>
                Chương <?= $so_chuong ?>:
                <?= htmlspecialchars($chuong['tieu_de']) ?>
            </h1>

            <h2 class="story-name">
                <span class="btn-favorite">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-book-open">
                        <path d="M12 7v14"></path>
                        <path
                            d="M3 18a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h5a4 4 0 0 1 4 4 4 4 0 0 1 4-4h5a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1h-6a3 3 0 0 0-3 3 3 3 0 0 0-3-3z">
                        </path>
                    </svg><a href="truyen.php?slug=<?= htmlspecialchars($slug_truyen) ?>">
                        <?= htmlspecialchars($ten_truyen) ?>
                    </a></span>
            </h2>

            <div class="story-meta">
                <?php if (!empty($the_loai)): ?>
                    <?php foreach (explode(', ', $the_loai) as $tl): ?>
                        <span class="tag"><?= htmlspecialchars($tl) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="chapter-info">
                <span class="btn-favorite"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-user text-gray-500 w-5 h-5">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg> <?= htmlspecialchars($tac_gia) ?></span>
                <span class="btn-favorite"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-clock9">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 7.5 12"></polyline>
                    </svg> <?= $thoi_gian_dang ?></span>
                <span class="views"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-eye">
                        <path
                            d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0">
                        </path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg> <?= number_format($chuong['luot_xem']) ?></span>
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