<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
require 'auth.php';
require_login();

/* 1️⃣ Kiểm tra id chương */
if (!isset($_GET['id'])) {
    die("Thiếu ID chương");
}

$id_chuong = (int) $_GET['id'];
$id_user = $_SESSION['user_id'];

/* 2️⃣ Kiểm tra chương có tồn tại + có quyền xóa không */
$sql = "
    SELECT c.id, c.id_truyen
    FROM chuong_truyen c
    JOIN truyen t ON c.id_truyen = t.id
    WHERE c.id = ? AND t.id_tac_gia = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_chuong, $id_user);
$stmt->execute();
$result = $stmt->get_result();
$chuong = $result->fetch_assoc();

if (!$chuong) {
    die("Chương không tồn tại hoặc bạn không có quyền xóa");
}

/* 3️⃣ Xóa chương */
$sql = "DELETE FROM chuong_truyen WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_chuong);
$stmt->execute();

/* 4️⃣ Quay về trang quản lý chương */
header("Location: quan_ly_chuong.php?id_truyen=" . $chuong['id_truyen']);
exit;
