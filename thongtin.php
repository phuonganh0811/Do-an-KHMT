<?php
ob_start();
session_start();
require 'connect.php';
require 'auth.php';
require_login();


$id = $_SESSION['user_id'];

// Lấy thông tin người dùng
$stmt = $conn->prepare("
    SELECT ten_hien_thi, email, avatar 
    FROM nguoi_dung 
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Avatar mặc định
$avatar = $user['avatar'] ?: 'uploads/avatar/default.png';
?>

<?php
if (isset($_POST['update'])) {

    $ten_hien_thi = trim($_POST['ten_hien_thi']);
    $mat_khau = $_POST['mat_khau'];

    // Xử lý avatar
    if (!empty($_FILES['avatar']['name'])) {
        $dir = 'uploads/avatar/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_name = $id . '.' . $ext;
        $path = $dir . $file_name;

        move_uploaded_file($_FILES['avatar']['tmp_name'], $path);

        $stmt = $conn->prepare("
            UPDATE nguoi_dung 
            SET ten_hien_thi = ?, avatar = ?
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $ten_hien_thi, $path, $id);

    } else {
        $stmt = $conn->prepare("
            UPDATE nguoi_dung 
            SET ten_hien_thi = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $ten_hien_thi, $id);
    }


    $stmt->execute();

    header("Location: thongtin.php?updated=1");
    exit;

}
?>
<?php include "menu.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            background: #f7f8fc;
        }

        .btn-primary {
            background: #ff6fae;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        .container {
            display: flex;
            gap: 24px;
            max-width: 1300px;
            margin: 0 auto;
            margin-top: 130px;
        }

        .profile-main {
            flex: 1;
            background: #fff;
            border-radius: 20px;
            padding: 30px 40px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            max-height: 600px;
        }

        .profile-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .profile-form label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #6b7280;
        }

        .profile-form label span {
            color: red;
        }

        .profile-form input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .upload-row {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-upload {
            background: #f472b6;
            color: #fff;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .avatar-preview {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .note {
            background: #f3f4f6;
            padding: 14px 18px;
            border-radius: 10px;
            color: #6b7280;
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-cancel {
            background: #fff;
            border: 1px solid #f472b6;
            color: #f472b6;
            padding: 12px 22px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-save {
            background: #f472b6;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php include('test.php'); ?>

        <div class="profile-main">
            <h2 class="profile-title">Thông tin tài khoản</h2>

            <form class="profile-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Tên đầy đủ <span>*</span></label>
                    <input type="text" name="ten_hien_thi" value="<?= htmlspecialchars($user['ten_hien_thi']) ?>"
                        required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email <span>*</span></label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu <span>*</span></label>
                        <input type="password" value="••••••••" disabled>
                    </div>

                </div>

                <div class="form-group">
                    <label>Ảnh đại diện</label>
                    <div class="upload-row">
                        <input type="file" name="avatar" id="avatar" accept="image/*">
                        <img src="<?= htmlspecialchars($avatar) ?>" class="avatar-preview">
                        <button type="button" class="btn-primary1" onclick="document.getElementById('avatar').click()">
                            ⬆ Tải file
                        </button>

                    </div>
                </div>

                <div class="note">
                    ℹ Lưu ý: Hãy kiểm tra kỹ thông tin trước khi thao tác!
                </div>

                <div class="form-actions">
                    <button type="submit" name="update" class="btn-save">
                        Cập nhật thông tin
                    </button>
                </div>
            </form>


        </div>

    </div>
    <?php ob_end_flush(); ?>
</body>

</html>