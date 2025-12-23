<?php
require 'connect.php';

header('Content-Type: application/json; charset=utf-8');

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 30;
$offset = ($page - 1) * $limit;

$where = [];
$params = [];
$types = "";

/* Lọc theo thể loại */
if (!empty($_GET['the_loai'])) {
    $where[] = "tl.ten_the_loai = ?";
    $params[] = $_GET['the_loai'];
    $types .= "s";
}

/* Tìm theo tên truyện */
if (!empty($_GET['keyword'])) {
    $where[] = "t.ten_truyen LIKE ?";
    $params[] = '%' . $_GET['keyword'] . '%';
    $types .= "s";
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "
SELECT 
    t.id,
    t.ten_truyen,
    t.anh_bia,
    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS ten_the_loai
FROM truyen t
LEFT JOIN truyen_the_loai ttl ON t.id = ttl.id_truyen
LEFT JOIN the_loai tl ON ttl.id_the_loai = tl.id
$whereSql
GROUP BY t.id
ORDER BY t.ngay_cap_nhat DESC
LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
