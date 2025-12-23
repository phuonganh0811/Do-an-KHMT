<?php
// =======================
// BẬT LỖI (DEBUG)
// =======================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =======================
// KẾT NỐI + AUTH
// =======================
require 'connect.php';
require 'auth.php';
require_login();

// =======================
// KIỂM TRA DỮ LIỆU BẮT BUỘC
// =======================
if (
    !isset($_POST['ten_truyen']) ||
    !isset($_POST['id_truyen'])
) {
    die("❌ Thiếu dữ liệu bắt buộc");
}

// =======================
// LẤY DỮ LIỆU
// =======================
$id_truyen = (int) $_POST['id_truyen'];
$ten_truyen = trim($_POST['ten_truyen']);
$tom_tat = trim($_POST['tom_tat'] ?? '');
$gia_truyen = (int) ($_POST['gia_truyen'] ?? 0);
$trang_thai = $_POST['trang_thai'] ?? 'dang_ra';
$anh_bia = $_POST['anh_bia_cu'] ?? null;

// =======================
// XỬ LÝ THỂ LOẠI
// =======================
$the_loai_ids = $_POST['the_loai_ids'] ?? '';
$the_loai_arr = array_filter(array_map('intval', explode(',', $the_loai_ids)));

// =======================
// TẠO SLUG
// =======================
function slugify($text)
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^a-z0-9à-ỹ\s-]/u', '', $text);
    $text = preg_replace('/\s+/', '-', trim($text));
    return $text;
}
$slug = slugify($ten_truyen);

// =======================
// XỬ LÝ ẢNH BÌA (SỬA TRUYỆN)
// =======================

// Mặc định giữ ảnh cũ
$anh_bia = $_POST['anh_bia_cu'] ?? null;

// Nếu có chọn ảnh mới
if (isset($_FILES['anh_bia']) && $_FILES['anh_bia']['error'] === UPLOAD_ERR_OK) {

    $folder = "uploads/cover/";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES['anh_bia']['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allow)) {
        die("❌ Định dạng ảnh không hợp lệ");
    }

    $fileName = uniqid('cover_') . '.' . $ext;
    $path = $folder . $fileName;

    if (!move_uploaded_file($_FILES['anh_bia']['tmp_name'], $path)) {
        die("❌ Không thể lưu ảnh");
    }

    // (Tùy chọn) Xoá ảnh cũ nếu tồn tại
    if (!empty($_POST['anh_bia_cu']) && file_exists($folder . basename($_POST['anh_bia_cu']))) {
        @unlink($folder . basename($_POST['anh_bia_cu']));
    }

    // **Lưu full path vào DB**
    $anh_bia = $folder . $fileName; // <- Đây là điểm quan trọng
}



// =======================
// UPDATE TRUYỆN
// =======================
$sql = "
    UPDATE truyen
    SET 
        ten_truyen = ?,
        tom_tat = ?,
        gia_truyen = ?,
        trang_thai = ?,
        slug = ?,
        anh_bia = ?
    WHERE id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssisssi",
    $ten_truyen,
    $tom_tat,
    $gia_truyen,
    $trang_thai,
    $slug,
    $anh_bia,
    $id_truyen
);
$stmt->execute();

// =======================
// UPDATE THỂ LOẠI
// =======================

// Xóa thể loại cũ
$stmt = $conn->prepare("DELETE FROM truyen_the_loai WHERE id_truyen = ?");
$stmt->bind_param("i", $id_truyen);
$stmt->execute();

// Thêm thể loại mới
if (!empty($the_loai_arr)) {
    $stmt = $conn->prepare("
        INSERT INTO truyen_the_loai (id_truyen, id_the_loai)
        VALUES (?, ?)
    ");
    foreach ($the_loai_arr as $id_tl) {
        $stmt->bind_param("ii", $id_truyen, $id_tl);
        $stmt->execute();
    }
}

// =======================
// HOÀN TẤT
// =======================
header("Location: sua_truyen_form.php?id=" . $id_truyen . "&msg=cap_nhat_thanh_cong");
exit;
