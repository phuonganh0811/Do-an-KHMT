<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'connect.php';
// Lấy slug từ URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if ($slug == '') {
    die("Truyện không tồn tại!");
}

/* =======================
   2️⃣ LẤY THÔNG TIN TRUYỆN
   - tên truyện
   - tác giả (nguoi_dung)
   - thể loại
   - tổng số chương
   - tổng lượt xem (SUM chương)
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
   3️⃣ LẤY DANH SÁCH CHƯƠNG
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
?>
<?php include "menu.php"; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Truyện</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                            <button id="btnDeCu" class="btn-favorite"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star">
                                    <path
                                        d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z">
                                    </path>
                                </svg>Đề cử</button>
                        <?php else: ?>
                            <p><a href="dang_nhap.php">Đăng nhập</a> để đề cử</p>
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
    </div>
</body>

</html>