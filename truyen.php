<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'connect.php';

/* =======================
   1️⃣ LẤY SLUG
======================= */
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if ($slug == '') {
    die("Truyện không tồn tại!");
}

/* =======================
   2️⃣ LẤY THÔNG TIN TRUYỆN
======================= */
$sql_truyen = "
SELECT 
    t.id,
    t.ten_truyen,
    t.tom_tat,
    t.anh_bia,
    t.trang_thai,
    t.slug,
    t.diem_de_cu,

    nd.ten_hien_thi AS tac_gia,

    COUNT(DISTINCT ct.id) AS tong_chuong,
    IFNULL(SUM(ct.luot_xem), 0) AS tong_luot_xem,

    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS the_loai
FROM truyen t
LEFT JOIN nguoi_dung nd ON t.id_tac_gia = nd.id
LEFT JOIN chuong_truyen ct ON ct.id_truyen = t.id
LEFT JOIN truyen_the_loai ttl ON ttl.id_truyen = t.id
LEFT JOIN the_loai tl ON tl.id = ttl.id_the_loai
WHERE t.slug = ?
GROUP BY t.id
";

$mapTrangThai = [
    'dang_ra' => 'Đang ra',
    'hoan_thanh' => 'Hoàn thành',
    'tam_dung' => 'Tạm ngừng'
];

$stmt = $conn->prepare($sql_truyen);
$stmt->bind_param("s", $slug);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();

if (!$truyen) {
    die('Truyện không tồn tại!');
}

$id_truyen = $truyen['id'];

/* =======================
   3️⃣ XỬ LÝ GỬI BÌNH LUẬN
======================= */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['add_comment']) &&
    isset($_SESSION['user_id'])
) {
    $content = trim($_POST['content']);
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
        ? (int) $_POST['parent_id']
        : 0;

    if ($content !== '') {
        $sql = "INSERT INTO comments (truyen_id, user_id, parent_id, content)
                VALUES (?, ?, ?, ?)";
        $stmtC = $conn->prepare($sql);
        $stmtC->bind_param("iiis", $id_truyen, $_SESSION['user_id'], $parent_id, $content);
        $stmtC->execute();
    }

    header("Location: truyen.php?slug=" . urlencode($slug));
    exit;
}

/* =======================
   4️⃣ KIỂM TRA YÊU THÍCH
======================= */
$isFavorited = false;
if (isset($_SESSION['user_id'])) {
    $sqlFav = "SELECT 1 FROM truyen_yeu_thich 
               WHERE id_nguoi_dung = ? AND id_truyen = ?";
    $stmtFav = $conn->prepare($sqlFav);
    $stmtFav->bind_param("ii", $_SESSION['user_id'], $id_truyen);
    $stmtFav->execute();
    $stmtFav->store_result();
    $isFavorited = $stmtFav->num_rows > 0;
}

/* =======================
   5️⃣ LẤY DANH SÁCH CHƯƠNG
======================= */
$sql_chuong = "
SELECT 
    id,
    so_chuong,
    tieu_de,
    slug,
    gia,
    la_tra_phi,
    luot_xem
FROM chuong_truyen
WHERE id_truyen = ?
ORDER BY so_chuong ASC
";

$stmt_chuong = $conn->prepare($sql_chuong);
$stmt_chuong->bind_param("i", $id_truyen);
$stmt_chuong->execute();
$ds_chuong = $stmt_chuong->get_result();

$chuong_data = [];
while ($row = $ds_chuong->fetch_assoc()) {
    $chuong_data[] = $row;
}

$tong_chuong = count($chuong_data);

/* =======================
   6️⃣ LẤY BÌNH LUẬN
======================= */
$sql_comment = "
SELECT 
    c.id,
    c.parent_id,
    c.content,
    c.created_at,
    nd.ten_hien_thi,
    nd.avatar
FROM comments c
JOIN nguoi_dung nd ON c.user_id = nd.id
WHERE c.truyen_id = ?
ORDER BY c.created_at ASC
";


