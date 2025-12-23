<?php

function getTruyenMoiCapNhat($conn, $limit = 16) {
    $sql = "SELECT id, ten_truyen, anh_bia, ngay_cap_nhat, slug
            FROM truyen 
            ORDER BY ngay_cap_nhat DESC 
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();

    return $stmt->get_result();
}
