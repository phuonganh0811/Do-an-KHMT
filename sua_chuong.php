<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
require 'auth.php';
require_login();

/* =========================
   1. Kiểm tra ID chương
========================= */
if (!isset($_GET['id'])) {
    die("Thiếu ID chương");
}

$id_chuong = (int)$_GET['id'];
$id_user   = (int)$_SESSION['user_id'];

/* =========================
   2. Lấy vai trò user
========================= */
$stmtRole = $conn->prepare("
    SELECT vai_tro 
    FROM nguoi_dung 
    WHERE id = ?
");
$stmtRole->bind_param("i", $id_user);
$stmtRole->execute();
$user = $stmtRole->get_result()->fetch_assoc();

$isAdmin = ($user && $user['vai_tro'] === 'quan_tri');

/* =========================
   3. Lấy chương + kiểm tra quyền
   - Admin: sửa tất cả
   - Tác giả: chỉ sửa chương truyện của mình
========================= */
if ($isAdmin) {
    // ADMIN: lấy chương không cần check tác giả
    $sql = "
        SELECT c.*, t.id AS id_truyen, t.id_tac_gia
        FROM chuong_truyen c
        JOIN truyen t ON c.id_truyen = t.id
        WHERE c.id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_chuong);
} else {
    // TÁC GIẢ: chỉ được sửa chương truyện của mình
    $sql = "
        SELECT c.*, t.id AS id_truyen, t.id_tac_gia
        FROM chuong_truyen c
        JOIN truyen t ON c.id_truyen = t.id
        WHERE c.id = ? AND t.id_tac_gia = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_chuong, $id_user);
}

$stmt->execute();
$chuong = $stmt->get_result()->fetch_assoc();

if (!$chuong) {
    die("Chương không tồn tại hoặc bạn không có quyền chỉnh sửa");
}

