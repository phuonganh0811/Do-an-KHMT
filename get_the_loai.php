<?php
require 'connect.php';

$sql = "SELECT id, ten_the_loai FROM the_loai ORDER BY ten_the_loai";
$result = $conn->query($sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode($categories);
