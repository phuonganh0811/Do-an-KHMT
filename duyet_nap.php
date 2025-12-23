<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'connect.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('ID không hợp lệ');
}

/* Gói nạp */
$goi_nap = [
    10000 => ['dau' => 8000,  'hoa' => 8000],
    20000 => ['dau' => 16000, 'hoa' => 16000],
    50000 => ['dau' => 40000, 'hoa' => 40000],
    100000 => ['dau' => 85000, 'hoa' => 85000],
    200000 => ['dau' => 170000,'hoa' => 170000],
    500000 => ['dau' => 450000,'hoa' => 450000],
    1000000 => ['dau' => 900000,'hoa' => 900000],
];

$conn->begin_transaction();

try {
    /* Khóa bản ghi nạp */
    $stmt = $conn->prepare("
        SELECT id_user, so_tien, trang_thai
        FROM nap_tien
        WHERE id = ? FOR UPDATE
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $nap = $stmt->get_result()->fetch_assoc();

    if (!$nap) {
        throw new Exception('Không tồn tại yêu cầu nạp');
    }

    if ($nap['trang_thai'] !== 'cho_duyet') {
        throw new Exception('Yêu cầu đã được xử lý');
    }

    if (!isset($goi_nap[$nap['so_tien']])) {
        throw new Exception('Gói nạp không hợp lệ');
    }

    $dau  = $goi_nap[$nap['so_tien']]['dau'];
    $hoa  = $goi_nap[$nap['so_tien']]['hoa'];
    $doanh_thu = $nap['so_tien'] - $dau; // phần hệ thống giữ

    /* 1️⃣ Cộng dâu + hoa cho user */
    $stmt = $conn->prepare("
        UPDATE nguoi_dung
        SET so_du = so_du + ?, diem_de_cu = diem_de_cu + ?
        WHERE id = ?
    ");
    $stmt->bind_param("iii", $dau, $hoa, $nap['id_user']);
    $stmt->execute();

    /* 2️⃣ Ghi doanh thu hệ thống */
    $stmt = $conn->prepare("
        INSERT INTO doanh_thu_he_thong (id_chuong, so_tien)
        VALUES (0, ?)
    ");
    $stmt->bind_param("i", $doanh_thu);
    $stmt->execute();

    /* 3️⃣ Đánh dấu đã duyệt */
    $stmt = $conn->prepare("
        UPDATE nap_tien
        SET trang_thai = 'da_duyet'
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $conn->commit();
    header("Location: admin_nap.php?success=1");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Lỗi: " . $e->getMessage();
}
