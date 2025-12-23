<?php
session_start();
require 'connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id_truyen = (int) $data['id_truyen'];
$so_diem = (int) $data['so_diem'];
$id_user = $_SESSION['user_id'];

if ($so_diem <= 0) {
    echo json_encode(['success' => false, 'message' => 'Số điểm không hợp lệ']);
    exit;
}

$conn->begin_transaction();

try {
    // Lấy điểm người dùng
    $stmt = $conn->prepare("SELECT diem_de_cu FROM nguoi_dung WHERE id = ? FOR UPDATE");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user['diem_de_cu'] < $so_diem) {
        throw new Exception('Không đủ điểm đề cử');
    }

    // Trừ điểm người dùng
    $stmt = $conn->prepare("UPDATE nguoi_dung SET diem_de_cu = diem_de_cu - ? WHERE id = ?");
    $stmt->bind_param("ii", $so_diem, $id_user);
    $stmt->execute();

    // Cộng điểm truyện
    $stmt = $conn->prepare("UPDATE truyen SET diem_de_cu = diem_de_cu + ? WHERE id = ?");
    $stmt->bind_param("ii", $so_diem, $id_truyen);
    $stmt->execute();

    // Lấy tổng đề cử mới
    $stmt = $conn->prepare("SELECT diem_de_cu FROM truyen WHERE id = ?");
    $stmt->bind_param("i", $id_truyen);
    $stmt->execute();
    $truyen = $stmt->get_result()->fetch_assoc();

    $conn->commit();

    echo json_encode([
        'success' => true,
        'tong_de_cu' => $truyen['diem_de_cu']
    ]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
