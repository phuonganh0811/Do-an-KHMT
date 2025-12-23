<?php include "menu.php"; ?>
<?php require 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tìm kiếm truyện</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #333;
        }

        .container {
            max-width: 1280px;
            margin: 150px auto;
            padding: 0 50px;
        }

        /* Header */
        .search-header {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }

        .search-header h2 {
            font-size: 26px;
            color: #e85ba2;
            margin-bottom: 5px;
        }

        .search-header p {
            font-size: 15px;
            color: #555;
        }

        .search-box {
            position: relative;
            margin-top: 10px;
        }

        .search-box input {
            width: 320px;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 15px;
            outline: none;
            transition: 0.3s;
        }

        .search-box input:focus {
            border-color: #ff80bf;
            box-shadow: 0 0 6px rgba(255, 128, 191, 0.4);
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        /* Tags */
        .category-title {
            display: flex;
            align-items: center;
            color: #e85ba2;
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .category-title i {
            margin-right: 8px;
        }

        .category-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
        }

        .tag {
            background: #f2f2f5;
            padding: 8px 18px;
            border-radius: 30px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .tag.active {
            background: #ff80bf;
            color: #fff;
            font-weight: 600;
        }

        .tag:hover {
            background: #ffb2d6;
            color: #fff;
        }

        /* Story cards */
        .story-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            /* mỗi hàng 6 truyện */
            gap: 20px;
        }

        .story-card {
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .story-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .story-card img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .story-info {
            padding: 10px 12px 15px;
        }

        .story-title {
            font-size: 16px;
            font-weight: 600;
            margin-top: 5px;
        }

        .story-meta {
            font-size: 13px;
            color: #777;
            margin-top: 4px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .page-btn {
            padding: 8px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            background: #fff;
            transition: 0.2s;
        }

        .page-btn:hover {
            background: #ffe3f1;
            border-color: #ff80bf;
        }

        .page-btn.active {
            background: #ff80bf;
            color: #fff;
            border-color: #ff80bf;
        }

        .page-btn.disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        @media (max-width: 1200px) {
            .story-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .story-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-header">
            <div>
                <h2>Tìm kiếm truyện</h2>
                <p>Khám phá truyện mới nhất cùng chúng tôi!</p>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Tên truyện...">
                <i>🔍</i>
            </div>
        </div>

        <div class="category-title"><i><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="lucide lucide-tag">
                    <path
                        d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z">
                    </path>
                    <circle cx="7.5" cy="7.5" r=".5" fill="currentColor"></circle>
                </svg></i> Thể loại</div>
                <div class="category-tags" id="categoryTags">
        <div class="tag active" data-id="categoryTags"></div>
    </div>

        <div class="story-grid" id="storyGrid"></div>
        <div class="pagination" id="pagination"></div>
    </div>

    <script src="tim_kiem.js"></script>
</body>

</html>