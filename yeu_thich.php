<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn chưa đăng nhập'
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id_truyen = (int)$data['id_truyen'];
$id_user   = $_SESSION['user_id'];

/* Kiểm tra đã yêu thích chưa */
$sqlCheck = "SELECT id FROM truyen_yeu_thich 
             WHERE id_nguoi_dung = ? AND id_truyen = ?";
$stmt = $conn->prepare($sqlCheck);
$stmt->bind_param("ii", $id_user, $id_truyen);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // 👉 ĐÃ YÊU THÍCH → XÓA
    $sqlDel = "DELETE FROM truyen_yeu_thich 
               WHERE id_nguoi_dung = ? AND id_truyen = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("ii", $id_user, $id_truyen);
    $stmtDel->execute();

    echo json_encode([
        'success' => true,
        'favorited' => false
    ]);
} else {
    // 👉 CHƯA YÊU THÍCH → THÊM
    $sqlIns = "INSERT INTO truyen_yeu_thich (id_nguoi_dung, id_truyen)
               VALUES (?, ?)";
    $stmtIns = $conn->prepare($sqlIns);
    $stmtIns->bind_param("ii", $id_user, $id_truyen);
    $stmtIns->execute();

    echo json_encode([
        'success' => true,
        'favorited' => true
    ]);
}
