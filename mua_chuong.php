<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? 0;
$id_chuong = (int) ($_POST['id_chuong'] ?? 0);
$slug = $_POST['slug'] ?? '';

if (!$user_id || !$id_chuong) {
    die("Thiếu dữ liệu");
}

/* ===============================
   1. LẤY THÔNG TIN CHƯƠNG
================================ */
$stmt = $conn->prepare("
    SELECT 
        c.gia,
        c.la_tra_phi,
        t.id_tac_gia,
        t.che_do_chia_luong
    FROM chuong_truyen c
    JOIN truyen t ON c.id_truyen = t.id
    WHERE c.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id_chuong);
$stmt->execute();

$chuong = $stmt->get_result()->fetch_assoc();

if (!$chuong || (int) $chuong['la_tra_phi'] !== 1) {
    die("Chương không hợp lệ hoặc không phải chương trả phí");
}

$gia = (int) $chuong['gia'];
$id_tac_gia = (int) $chuong['id_tac_gia'];
$che_do = $chuong['che_do_chia_luong'] ?? 'khong_doc_quyen';

/* ===============================
   2. ĐÃ MUA CHƯA
================================ */
$stmt = $conn->prepare("
    SELECT id 
    FROM mua_chuong
    WHERE id_nguoi_dung = ? AND id_chuong = ?
    LIMIT 1
");
$stmt->bind_param("ii", $user_id, $id_chuong);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    header("Location: doc_chuong.php?slug=$slug");
    exit;
}

/* ===============================
   3. TÍNH TIỀN CHIA
================================ */
if ($che_do === 'doc_quyen') {
    $tien_tac_gia = (int) ($gia * 0.9);
} else {
    $tien_tac_gia = (int) ($gia * 0.7);
}

$tien_he_thong = $gia - $tien_tac_gia;

/* ===============================
   4. GIAO DỊCH AN TOÀN
================================ */
$conn->begin_transaction();

try {
    /* Khóa số dư người mua */
    $stmt = $conn->prepare("
        SELECT so_du 
        FROM nguoi_dung
        WHERE id = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || (int) $user['so_du'] < $gia) {
        throw new Exception("❌ Không đủ tiền, vui lòng nạp thêm");
    }

    /* Trừ tiền người mua */
    $stmt = $conn->prepare("
        UPDATE nguoi_dung
        SET so_du = so_du - ?
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $gia, $user_id);
    $stmt->execute();

    /* Cộng tiền cho tác giả */
    $stmt = $conn->prepare("
        UPDATE nguoi_dung
        SET so_du = so_du + ?
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $tien_tac_gia, $id_tac_gia);
    $stmt->execute();

    /* Lưu doanh thu hệ thống */
    $stmt = $conn->prepare("
        INSERT INTO doanh_thu_he_thong (id_chuong, so_tien)
        VALUES (?, ?)
    ");
    $stmt->bind_param("ii", $id_chuong, $tien_he_thong);
    $stmt->execute();

    /* Ghi lịch sử mua */
    $ma_gd = uniqid("GD_");

    $stmt = $conn->prepare("
        INSERT INTO mua_chuong
        (id_nguoi_dung, id_chuong, so_tien, ma_giao_dich)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiis", $user_id, $id_chuong, $gia, $ma_gd);
    $stmt->execute();

    /* (Tuỳ chọn) ghi log chia tiền */
    /*
    $stmt = $conn->prepare("
        INSERT INTO lich_su_chia_tien
        (id_chuong, id_tac_gia, so_tien, che_do)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iiis", $id_chuong, $id_tac_gia, $tien_tac_gia, $che_do);
    $stmt->execute();
    */

    $conn->commit();

    header("Location: doc_chuong.php?slug=$slug");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    die($e->getMessage());
}
