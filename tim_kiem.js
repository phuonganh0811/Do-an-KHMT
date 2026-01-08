const categoryBox = document.getElementById('categoryTags');
const storyGrid = document.getElementById('storyGrid');

let currentPage = 1;
let currentCategory = "";
const perPage = 30;
let keyword = "";

/* =========================
   TẠO TAG "TẤT CẢ"
========================= */
function renderAllTag() {
    categoryBox.innerHTML = ""; //Dòng này xóa toàn bộ tag cũ trước khi render lại

    const allTag = document.createElement('div'); //Tạo 1 thẻ <div> mới bằng JavaScript
    allTag.className = 'tag active'; //Gán class CSS tag activecho div
    allTag.innerText = 'Tất cả'; //Hiện tag tất cả

    allTag.addEventListener('click', () => {
        setActiveTag(allTag); //Bỏ active khỏi tag khác, Gắn active cho tag tất cả
        currentCategory = ""; //Xóa điều kiện lọc thể loại
        currentPage = 1; //reset phân trang về 1
        loadStories();
    });

    categoryBox.appendChild(allTag); //appendChild → đưa tag tất cả vào giao diện

}

/* =========================
   LOAD THỂ LOẠI
========================= */
// 🔹 Gửi request GET lên server, File get_the_loai.php sẽ: Truy vấn bảng the_loai, Trả về JSON danh sách thể loại
fetch('get_the_loai.php')
    .then(res => res.json()) //Chuyển response (dạng text) → object JS
    .then(data => { //danh sách thể loại từ database
        renderAllTag(); //Tạo tag tất cả

        data.forEach(tl => { //duyệt trong db tl
            const div = document.createElement('div'); //tạo div
            div.className = 'tag'; //css tag cho div
            div.innerText = tl.ten_the_loai; //hiện ra tên thể loại

            div.addEventListener('click', () => { //ấn vào tag
                setActiveTag(div); //active tag nay, bỏ active cho tag khác
                currentCategory = tl.ten_the_loai; // lọc thể loại = tên thể loại nay
                currentPage = 1; //load trang 1
                loadStories();
            });

            categoryBox.appendChild(div);//hiển thị tag tl duyệt từ db vào giao diện
        });
    });

/* =========================
   ACTIVE TAG
========================= */
function setActiveTag(tag) {
    //document.querySelectorAll('.tag') Lấy toàn bộ thẻ có class tag
    // forEach(t => t.classList.remove('active')) Lặp qua từng tag, Xóa class active khỏi tất cả
    document.querySelectorAll('.tag').forEach(t => t.classList.remove('active'));
    tag.classList.add('active'); //Gán active cho tag được click
}

/* =========================
   RÚT GỌN TÊN TRUYỆN
========================= */
function shortenTitle(title, maxLength = 15) {
    return title.length > maxLength
        ? title.substring(0, maxLength) + "..."
        : title;
}

/* =========================
   LOAD TRUYỆN
========================= */
function loadStories() {
    fetch('get_truyen.php?')
        .then(res => res.json())
        .then(data => {
            storyGrid.innerHTML = "";

            if (data.length === 0) {
                storyGrid.innerHTML = "<p>Không có truyện phù hợp</p>";
                return;
            }

            data.forEach(story => {
                const div = document.createElement("div");
                div.className = "story-card";
                div.innerHTML = `
                    <img src="${story.anh_bia || 'no-image.jpg'}">
                    <div class="story-info">
                        <div class="story-title">
                            ${shortenTitle(story.ten_truyen)}
                        </div>
                        <div class="story-meta">
                            ${story.ten_the_loai || ''}
                        </div>
                    </div>
                `;
                storyGrid.appendChild(div);
            });
        });
}

/* =========================
   TÌM KIẾM
========================= */
searchInput.addEventListener("input", e => {
    keyword = e.target.value.trim();
    currentPage = 1;
    loadStories();
});
loadStories();
