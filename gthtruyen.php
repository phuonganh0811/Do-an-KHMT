<?php
require 'connect.php';

/* ================== LẤY SLUG ================== */
$slug = $_GET['slug'] ?? '';
if (!$slug) {
  die("Thiếu slug truyện");
}

/* ================== LẤY TRUYỆN ================== */
$sql = "
SELECT t.*, tg.ten_tac_gia
FROM truyen t
LEFT JOIN tac_gia tg ON t.id_tac_gia = tg.id
WHERE t.slug = ?
LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$truyen) {
  die("Truyện không tồn tại");
}

$id_truyen = (int) $truyen['id'];

/* ================== LẤY THỂ LOẠI ================== */
$sql = "
SELECT tl.ten_the_loai
FROM the_loai tl
JOIN truyen_the_loai ttl ON tl.id = ttl.id_the_loai
WHERE ttl.id_truyen = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$theLoai = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

/* ================== TỔNG LƯỢT XEM (TỔNG VIEW CHƯƠNG) ================== */
$sql = "
SELECT COALESCE(SUM(luot_xem), 0) AS tong_luot_xem
FROM chuong_truyen
WHERE id_truyen = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$tongLuotXem = $stmt->get_result()->fetch_assoc()['tong_luot_xem'];
$stmt->close();

/* ================== ĐẾM CHƯƠNG ================== */
$sql = "SELECT COUNT(*) AS tong FROM chuong_truyen WHERE id_truyen = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$tongChuong = $stmt->get_result()->fetch_assoc()['tong'];
$stmt->close();

/* ================== PHÂN TRANG ================== */
$page = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

/* ================== LẤY DANH SÁCH CHƯƠNG ================== */
$sql = "
SELECT id, so_chuong, tieu_de, slug, la_tra_phi
FROM chuong_truyen
WHERE id_truyen = ?
ORDER BY so_chuong ASC
LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $id_truyen, $perPage, $offset);
$stmt->execute();
$chuongList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalPages = ceil($tongChuong / $perPage);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trang Truyện</title>
  <style>
    body {
      background: #ffe4ee;
      font-family: Arial;
      margin: 0;
      padding: 20px;
      color: #333;
    }

    .container {
      max-width: 1100px;
      margin: auto;
    }

    .card {
      background: #fff;
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .grid {
      display: grid;
      grid-template-columns: 1fr 2fr;
      gap: 20px;
    }

    img {
      width: 100%;
      height: 280px;
      object-fit: cover;
      border-radius: 18px;
    }

    .tags span {
      background: #f7b6d0;
      padding: 6px 12px;
      border-radius: 12px;
      margin-right: 6px;
      font-size: 14px;
    }

    .tabs {
      display: flex;
      border-radius: 20px;
      overflow: hidden;
    }

    .tab {
      flex: 1;
      text-align: center;
      padding: 12px;
      font-weight: bold;
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
      padding: 12px 0;
      border-bottom: 1px solid #ddd;
    }

    .btn {
      background: #f06292;
      color: #fff;
      padding: 6px 14px;
      border-radius: 12px;
      text-decoration: none;
    }

    input {
      padding: 8px 12px;
      width: 180px;
      border: 1px solid #ccc;
      border-radius: 10px;
    }

    .search-box {
      position: relative;
    }

    .search-box img {
      position: absolute;
      width: 18px;
      left: 8px;
      top: 9px;
      opacity: 0.5;
    }

    .search-box input {
      padding-left: 32px;
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="card grid">
      <div>
        <img src="<?= htmlspecialchars($truyen['anh_bia']) ?>" alt="cover">
      </div>
      <div>
        <h1 style="color:#e91e63; font-size:26px;">
          <?= htmlspecialchars($truyen['ten_truyen']) ?>
        </h1>

        <div class="tags" style="margin:12px 0;">
          <?php foreach ($theLoai as $tl): ?>
            <span><?= htmlspecialchars($tl['ten_the_loai']) ?></span>
          <?php endforeach; ?>
        </div>

        <p><?= nl2br(htmlspecialchars($truyen['tom_tat'])) ?></p>

        <div class="info" style="margin-top:12px; font-size:14px;">
          <p>Tác giả:
            <b><?= htmlspecialchars($truyen['ten_tac_gia'] ?? 'Đang cập nhật') ?></b>
          </p>
          <p>Lượt xem: <?= number_format($tongLuotXem) ?></p>
          <p>Chương: <?= $tongChuong ?></p>
          <p>Trạng thái:
            <b style="color:#e91e63;">
              <?= ucfirst(str_replace('_', ' ', $truyen['trang_thai'])) ?>
            </b>
          </p>
        </div>
      </div>
    </div>

    <div class="tabs card">
      <div class="tab active">📖 Chương (<?= $tongChuong ?>)</div>
      <div class="tab inactive">🎧 Audio (0)</div>
    </div>

    <div class="card">
      <h2 style="color:#e91e63;">Danh Sách Chương</h2>

      <?php foreach ($chuongList as $c): ?>
        <div class="chapter-item">
          <span>
            Chương <?= $c['so_chuong'] ?><?= $c['la_tra_phi'] ? ' 🔒' : '' ?>
            <?= htmlspecialchars($c['tieu_de']) ?>
          </span>
          <a class="btn" href="doc_chuong.php?slug=<?= urlencode($c['slug']) ?>">
            👁️ Đọc ngay
          </a>
        </div>
      <?php endforeach; ?>

      <!-- PHÂN TRANG -->
      <div style="text-align:center; margin-top:20px;">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a class="btn" style="<?= $i == $page ? '' : 'background:#fff;color:#e91e63;border:1px solid #e91e63;' ?>"
            href="?slug=<?= urlencode($slug) ?>&page=<?= $i ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>

    </div>
  </div>
</body>

</html>