/* =========================
   4. Xử lý cập nhật
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tieu_de  = trim($_POST['tieu_de']);
    $noi_dung = trim($_POST['noi_dung']);

    // checkbox
    $la_tra_phi = isset($_POST['la_tra_phi']) ? 1 : 0;

    // nếu không trả phí → giá = 0
    $gia = $la_tra_phi
        ? (isset($_POST['gia']) ? floatval($_POST['gia']) : 0)
        : 0;

    $sql = "
        UPDATE chuong_truyen
        SET 
            tieu_de = ?, 
            noi_dung = ?, 
            gia = ?, 
            la_tra_phi = ?
        WHERE id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssdii",
        $tieu_de,
        $noi_dung,
        $gia,
        $la_tra_phi,
        $id_chuong
    );
    $stmt->execute();

    header("Location: sua_chuong.php?id=" . $id_chuong . "&updated=1");
    exit;
}
?>
<?php include "menu.php"; ?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thêm Chương - <?= htmlspecialchars($truyen['ten_truyen']) ?></title>
    <style>
        /* Ẩn checkbox gốc */
        .checkbox-label input[type="checkbox"] {
            display: none;
        }

        /* Tạo ô tick tuỳ chỉnh */
        .checkbox-label .checkmark {
            display: inline-block;
            width: 20px;
            height: 20px;
            background-color: #fff;
            border: 2px solid #ff69b4;
            /* viền hồng */
            border-radius: 6px;
            /* bo góc */
            margin-left: 10px;
            /* khoảng cách từ chữ */
            vertical-align: middle;
            position: relative;
            cursor: pointer;
        }

        /* Hiển thị tick khi checked */
        .checkbox-label input:checked+.checkmark::after {
            content: "";
            position: absolute;
            left: 5px;
            top: 1px;
            width: 5px;
            height: 10px;
            border: solid #ff69b4;
            /* màu tick hồng */
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        /* Hover effect */
        .checkbox-label .checkmark:hover {
            background-color: #ffe6f0;
            /* hồng nhạt khi hover */
        }

        .row1 {
            display: flex;
            gap: 90px;
            /* 👈 tăng khoảng cách */
            align-items: flex-end;
        }

        * {
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            margin: 0;
            background: #f7f8fc;
        }


        /* Layout */
        .container {
            display: flex;
            gap: 24px;
            max-width: 1300px;
            margin: 0 auto;
            margin-top: 130px;

            /* margin-top: 30px;
            display: flex;
            gap: 30px;
            padding: 30px; */
        }


        /* Main content */
        .content {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 30px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-group span {
            color: #ff5fa2;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            outline: none;
        }

        textarea {
            width: 100%;
            min-height: 300px;
            padding: 12px;
        }

        .row {
            display: flex;
            gap: 20px;
            align-items: flex-end;

        }

        .upload {
            display: flex;
            gap: 10px;
        }

        /* Buttons */
        .btn-primary {
            background: #ff6fae;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-outline {
            background: white;
            border: 1.5px solid #ff6fae;
            color: #ff6fae;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        /* Footer */
        .note {
            background: #eef0f4;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
        }

        .tag-input {
            position: relative;
            width: 100%;
        }

        .tag-input input {
            width: 100%;
            padding: 10px;
        }

        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            display: none;
            z-index: 10;
        }

        .suggestions div {
            padding: 8px;
            cursor: pointer;
        }

        .suggestions div:hover {
            background: #f3f3f3;
        }

        #selected-tags {
            margin-top: 10px;
        }

        .tag {
            display: inline-block;
            background: #ff6fae;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            margin-right: 5px;
            font-size: 14px;
        }

        .tag span {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .form-truyen {
            flex: 1;
        }

        /* Tác giả nhỏ hơn */
        .form-tacgia {
            flex: 2;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            box-sizing: border-box;
        }
    </style>
    <script>
        function toggleGia() {
            var checkbox = document.getElementById('la_tra_phi');
            var giaInput = document.getElementById('gia');
            giaInput.disabled = !checkbox.checked;
        }
    </script>
</head>

<body>
    <div class="container">
        <?php include('test.php'); ?>

        <main class="content">
            <div class="content-header">
                <h2>Sửa chương <?= $chuong['so_chuong'] ?></h2>
                <button class="btn-outline">← Quay lại</button>
            </div>
            <form method="post">
                <div class="row">
                    <div class="form-group form-tacgia">
                        <label>Tiêu đề <span>*</span></label>
                        <input type="text" name="tieu_de" value="<?= htmlspecialchars($chuong['tieu_de']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Nội dung chương <span>*</span></label>
                    <textarea name="noi_dung"><?= htmlspecialchars($chuong['noi_dung']) ?></textarea>
                </div>

                <div class="row1">
                    <div class="form-group checkbox-group">
                        <label for="la_tra_phi" class="checkbox-label">
                            Chương trả phí
                            <input type="checkbox" name="la_tra_phi" id="la_tra_phi" <?= $chuong['la_tra_phi'] ? 'checked' : '' ?> onchange="toggleGia()">
                            <span class="checkmark"></span>
                        </label>
                    </div>


                    <div class="form-group">
                        <label>Giá (áp dụng nếu là chương trả phí)</label>
                        <input type="number" name="gia" id="gia" value="<?= htmlspecialchars($chuong['gia']) ?>" min="0"
                            <?= $chuong['la_tra_phi'] ? '' : 'disabled' ?>>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn-primary">Sửa chương</button>
                </div>
            </form>
        </main>
    </div>

    <script>toggleGia();</script>
    <script>
        const checkbox = document.getElementById('la_tra_phi');
        const giaInput = document.getElementById('gia');

        checkbox.addEventListener('change', function () {
            if (this.checked) {
                giaInput.disabled = false;
            } else {
                giaInput.value = 0;
                giaInput.disabled = true;
            }
        });
    </script>

</body>

</html>