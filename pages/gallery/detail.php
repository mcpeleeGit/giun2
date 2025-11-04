<?php
if (empty($galleryItem)) {
    echo "<section class=\"section\"><div class=\"container\"><p class=\"message message-error\">갤러리 항목을 찾을 수 없습니다.</p></div></section>";
    return;
}
$createdAt = $galleryItem->created_at ?? null;
$formattedDate = $createdAt ? date('Y.m.d H:i', strtotime($createdAt)) : '';
?>

<section class="section">
    <div class="container">
        <article class="surface-card gallery-detail">
            <figure class="gallery-detail__figure">
                <img src="<?= htmlspecialchars($galleryItem->image_path ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($galleryItem->title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </figure>
            <div class="gallery-detail__content">
                <header>
                    <h2><?= htmlspecialchars($galleryItem->title ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <dl class="meta">
                        <div>
                            <dt>작성자</dt>
                            <dd><?= htmlspecialchars($galleryItem->author ?? '알 수 없음', ENT_QUOTES, 'UTF-8'); ?></dd>
                        </div>
                        <?php if (!empty($formattedDate)): ?>
                            <div>
                                <dt>등록일</dt>
                                <dd><?= htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8'); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </header>
                <div class="gallery-detail__description">
                    <?= nl2br(htmlspecialchars($galleryItem->description ?? '', ENT_QUOTES, 'UTF-8')); ?>
                </div>
                <footer class="gallery-detail__actions">
                    <a class="btn btn-ghost" href="/gallery">목록으로 돌아가기</a>
                </footer>
            </div>
        </article>
    </div>
</section>

<style>
.gallery-detail {
    display: grid;
    gap: 2.5rem;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    padding: 2.5rem;
    border-radius: 20px;
    box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
}

.gallery-detail__figure {
    margin: 0;
}

.gallery-detail__figure img {
    width: 100%;
    border-radius: 18px;
    object-fit: cover;
}

.gallery-detail__content h2 {
    margin-top: 0;
    font-size: 2rem;
    color: #111827;
}

.gallery-detail__content header {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.gallery-detail__content .meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.75rem;
    margin: 0;
    color: #6b7280;
    font-size: 0.95rem;
}

.gallery-detail__content .meta dt {
    font-weight: 600;
    margin-bottom: 0.35rem;
    color: #374151;
}

.gallery-detail__content .meta dd {
    margin: 0;
}

.gallery-detail__description {
    line-height: 1.8;
    color: #1f2937;
    white-space: pre-wrap;
}

.gallery-detail__actions {
    margin-top: 2rem;
}

.gallery-detail__actions .btn {
    padding-inline: 1.5rem;
}

@media (max-width: 768px) {
    .gallery-detail {
        padding: 2rem;
        gap: 2rem;
    }

    .gallery-detail__content h2 {
        font-size: 1.75rem;
    }
}
</style>
