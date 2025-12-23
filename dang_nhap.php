<?php
session_start();
require 'connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['username']) || empty($_POST['mat_khau'])) {
        die("❌ Thiếu thông tin đăng nhập");
    }

    $username = trim($_POST['username']);
    $password = $_POST['mat_khau'];

    $stmt = $conn->prepare("
        SELECT id, ten_dang_nhap, email, mat_khau, ten_hien_thi, vai_tro, trang_thai
        FROM nguoi_dung
        WHERE ten_dang_nhap = ? OR email = ?
        LIMIT 1
    ");

    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        die("❌ Tài khoản không tồn tại");
    }

    if ($user['trang_thai'] != 1) {
        die("❌ Tài khoản đã bị khóa");
    }

    if (!password_verify($password, $user['mat_khau'])) {
        die("❌ Sai mật khẩu");
    }

    /* Lưu session */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
    $_SESSION['ten_hien_thi'] = $user['ten_hien_thi'];
    $_SESSION['vai_tro'] = $user['vai_tro'];
    $_SESSION["email"] = $user["email"];

    header("Location: trangchucopy.php");
    exit;
}
?>

<form method="POST">
    <h2>🔐 Đăng nhập</h2>

    <input type="text" name="username" placeholder="Tên đăng nhập hoặc Email" required>
    <input type="password" name="mat_khau" placeholder="Mật khẩu" required>

    <button type="submit">Đăng nhập</button>
</form>