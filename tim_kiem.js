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
    categoryBox.innerHTML = "";

    const allTag = document.createElement('div');
    allTag.className = 'tag active';
    allTag.innerText = 'Tất cả';

    allTag.addEventListener('click', () => {
        setActiveTag(allTag);
        currentCategory = "";
        currentPage = 1;
        loadStories();
    });

    categoryBox.appendChild(allTag);
}

/* =========================
   LOAD THỂ LOẠI
========================= */
fetch('get_the_loai.php')
    .then(res => res.json())
    .then(data => {
        renderAllTag();

        data.forEach(tl => {
            const div = document.createElement('div');
            div.className = 'tag';
            div.innerText = tl.ten_the_loai;

            div.addEventListener('click', () => {
                setActiveTag(div);
                currentCategory = tl.ten_the_loai;
                currentPage = 1;
                loadStories();
            });

            categoryBox.appendChild(div);
        });
    });

/* =========================
   ACTIVE TAG
========================= */
function setActiveTag(tag) {
    document.querySelectorAll('.tag').forEach(t => t.classList.remove('active'));
    tag.classList.add('active');
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
function shortenTitle(title, maxLength = 15) {
    return title.length > maxLength
        ? title.substring(0, maxLength) + "..."
        : title;
}

/* =========================
   LOAD TRUYỆN
========================= */
function loadStories() {
    let params = new URLSearchParams();
    params.append('page', currentPage);

    if (currentCategory) {
        params.append('the_loai', currentCategory);
    }

    if (keyword) {
        params.append('keyword', keyword);
    }

    fetch('get_truyen.php?' + params.toString())
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
