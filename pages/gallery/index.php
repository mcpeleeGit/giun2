<?php
// 갤러리 항목 목록을 가져옵니다.
$galleryItems = $galleryItems ?? [];
?>

<section>
    <h2>썸네일 갤러리</h2>
    <div class='gallery'>
        <?php foreach ($galleryItems as $item): ?>
            <div class='card'>
                <img src='<?= htmlspecialchars($item->image_path) ?>' alt='<?= htmlspecialchars($item->title) ?>'>
                <p><?= htmlspecialchars($item->title) ?></p>
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

.card {
    border: 1px solid #ddd;
    padding: 1rem;
    background: #fff;
    width: calc(33.333% - 1rem);
    box-sizing: border-box;
    text-align: center;
}

.card img {
    max-width: 100%;
    height: auto;
}
</style> 