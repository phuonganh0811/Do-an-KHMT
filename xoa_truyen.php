<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connect.php';
require 'auth.php';
require_login();

/* =========================
   1. Kiểm tra ID truyện
========================= */
if (!isset($_GET['id'])) {
    die("Thiếu ID truyện");
}

$id_truyen = (int)$_GET['id'];
$id_user   = (int)$_SESSION['user_id'];

/* =========================
   2. Kiểm tra quyền ADMIN
========================= */
$stmtRole = $conn->prepare("
    SELECT vai_tro 
    FROM nguoi_dung 
    WHERE id = ?
");
$stmtRole->bind_param("i", $id_user);
$stmtRole->execute();
$user = $stmtRole->get_result()->fetch_assoc();

if (!$user || $user['vai_tro'] !== 'quan_tri') {
    die("Bạn không có quyền xóa truyện");
}

/* =========================
   3. Kiểm tra truyện tồn tại
========================= */
$stmt = $conn->prepare("
    SELECT id 
    FROM truyen 
    WHERE id = ?
");
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$truyen = $stmt->get_result()->fetch_assoc();

if (!$truyen) {
    die("Truyện không tồn tại");
}

/* =========================
   4. Xóa truyện (TRANSACTION)
========================= */
$conn->begin_transaction();

try {
    // Xóa chương truyện
    $stmt = $conn->prepare("
        DELETE FROM chuong_truyen 
        WHERE id_truyen = ?
    ");
    $stmt->bind_param("i", $id_truyen);
    $stmt->execute();

    // Xóa thể loại truyện
    $stmt = $conn->prepare("
        DELETE FROM truyen_the_loai 
        WHERE id_truyen = ?
    ");
    $stmt->bind_param("i", $id_truyen);
    $stmt->execute();

    // Xóa truyện
    $stmt = $conn->prepare("
        DELETE FROM truyen 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id_truyen);
    $stmt->execute();

    $conn->commit();

    header("Location: quanlytruyenadmin.php?deleted=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die("Lỗi khi xóa truyện: " . $e->getMessage());
}
?>