<?php include "menu.php"; ?>
<?php
require 'connect.php';

// Lấy id truyện
$id_truyen = isset($_GET['id_truyen']) ? intval($_GET['id_truyen']) : 0;
if ($id_truyen <= 0) {
    header("Location: admin_truyen.php");
    exit;
}

// Lấy thông tin truyện
$stmt = $conn->prepare("SELECT ten_truyen FROM truyen WHERE id = ?");
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();
if (!$truyen)
    die("Truyện không tồn tại.");

$error = '';
$success = '';

function createSlug($string)
{
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_chuong = intval($_POST['so_chuong']);
    $tieu_de = trim($_POST['tieu_de']);
    $slug = trim($_POST['slug']);
    $noi_dung = trim($_POST['noi_dung']);
    $la_tra_phi = isset($_POST['la_tra_phi']) ? 1 : 0;
    $gia = $la_tra_phi ? intval($_POST['gia']) : 0;

    if ($so_chuong <= 0 || $tieu_de == '' || $noi_dung == '') {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        if ($slug == '')
            $slug = createSlug($tieu_de);

        $stmt_check = $conn->prepare("SELECT id FROM chuong_truyen WHERE slug = ? LIMIT 1");
        $stmt_check->bind_param("s", $slug);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Slug đã tồn tại, vui lòng chọn tiêu đề hoặc slug khác.";
        } else {
            $stmt_insert = $conn->prepare("INSERT INTO chuong_truyen (id_truyen, so_chuong, tieu_de, slug, noi_dung, la_tra_phi, gia) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("iisssii", $id_truyen, $so_chuong, $tieu_de, $slug, $noi_dung, $la_tra_phi, $gia);
            if ($stmt_insert->execute()) {
                $success = "Thêm chương thành công!";
                $_POST = [];
            } else {
                $error = "Lỗi khi thêm chương: " . $conn->error;
            }
        }
    }
}
?>

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
            margin-top: 110px;

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
            height: 120px;
            resize: none;
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

        textarea {
            width: 100%;
            min-height: 300px;
            padding: 12px;
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
                <h2>✏️ Thêm chương cho truyện: <?= htmlspecialchars($truyen['ten_truyen']) ?></h2>

            </div>
            <?php if ($error): ?>
                <p class="error"><?= $error ?></p><?php endif; ?>
            <?php if ($success): ?>
                <p class="success"><?= $success ?></p><?php endif; ?>
            <form method="post">
                <div class="row">
                    <div class="form-group form-truyen">
                        <label>Số chương <span>*</span></label>
                        <input type="number" name="so_chuong"
                            value="<?= isset($_POST['so_chuong']) ? htmlspecialchars($_POST['so_chuong']) : '' ?>"
                            required>
                    </div>

                    <div class="form-group form-tacgia">
                        <label>Tiêu đề <span>*</span></label>
                        <input type="text" name="tieu_de"
                            value="<?= isset($_POST['tieu_de']) ? htmlspecialchars($_POST['tieu_de']) : '' ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nội dung chương <span>*</span></label>
                    <textarea name="noi_dung" rows="15"
                        required><?= isset($_POST['noi_dung']) ? htmlspecialchars($_POST['noi_dung']) : '' ?></textarea>
                </div>

                <div class="row1">
                    <div class="form-group checkbox-group">
                        <label for="la_tra_phi" class="checkbox-label">
                            Chương trả phí
                            <input type="checkbox" name="la_tra_phi" id="la_tra_phi" <?= isset($_POST['la_tra_phi']) ? 'checked' : '' ?> onchange="toggleGia()">
                            <span class="checkmark"></span>
                        </label>
                    </div>


                    <div class="form-group">
                        <label>Giá (áp dụng nếu là chương trả phí)</label>
                        <input type="number" name="gia" id="gia"
                            value="<?= isset($_POST['gia']) ? htmlspecialchars($_POST['gia']) : '0' ?>" min="0"
                            <?= isset($_POST['la_tra_phi']) ? '' : 'disabled' ?>>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn-primary">Đăng chương</button>
                </div>
            </form>
        </main>
    </div>

    <script>toggleGia();</script>
</body>

</html>