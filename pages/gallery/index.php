<?php
// 갤러리 항목 목록을 가져옵니다.
$galleryItems = $galleryItems ?? [];
?>

<section>
    <h2>썸네일 갤러리</h2>
    <div class='gallery'>
        <?php foreach ($galleryItems as $item): ?>
            <a class='card' href='/gallery/<?= $item->id; ?>'>
                <img src='<?= htmlspecialchars($item->image_path) ?>' alt='<?= htmlspecialchars($item->title) ?>'>
                <p><?= htmlspecialchars($item->title) ?></p>
            </a>
        <?php endforeach; ?>
        <?php if (empty($galleryItems)): ?>
            <p class="empty">아직 등록된 갤러리 이미지가 없습니다.</p>
        <?php endif; ?>
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
    border-radius: 12px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    text-decoration: none;
    color: inherit;
}

.card img {
    max-width: 100%;
    height: auto;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}

.empty {
    color: #6b7280;
}
</style>