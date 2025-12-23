<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connect.php';

$id = (int)$_GET['id'];

// Trừ tiền + cập nhật trạng thái
$conn->query("
    UPDATE nguoi_dung nd
    JOIN rut_tien r ON r.id_nguoi_dung = nd.id
    SET 
        nd.so_du = nd.so_du - r.so_tien,
        r.trang_thai = 'da_duyet'
    WHERE r.id = $id
      AND r.trang_thai = 'cho_duyet'
");

header("Location: admin_rut.php");
exit;