$stmtCM = $conn->prepare($sql_comment);
$stmtCM->bind_param("i", $id_truyen);
$stmtCM->execute();
$rsCM = $stmtCM->get_result();

$comments = [];
while ($row = $rsCM->fetch_assoc()) {
    $pid = $row['parent_id'] ?? 0;
    $comments[$pid][] = $row;
}

$tong_binh_luan = array_sum(array_map('count', $comments));


/* =======================
   7️⃣ HÀM HIỂN THỊ BÌNH LUẬN
======================= */
function renderComments($parent_id, $comments)
{
    if (!isset($comments[$parent_id]))
        return;

    foreach ($comments[$parent_id] as $c):

        $replyCount = isset($comments[$c['id']]) ? count($comments[$c['id']]) : 0;
        ?>

        <div class="comment <?= $parent_id != 0 ? 'reply' : '' ?>">

            <!-- AVATAR -->
            <div class="comment-avatar">
                <img src="<?= !empty($c['avatar'])
                    ? htmlspecialchars($c['avatar'])
                    : 'assets/avatar-default.png' ?>">
            </div>

            <!-- NỘI DUNG -->
            <div class="comment-content">

                <div class="comment-header">
                    <div class="comment-name">
                        <?= htmlspecialchars($c['ten_hien_thi']) ?>
                    </div>
                    <div class="comment-time">
                        <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                    </div>
                </div>

                <div class="comment-text">
                    <?= nl2br(htmlspecialchars($c['content'])) ?>
                </div>

                <!-- NÚT TRẢ LỜI -->
                <div class="reply-btn" onclick="toggleReplies(<?= $c['id'] ?>)">
                    Trả lời<?= $replyCount > 0 ? " ($replyCount)" : "" ?>
                </div>

                <!-- ====== WRAPPER ẨN/HIỆN ====== -->
                <div class="reply-wrapper" id="replies-<?= $c['id'] ?>">

                    <!-- DANH SÁCH REPLY (Ở TRÊN) -->
                    <div class="reply-list">
                        <?php renderComments($c['id'], $comments); ?>
                    </div>

                    <!-- FORM TRẢ LỜI (Ở DƯỚI) -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="post" class="reply-form">
                            <input type="hidden" name="parent_id" value="<?= $c['id'] ?>">

                            <textarea name="content" placeholder="Nhập nội dung trả lời..." required></textarea>

                            <div class="comment-actions">
                                <button type="button" class="btn-cancel" onclick="toggleReplies(<?= $c['id'] ?>)">
                                    Hủy
                                </button>

                                <button type="submit" name="add_comment" class="btn-submit">
                                    Trả lời
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <?php
    endforeach;
}
?>
<?php include "menu.php"; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Truyện</title>
    <style>
        /* ===== YÊU THÍCH ===== */
        .btn-favorite {
            background: #fff;
            border: 2px solid #ff5fa2;
            color: #ff5fa2;
            padding: 10px 18px;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .25s ease;
            display: inline-flex;
            align-items: center;
            /* căn giữa theo chiều dọc */
            gap: 6px;
            /* khoảng cách icon – chữ */
            line-height: 1;
            text-decoration: none;
        }

        .btn-favorite:hover {
            background: #ff5fa2;
            color: #fff;
        }

        .btn-favorite.active {
            background: #ff5fa2;
            color: #fff;
        }

        /* ===== ĐỀ CỬ ===== */
        #btnDeCu {
            background: #F87171;
            border: none;
            color: #fff;
            padding: 10px 18px;
            border-radius: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        #btnDeCu:hover {
            opacity: 0.9;
        }

        /* Popup đề cử */
        .popup-content h3 {
            color: #e91e63;
            margin-bottom: 10px;
        }

        .popup-content input {
            width: 100%;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 12px;
        }

        .popup-actions {
            display: flex;
            gap: 10px;
        }

        .popup-actions button {
            flex: 1;
            padding: 8px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
        }

        #xacNhanDeCu {
            background: #ff5fa2;
            color: #fff;
        }

        #dongPopup {
            background: #eee;
        }

        .popup {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .popup.hidden {
            display: none;
        }

        .popup-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        .tom-tat-wrapper {
            max-height: 4.5em;
            /* ~3 dòng nếu line-height ~1.5em */
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .tom-tat-wrapper.expanded {
            max-height: 1000px;
            /* khi mở rộng, đặt giá trị lớn */
        }

        .toggle-tom-tat {
            background: none;
            border: none;
            color: gray;
            cursor: pointer;
            padding: 0;
            margin-top: 5px;
        }

        .tom-tat {
            margin-bottom: 12px;
            /* khoảng cách giữa các đoạn */
            line-height: 1.7;
            color: #666;
        }

        .tom-tat:last-child {
            margin-bottom: 0;
        }


        body {
            background: #f6f7fb;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
            margin-top: 135px;
        }

        /* Card truyện */
        .story-card {
            display: flex;
            background: linear-gradient(135deg, #fff5fa, #ffffff);
            border-radius: 20px;
            padding: 24px;
            gap: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .story-cover img {
            width: 220px;
            height: 320px;
            object-fit: cover;
            border-radius: 16px;
        }

        .story-info h1 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 12px;
            color: #ff5fa2;
        }

        .tags {
            display: flex;
            gap: 8px;
            margin-bottom: 12px;
        }

        .tags span {
            background: #ff5fa2;
            color: #fff;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 13px;
        }

        .description {
            color: #666;
            margin-bottom: 16px;
            line-height: 1.6;
        }

        .meta {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            font-size: 14px;
        }

        .status {
            background: #ff5fa2;
            color: #fff;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 16px;
            margin: 30px 0 20px;
        }

        .tabs button {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 14px;
            background: #eee;
            font-weight: 600;
            cursor: pointer;
        }

        .tabs button.active {
            background: #ff5fa2;
            color: #fff;
        }

        /* Danh sách chương */
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .chapter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .chapter:last-child {
            border-bottom: none;
        }

        .chapter button {
            background: #ff5fa2;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
        }

        .active {
            background: #f06292;
            color: #fff;
        }

        .inactive {
            background: #fff;
            color: #777;
        }

        .chapter-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* ⭐ CĂN GIỮA THEO CHIỀU DỌC */
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
        }

        .btn {
            background: #f06292;
            color: #fff;
            padding: 6px 14px;
            border-radius: 12px;
            text-decoration: none;
        }

        .action-buttons {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            /* căn giữa ngang */
            align-items: center;
            /* căn giữa dọc (nếu có chiều cao) */
        }

        * {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        /* ===== KHUNG COMMENT ===== */
        .comment-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
            padding: 20px;
            max-width: 100%;
        }

        /* TIÊU ĐỀ */
        .comment-title {
            color: #ff5fa2;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 18px;
        }

        /* ===== COMMENT LIST ===== */
        .comment-list {
            margin-bottom: 20px;
            margin-left: 20px;
        }

        /* ===== COMMENT ===== */
        .comment {
            display: flex;
            gap: 12px;
            padding: 12px 0;
        }

        .comment+.comment {
            border-top: 1px solid #f2f2f2;
        }

        /* COMMENT REPLY (LỒNG) */
        .comment.reply {
            padding-left: 12px;
            border-left: 2px solid #ffe0ed;
        }

        /* ===== AVATAR ===== */
        .comment-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            overflow: hidden;
            background: #eee;
            flex-shrink: 0;
        }

        .comment-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===== NỘI DUNG ===== */
        .comment-content {
            flex: 1;
        }

        .comment-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .comment-name {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }

        .comment-time {
            font-size: 12px;
            color: #999;
        }

        .comment-text {
            font-size: 14px;
            margin: 6px 0;
            line-height: 1.6;
            color: #333;
        }

        /* ===== NÚT TRẢ LỜI ===== */
        .reply-btn {
            font-size: 13px;
            color: #ff5fa2;
            cursor: pointer;
            display: inline-block;
            margin-top: 4px;
        }

        .reply-btn:hover {
            text-decoration: underline;
        }

        /* ===== FORM BÌNH LUẬN / TRẢ LỜI ===== */
        .write-comment,
        .reply-form {
            margin-top: 12px;
        }

        /* .reply-form {
            display: none;
        } */

        /* TEXTAREA */
        .write-comment textarea,
        .reply-form textarea {
            width: 100%;
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            resize: none;
            outline: none;
            min-height: 70px;
        }

        .write-comment textarea::placeholder,
        .reply-form textarea::placeholder {
            color: #bbb;
        }

        /* ===== ACTIONS ===== */
        .comment-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 8px;
        }

        /* ===== BUTTON ===== */
        .btn-cancel {
            background: #f3f3f3;
            color: #555;
            border-radius: 8px;
            padding: 6px 14px;
            border: none;
            cursor: pointer;
        }

        .btn-submit {
            background: #ff5fa2;
            color: #fff;
            border-radius: 8px;
            padding: 6px 16px;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-submit:hover {
            opacity: .9;
        }

        /* ===== NO COMMENT ===== */
        .no-comment {
            text-align: center;
            padding: 30px 10px;
        }

        .no-comment img {
            width: 160px;
            opacity: .9;
            margin-bottom: 10px;
        }

        .no-comment .title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .no-comment .sub {
            font-size: 14px;
            color: #999;
        }

        .reply-wrapper {
            display: none;
            margin-top: 10px;
        }

        .reply-list {
            padding-left: 0;
            border-left: none;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Thông tin truyện -->
        <div class="story-card">
            <div class="story-cover">
                <img src="<?= htmlspecialchars($truyen['anh_bia']); ?>" alt="cover" />
                <div class="action-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button id="btnFavorite" class="btn-favorite <?= $isFavorited ? 'active' : '' ?>"
                            data-favorited="<?= $isFavorited ? '1' : '0' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-heart">
                                <path
                                    d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                                </path>
                            </svg> <?= $isFavorited ? 'Đã yêu thích' : 'Yêu thích' ?>
                        </button>
                    <?php else: ?>
                        <a href="dang_nhap.php" class="btn-favorite"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart">
                                <path
                                    d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z">
                                </path>
                            </svg> Yêu thích</a>
                    <?php endif; ?>
                </div>

            </div>

            <div class="story-info">
                <h1 style="color:#e91e63; font-size:26px;">
                    <?php echo $truyen['ten_truyen']; ?>
                </h1>

                <div class="tags" style="margin:12px 0;">
                    <?php
                    if (!empty($truyen['the_loai'])) {
                        $dsTheLoai = explode(', ', $truyen['the_loai']);
                        foreach ($dsTheLoai as $tenTheLoai) {
                            echo '<span>' . htmlspecialchars($tenTheLoai) . '</span>';
                        }
                    } else {
                        echo '<span>Chưa phân loại</span>';
                    }
                    ?>
                </div>

                <div class="description">
                    <?php
                    $doanVan = preg_split("/\r\n|\n|\r/", trim($truyen['tom_tat']));
                    echo '<div class="tom-tat-wrapper">';

                    foreach ($doanVan as $doan) {
                        if (trim($doan) !== '') {
                            echo '<p class="tom-tat">' . htmlspecialchars($doan) . '</p>';
                        }
                    }

                    echo '</div>';
                    ?>
                    <button class="toggle-tom-tat" onclick="toggleTomTat(this)">Xem thêm</button>
                </div>


                <div class="meta">
                    <p>Tác giả: <b><?php echo $truyen['tac_gia']; ?></b></p>
                    <p>Lượt xem: <?php echo number_format($truyen['tong_luot_xem']); ?></p>
                    <p>Đề cử: <strong id="tongDeCu"><?= $truyen['diem_de_cu'] ?></strong></p>
                    <p>Chương: <?php echo $truyen['tong_chuong']; ?></p>
                    <div class="de-cu-box">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button id="btnDeCu" class="btn-favorite"><svg xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star">
                                    <path
                                        d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                    </path>
                                </svg>Đề cử</button>
                        <?php else: ?>

                        <?php endif; ?>
                    </div>

                    <!-- POPUP -->
                    <div id="popupDeCu" class="popup hidden">
                        <div class="popup-content">
                            <h3>Đề cử truyện</h3>
                            <input type="number" id="soDiem" min="1" placeholder="Nhập số điểm">
                            <div class="popup-actions">
                                <button id="xacNhanDeCu">Xác nhận</button>
                                <button id="dongPopup">Hủy</button>
                            </div>
                        </div>
                    </div>
                    <div><strong>Trạng thái:</strong> <span class="status">
                            <?= $mapTrangThai[trim(strtolower($truyen['trang_thai']))] ?? 'Không xác định'; ?>
                        </span></div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="active">📖 Chương (<?php echo $tong_chuong; ?>)</button>
            <button>🎧 Audio (0)</button>
        </div>

        <!-- Danh sách chương -->
        <div class="card">

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h2 style="color:#e91e63;">Danh Sách Chương (<?= $tong_chuong ?>)</h2>
            </div>

            <div id="chapter-list"></div>
            <div id="pagination" style="text-align:center; margin-top:20px;"></div>

            <script>
                const chapters = <?= json_encode($chuong_data, JSON_UNESCAPED_UNICODE) ?>;

                const perPage = 20;
                const totalChapters = chapters.length;
                const totalPages = Math.ceil(totalChapters / perPage);

                function loadPage(page) {
                    const list = document.getElementById('chapter-list');
                    list.innerHTML = '';

                    const start = (page - 1) * perPage;
                    const end = Math.min(start + perPage, totalChapters);

                    for (let i = start; i < end; i++) {
                        const c = chapters[i];

                        list.innerHTML += `
                    <div class="chapter-item">
                        <span>
                            Chương ${c.so_chuong.toString().padStart(2, '0')}
                            ${c.tieu_de ? ' - ' + c.tieu_de : ''}
                        </span>

                        <a class="btn" href="doc_chuong.php?slug=${c.slug}">
                            Đọc ngay
                        </a>
                    </div>
                `;
                    }

                    renderPagination(page);
                }

                function renderPagination(active) {
                    const pag = document.getElementById('pagination');
                    pag.innerHTML = '';

                    function addBtn(i) {
                        pag.innerHTML += `
                    <button onclick="loadPage(${i})"
                        style="
                            padding:8px 14px;
                            margin:0 4px;
                            border-radius:10px;
                            border:1px solid #e91e63;
                            background:${i === active ? '#e91e63' : '#fff'};
                            color:${i === active ? '#fff' : '#e91e63'};
                        ">
                        ${i}
                    </button>
                `;
                    }

                    if (totalPages <= 9) {
                        for (let i = 1; i <= totalPages; i++) addBtn(i);
                        return;
                    }

                    addBtn(1);

                    if (active > 4) pag.innerHTML += `<span style="margin:0 6px;">...</span>`;

                    let start = Math.max(2, active - 1);
                    let end = Math.min(totalPages - 1, active + 1);

                    for (let i = start; i <= end; i++) addBtn(i);

                    if (active < totalPages - 3) pag.innerHTML += `<span style="margin:0 6px;">...</span>`;

                    addBtn(totalPages);
                }

                loadPage(1);
            </script>
            <script>
                function toggleTomTat(btn) {
                    const wrapper = btn.previousElementSibling; // div.tom-tat-wrapper
                    wrapper.classList.toggle('expanded');
                    btn.textContent = wrapper.classList.contains('expanded') ? 'Thu gọn' : 'Xem thêm';
                }
            </script>
            <script>
                const btnDeCu = document.getElementById('btnDeCu');
                const popup = document.getElementById('popupDeCu');
                const dongPopup = document.getElementById('dongPopup');
                const xacNhan = document.getElementById('xacNhanDeCu');
                const tongDeCu = document.getElementById('tongDeCu');

                btnDeCu.onclick = () => popup.classList.remove('hidden');
                dongPopup.onclick = () => popup.classList.add('hidden');

                xacNhan.onclick = () => {
                    const soDiem = document.getElementById('soDiem').value;
                    if (!soDiem || soDiem <= 0) {
                        alert('Nhập số điểm hợp lệ');
                        return;
                    }

                    fetch('de_cu.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            id_truyen: <?= $truyen['id'] ?>,
                            so_diem: soDiem
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                tongDeCu.innerText = data.tong_de_cu;
                                popup.classList.add('hidden');
                                alert('Đề cử thành công!');
                            } else {
                                alert(data.message);
                            }
                        });
                };
            </script>
            <script>
                const btnFavorite = document.getElementById('btnFavorite');
                const heartSVG = `
<svg xmlns="http://www.w3.org/2000/svg"
     width="16" height="16" viewBox="0 0 24 24"
     class="heart-icon"
     fill="none" stroke="currentColor"
     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <path d="M19 14c1.49-1.46 3-3.21 3-5.5
             A5.5 5.5 0 0 0 16.5 3
             c-1.76 0-3 .5-4.5 2
             -1.5-1.5-2.74-2-4.5-2
             A5.5 5.5 0 0 0 2 8.5
             c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
</svg>
`;

                if (btnFavorite) {
                    btnFavorite.addEventListener('click', () => {
                        fetch('yeu_thich.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                id_truyen: <?= $id_truyen ?>
                            })
                        })
                            .then(res => res.json())
                            .then(data => {
                                if (!data.success) {
                                    alert(data.message);
                                    return;
                                }

                                if (data.favorited) {
                                    btnFavorite.classList.add('active');
                                    btnFavorite.innerHTML = `${heartSVG} <span>Đã yêu thích</span>`;
                                    btnFavorite.dataset.favorited = '1';
                                } else {
                                    btnFavorite.classList.remove('active');
                                    btnFavorite.innerHTML = `${heartSVG} <span>Yêu thích</span>`;
                                    btnFavorite.dataset.favorited = '0';
                                }

                            });
                    });
                }
            </script>


        </div>
        <div class="comment-box">
            <div class="comment-title">
                💬 Bình luận (<?= $tong_binh_luan ?>)
            </div>

            <?php if ($tong_binh_luan == 0): ?>
                <div class="no-comment">
                    <img src="Ảnh/no-data.webp">
                    <p class="title">Chưa có bình luận!</p>
                    <p class="sub">Hãy là người đầu tiên bình luận truyện!</p>
                </div>
            <?php else: ?>
                <div class="comment-list">
                    <?php renderComments(0, $comments); ?>
                </div>
            <?php endif; ?>

            <!-- KHUNG VIẾT BÌNH LUẬN GỐC (LUÔN HIỆN) -->
            <div class="write-comment">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post">
                        <textarea name="content" placeholder="Nội dung bình luận..." required></textarea>
                        <input type="hidden" name="parent_id" value="0">
                        <div class="comment-actions">
                            <button type="reset" class="btn-cancel">Hủy bỏ</button>
                            <button type="submit" name="add_comment" class="btn-submit">Bình luận</button>
                        </div>
                    </form>
                <?php else: ?>
                    <textarea placeholder="Đăng nhập để bình luận..." readonly
                        onclick="window.location='dang_nhap.php'"></textarea>
                    <div class="comment-actions">
                        <a href="dang_nhap.php" class="btn-submit">Bình luận</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>


        <script>
            function toggleReplies(id) {
                const el = document.getElementById('replies-' + id);
                if (!el) return;
                el.style.display = (el.style.display === 'block') ? 'none' : 'block';
            }
        </script>




    </div>
</body>

</html>