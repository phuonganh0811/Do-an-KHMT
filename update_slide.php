<?php
require 'connect.php';
session_start();

/* ===== CHECK ADMIN ===== */
if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['success' => false, 'msg' => 'Chưa đăng nhập']));
}

$stmt = $conn->prepare("SELECT vai_tro FROM nguoi_dung WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || $user['vai_tro'] !== 'quan_tri') {
    exit(json_encode(['success' => false, 'msg' => 'Không có quyền']));
}

/* ===== DATA ===== */
$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$desc = trim($_POST['description'] ?? '');
$link = trim($_POST['link'] ?? '');

$imagePath = null;

/* ========= UPLOAD ẢNH BANNER ========= */
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $folder = 'uploads/banner/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allow)) {
        exit(json_encode(['success' => false, 'msg' => 'Ảnh không hợp lệ']));
    }

    $fileName = uniqid('banner_') . '.' . $ext;
    $path = $folder . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
        exit(json_encode(['success' => false, 'msg' => 'Không lưu được ảnh']));
    }

    $imagePath = $path;
}
$isNew = ($id === 0);

if ($isNew) {
    $stmt = $conn->prepare("
        INSERT INTO banner_slides (title, description, image, link, sort_order, is_active)
        VALUES (?, ?, ?, ?, 999, 1)
    ");
    $stmt->bind_param("ssss", $title, $desc, $imagePath, $link);
    $stmt->execute();

    echo json_encode(['success' => true, 'new' => true]);
    exit;
}

/* ===== UPDATE DB ===== */
if ($imagePath) {
    $sql = "UPDATE banner_slides 
            SET title=?, description=?, link=?, image=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $title, $desc, $link, $imagePath, $id);
} else {
    $sql = "UPDATE banner_slides 
            SET title=?, description=?, link=? 
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $desc, $link, $id);
}

$stmt->execute();

echo json_encode(['success' => true]);
