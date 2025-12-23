<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <style>
        /* Giống như trên */
        .banner {
            width: 100%;
            overflow: hidden;
            background: #f0f0f0;
            white-space: nowrap;
        }
        .banner-content {
            display: inline-block;
            animation: scroll 10s linear infinite;
        }
        @keyframes scroll {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
    </style>
</head>
<body>
    <div class="banner">
        <div class="banner-content">
            <?php echo htmlspecialchars($content); ?> <!-- Chèn dữ liệu từ DB -->
        </div>
    </div>
</body>
</html>
