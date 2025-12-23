<?php
require 'connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['ten_dang_nhap']) ||
        empty($_POST['email']) ||
        empty($_POST['mat_khau'])
    ) {
        die("❌ Vui lòng nhập đầy đủ thông tin");
    }

    $ten_dang_nhap = trim($_POST['ten_dang_nhap']);
    $email         = trim($_POST['email']);
    $ten_hien_thi  = trim($_POST['ten_hien_thi'] ?? '');
    $mat_khau      = $_POST['mat_khau'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Email không hợp lệ");
    }

    if (strlen($mat_khau) < 6) {
        die("❌ Mật khẩu tối thiểu 6 ký tự");
    }

    $hash = password_hash($mat_khau, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("
        INSERT INTO nguoi_dung
        (ten_dang_nhap, email, mat_khau, ten_hien_thi)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->bind_param("ssss",
        $ten_dang_nhap,
        $email,
        $hash,
        $ten_hien_thi
    );

    try {
        $stmt->execute();
        header("Location: dang_nhap.php?success=1");
        exit;
    } catch (Exception $e) {
        die("❌ Tên đăng nhập hoặc email đã tồn tại");
    }
}
?>

<form method="POST">
    <h2>📝 Đăng ký</h2>

    <input type="text" name="ten_dang_nhap" placeholder="Tên đăng nhập" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="ten_hien_thi" placeholder="Tên hiển thị">
    <input type="password" name="mat_khau" placeholder="Mật khẩu" required>

    <button type="submit">Đăng ký</button>
</form>
