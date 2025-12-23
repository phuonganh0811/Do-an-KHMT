<?php include "menu.php"; ?>
<?php
session_start();
require 'connect.php';
require 'auth.php';
require_login();

/* Lấy thể loại */
$theLoai = [];
$rs = mysqli_query($conn, "SELECT id, ten_the_loai FROM the_loai ORDER BY ten_the_loai");
while ($row = mysqli_fetch_assoc($rs)) {
    $theLoai[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đăng Truyện</title>
    <link rel="stylesheet" href="dang-truyen.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            background: #f7f8fc;
        }
        

        /* Layout */
        .container {
            display: flex;
            gap: 24px;
            max-width: 1300px;
            margin: 0 auto;
            margin-top: 80px;

            /* margin-top: 30px;
            display: flex;
            gap: 30px;
            padding: 30px; */
        }


        /* Main content */
        .content {
            flex: 1;
            background: white;
            border-radius: 20px;
            padding: 30px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        /* Form */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .form-group span {
            color: #ff5fa2;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            outline: none;
        }

        textarea {
            height: 120px;
            resize: none;
        }

        .row {
            display: flex;
            gap: 20px;
            align-items: flex-end;

        }

        .upload {
            display: flex;
            gap: 10px;
        }

        /* Buttons */
        .btn-primary {
            background: #ff6fae;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        .btn-outline {
            background: white;
            border: 1.5px solid #ff6fae;
            color: #ff6fae;
            padding: 10px 18px;
            border-radius: 10px;
            cursor: pointer;
        }

        /* Footer */
        .note {
            background: #eef0f4;
            padding: 12px;
            border-radius: 10px;
            font-size: 14px;
            margin-top: 10px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
        }

        .tag-input {
            position: relative;
            width: 100%;
        }

        .tag-input input {
            width: 100%;
            padding: 10px;
        }

        .suggestions {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            width: 100%;
            max-height: 150px;
            overflow-y: auto;
            display: none;
            z-index: 10;
        }

        .suggestions div {
            padding: 8px;
            cursor: pointer;
        }

        .suggestions div:hover {
            background: #f3f3f3;
        }

        #selected-tags {
            margin-top: 10px;
        }

        .tag {
            display: inline-block;
            background: #ff6fae;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            margin-right: 5px;
            font-size: 14px;
        }

        .tag span {
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .form-truyen {
            flex: 2;
        }

        /* Tác giả nhỏ hơn */
        .form-tacgia {
            flex: 1;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>

    <div class="container">

        <?php include('test.php'); ?>

        <!-- MAIN CONTENT -->
        <main class="content">

            <div class="content-header">
                <h2>✏️ Đăng Truyện</h2>
                <button class="btn-outline">← Quay lại</button>
            </div>
            <form method="POST" action="dang_truyen.php" enctype="multipart/form-data" onsubmit="return validateForm()">

                <div class="row">
                    <div class="form-group form-truyen">
                        <label>Tên truyện <span>*</span></label>
                        <input type="text" name="ten_truyen" required>
                    </div>

                    <div class="form-group form-tacgia">
                        <label>Tác giả <span>*</span></label>
                        <input type="text" value="<?= $_SESSION['ten_hien_thi'] ?>" disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>Thể loại</label>

                    <div class="tag-input">
                        <input type="text" id="theloai-input" placeholder="Nhập thể loại...">
                        <div id="suggestions" class="suggestions"></div>
                    </div>

                    <div id="selected-tags"></div>

                    <!-- input ẩn gửi ID thể loại -->
                    <input type="hidden" name="the_loai_ids" id="the_loai_ids">
                </div>


                <div class="row">
                    <div class="form-group">
                        <label>Chế độ chia lương <span>*</span></label>
                        <select name="che_do_chia_luong" required>
                            <option value="khong_doc_quyen">Không độc quyền (70% cho tác giả)</option>
                            <option value="doc_quyen">Độc quyền (90% cho tác giả)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Giá truyện (đậu) <span>*</span></label>
                        <input type="number" name="gia_truyen" min="0" value="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ảnh bìa <span>*</span></label>

                    <div class="upload">
                        <input type="file" name="anh_bia" id="anh_bia" accept="image/*" hidden required>

                        <button type="button" class="btn-primary" onclick="document.getElementById('anh_bia').click()">
                            ⬆ Tải file
                        </button>

                        <span id="fileName" style="font-size:13px;color:#666"></span>
                    </div>
                </div>


                <div class="form-group">
                    <label>Mô tả <span>*</span></label>
                    <textarea name="tom_tat" required></textarea>
                </div>

                <div class="note">
                    ℹ️ Lưu ý: Hãy kiểm tra kỹ thông tin trước khi thao tác!
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-outline">Hủy bỏ</button>
                    <button type="submit" class="btn-primary">Đăng truyện</button>
                </div>

            </form>

        </main>
    </div>
    <script>
        document.getElementById('anh_bia').addEventListener('change', function () {
            document.getElementById('fileName').innerText = this.files[0]?.name || '';
        });

        function validateForm() {
            if (!document.getElementById('the_loai_ids').value) {
                alert("❌ Vui lòng chọn ít nhất 1 thể loại");
                return false;
            }
            return true;
        }
    </script>

    <script>
        const theLoai = <?= json_encode($theLoai) ?>;

        const input = document.getElementById('theloai-input');
        const suggestions = document.getElementById('suggestions');
        const selectedTags = document.getElementById('selected-tags');
        const hiddenInput = document.getElementById('the_loai_ids');

        let selected = [];

        input.addEventListener('input', () => {
            const key = input.value.toLowerCase();
            suggestions.innerHTML = '';
            if (!key) return suggestions.style.display = 'none';

            const matches = theLoai.filter(t =>
                t.ten_the_loai.toLowerCase().includes(key)
                && !selected.includes(t.id)
            );

            matches.forEach(t => {
                const div = document.createElement('div');
                div.textContent = t.ten_the_loai;
                div.onclick = () => addTag(t);
                suggestions.appendChild(div);
            });

            suggestions.style.display = matches.length ? 'block' : 'none';
        });

        function addTag(t) {
            selected.push(t.id);
            const tag = document.createElement('div');
            tag.className = 'tag';
            tag.innerHTML = t.ten_the_loai + '<span onclick="removeTag(' + t.id + ', this)">×</span>';
            selectedTags.appendChild(tag);
            updateHidden();
            input.value = '';
            suggestions.style.display = 'none';
        }

        function removeTag(id, el) {
            selected = selected.filter(x => x !== id);
            el.parentElement.remove();
            updateHidden();
        }

        function updateHidden() {
            hiddenInput.value = selected.join(',');
        }
    </script>
</body>

</html>