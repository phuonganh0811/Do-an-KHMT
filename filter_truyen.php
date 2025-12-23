<?php
require 'connect.php';

$tags = $_POST['tags'] ?? [];
$keyword = $_POST['keyword'] ?? '';

$sql = "SELECT DISTINCT t.* FROM truyen t ";
$params = [];

if (!empty($tags)) {
    $placeholders = implode(',', array_fill(0, count($tags), '?'));

    $sql .= "
        JOIN truyen_the_loai ttl ON t.id = ttl.id_truyen
        JOIN the_loai tl ON tl.id = ttl.id_the_loai
        WHERE tl.slug IN ($placeholders)
    ";

    $params = array_merge($params, $tags);

    if ($keyword) {
        $sql .= " AND t.ten_truyen LIKE ? ";
        $params[] = "%$keyword%";
    }

    $sql .= " GROUP BY t.id HAVING COUNT(DISTINCT tl.slug) = " . count($tags);
} else {
    if ($keyword) {
        $sql .= " WHERE t.ten_truyen LIKE ? ";
        $params[] = "%$keyword%";
    }
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
