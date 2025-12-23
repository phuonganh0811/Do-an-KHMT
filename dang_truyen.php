
<?php
session_start();
require 'connect.php';
require 'auth.php';
require_login();

/* Hiển thị lỗi MySQL để debug */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* ========= 0. KIỂM TRA ĐĂNG NHẬP ========= */
if (!isset($_SESSION['user_id'])) {
    die("❌ Bạn chưa đăng nhập");
}

/* Ai đăng nhập cũng là tác giả */
$id_tac_gia = (int) $_SESSION['user_id'];

/* ========= 1. VALIDATE DỮ LIỆU ========= */
if (
    empty($_POST['ten_truyen']) ||
    empty($_POST['tom_tat']) ||
    empty($_POST['che_do_chia_luong'])
) {
    die("❌ Thiếu dữ liệu bắt buộc");
}

$ten_truyen = trim($_POST['ten_truyen']);
$tom_tat    = trim($_POST['tom_tat']);
$gia_truyen = isset($_POST['gia_truyen']) ? (int) $_POST['gia_truyen'] : 0;
$che_do     = $_POST['che_do_chia_luong'];

/* ========= 2. TẠO SLUG ========= */
function slugify($str)
{
    $str = trim($str);
    if ($str === '') return '';

    $map = [
        'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a',
        'â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
        'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a',
        'è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
        'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e',
        'ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
        'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o',
        'ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
        'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o',
        'ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
        'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u',
        'ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y',
        'đ'=>'d',
        'À'=>'a','Á'=>'a','Ạ'=>'a','Ả'=>'a','Ã'=>'a',
        'Â'=>'a','Ầ'=>'a','Ấ'=>'a','Ậ'=>'a','Ẩ'=>'a','Ẫ'=>'a',
        'Ă'=>'a','Ằ'=>'a','Ắ'=>'a','Ặ'=>'a','Ẳ'=>'a','Ẵ'=>'a',
        'È'=>'e','É'=>'e','Ẹ'=>'e','Ẻ'=>'e','Ẽ'=>'e',
        'Ê'=>'e','Ề'=>'e','Ế'=>'e','Ệ'=>'e','Ể'=>'e','Ễ'=>'e',
        'Ì'=>'i','Í'=>'i','Ị'=>'i','Ỉ'=>'i','Ĩ'=>'i',
        'Ò'=>'o','Ó'=>'o','Ọ'=>'o','Ỏ'=>'o','Õ'=>'o',
        'Ô'=>'o','Ồ'=>'o','Ố'=>'o','Ộ'=>'o','Ổ'=>'o','Ỗ'=>'o',
        'Ơ'=>'o','Ờ'=>'o','Ớ'=>'o','Ợ'=>'o','Ở'=>'o','Ỡ'=>'o',
        'Ù'=>'u','Ú'=>'u','Ụ'=>'u','Ủ'=>'u','Ũ'=>'u',
        'Ư'=>'u','Ừ'=>'u','Ứ'=>'u','Ự'=>'u','Ử'=>'u','Ữ'=>'u',
        'Ỳ'=>'y','Ý'=>'y','Ỵ'=>'y','Ỷ'=>'y','Ỹ'=>'y',
        'Đ'=>'d'
    ];

    $str = strtr($str, $map);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9]+/', '-', $str);
    return trim($str, '-');
}


$slug = slugify($ten_truyen);

if ($slug === '') {
    die("❌ Không thể tạo slug từ tên truyện");
}

/* ===== ĐẢM BẢO SLUG KHÔNG TRÙNG ===== */
$baseSlug = $slug;
$i = 1;

$stmtCheck = $conn->prepare("SELECT COUNT(*) FROM truyen WHERE slug = ?");
while (true) {
    $stmtCheck->bind_param("s", $slug);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->free_result();

    if ($count == 0) break;

    $slug = $baseSlug . '-' . $i;
    $i++;
}

/* ========= 3. UPLOAD ẢNH BÌA ========= */
if (!isset($_FILES['anh_bia']) || $_FILES['anh_bia']['error'] !== 0) {
    die("❌ Lỗi upload ảnh");
}

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

/* ========= 4. INSERT TRUYỆN + THỂ LOẠI ========= */
$conn->begin_transaction();

try {

    /* 4.1 Insert truyện */
    $stmt = $conn->prepare("
        INSERT INTO truyen
        (ten_truyen, tom_tat, anh_bia, slug, gia_truyen, che_do_chia_luong, id_tac_gia)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssisi",
        $ten_truyen,
        $tom_tat,
        $path,
        $slug,
        $gia_truyen,
        $che_do,
        $id_tac_gia
    );

    $stmt->execute();
    $id_truyen = $stmt->insert_id;

    /* 4.2 Insert thể loại */
    $theLoaiArr = array_filter(explode(',', $_POST['the_loai_ids']));

    $stmtTL = $conn->prepare("
        INSERT INTO truyen_the_loai (id_truyen, id_the_loai)
        VALUES (?, ?)
    ");

    foreach ($theLoaiArr as $idTL) {
        $idTL = (int) $idTL;
        $stmtTL->bind_param("ii", $id_truyen, $idTL);
        $stmtTL->execute();
    }

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    die("❌ Lỗi đăng truyện: " . $e->getMessage());
}

/* ========= 5. HOÀN TẤT ========= */
header("Location: truyen.php?slug=" . urlencode($slug));
exit;
