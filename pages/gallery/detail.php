<?php
if (empty($galleryItem)) {
    echo "<section class=\"section\"><div class=\"container\"><p class=\"message message-error\">갤러리 항목을 찾을 수 없습니다.</p></div></section>";
    return;
}
?>

<section class="section">
    <div class="container">
        <article class="surface-card gallery-detail">
            <img src="<?= htmlspecialchars($galleryItem->image_path ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($galleryItem->title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="gallery-detail__content">
                <h2><?= htmlspecialchars($galleryItem->title ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="meta">작성자 <?= htmlspecialchars($galleryItem->author ?? '', ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($galleryItem->created_at ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                <p><?= nl2br(htmlspecialchars($galleryItem->description ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                <a class="btn btn-ghost" href="/gallery">목록으로 돌아가기</a>
            </div>
        </article>
    </div>
</section>

<style>
.gallery-detail {
    display: grid;
    gap: 2rem;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    padding: 2rem;
    border-radius: 18px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}

.gallery-detail img {
    width: 100%;
    border-radius: 16px;
    object-fit: cover;
}

.gallery-detail__content h2 {
    margin-top: 0;
    font-size: 2rem;
    color: #111827;
}

.gallery-detail__content .meta {
    color: #6b7280;
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
}

.gallery-detail__content p {
    line-height: 1.7;
    color: #1f2937;
}
</style>
