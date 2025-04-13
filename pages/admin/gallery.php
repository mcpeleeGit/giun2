<?php
$user = unserialize($_SESSION['user']);

// 갤러리 항목 목록을 가져옵니다.
$galleryItems = $galleryItems ?? [];
?>

<section>
    <h2>갤러리 관리</h2>
    <div class="register-button-div">
        <button id="openModal">등록</button>
    </div>

    <!-- 모달 창 -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="galleryForm" action="/admin/gallery/create" method="post" enctype="multipart/form-data">
                <label for="title">제목:</label>
                <input type="text" id="title" name="title" required>

                <label for="description">설명:</label>
                <textarea id="description" name="description" rows="4"></textarea>

                <label for="image">이미지:</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <label for="author">작성자:</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($user->name ?? '') ?>" readonly>

                <button type="submit">등록</button>
            </form>
        </div>
    </div>

    <div class="gallery">
        <?php foreach ($galleryItems as $item): ?>
            <div class="gallery-item">
                <img src="<?= htmlspecialchars($item->image_path) ?>" alt="<?= htmlspecialchars($item->title) ?>">
                <h3><?= htmlspecialchars($item->title) ?></h3>
                <p><?= htmlspecialchars($item->description) ?></p>
                <p><strong>작성자:</strong> <?= htmlspecialchars($item->author) ?></p>
                <p><strong>작성일:</strong> <?= htmlspecialchars($item->created_at) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
.gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.gallery-item {
    border: 1px solid #ddd;
    padding: 1rem;
    background: #fff;
    width: calc(33.333% - 1rem);
    box-sizing: border-box;
}

.gallery-item img {
    max-width: 100%;
    height: auto;
}

.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.register-button-div {
    text-align: right;
    margin-bottom: 10px;
}
</style>

<script>
document.getElementById('openModal').onclick = function() {
    document.getElementById('modal').style.display = 'block';
}

document.querySelector('.close').onclick = function() {
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('modal')) {
        document.getElementById('modal').style.display = 'none';
    }
}
</script>
