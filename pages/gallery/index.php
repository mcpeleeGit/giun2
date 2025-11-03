<?php
$galleryItems = $galleryItems ?? [];
$currentUser = $currentUser ?? null;
$message = $message ?? null;
$error = $error ?? null;
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>썸네일 갤러리</h2>
            <p>회원들이 공유한 이미지를 함께 감상해 보세요.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($currentUser)): ?>
            <form method="POST" action="/gallery" enctype="multipart/form-data" class="gallery-form card">
                <h3>내 이미지 공유하기</h3>
                <?= csrf_field(); ?>
                <label class="sr-only" for="gallery-title">이미지 제목</label>
                <input id="gallery-title" type="text" name="title" placeholder="이미지 제목" maxlength="120" required>
                <label class="sr-only" for="gallery-description">설명</label>
                <textarea id="gallery-description" name="description" rows="3" placeholder="짧은 설명을 남겨 보세요." maxlength="1000"></textarea>
                <label class="file-upload">
                    <span>이미지 파일 선택 (JPG, PNG, GIF, WEBP)</span>
                    <input type="file" name="image" accept="image/*" required>
                </label>
                <button type="submit" class="btn btn-primary">갤러리에 등록</button>
            </form>
        <?php else: ?>
            <div class="message message-info">
                갤러리에 이미지를 등록하려면 <a href="/login" class="link">로그인</a>이 필요합니다.
            </div>
        <?php endif; ?>

        <div class="gallery-grid">
            <?php foreach ($galleryItems as $item): ?>
                <a class="gallery-card" href="/gallery/<?= $item->id; ?>">
                    <div class="gallery-card__thumb">
                        <img src="<?= htmlspecialchars($item->image_path ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item->title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="gallery-card__body">
                        <h3><?= htmlspecialchars($item->title ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="meta">작성자 <?= htmlspecialchars($item->author ?? '알 수 없음', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($galleryItems)): ?>
            <p class="message message-info">아직 등록된 갤러리 이미지가 없습니다. 첫 번째 작품의 주인공이 되어 주세요!</p>
        <?php endif; ?>
    </div>
</section>

<style>
.gallery-form {
    margin-bottom: 2.5rem;
    display: grid;
    gap: 1rem;
    padding: 1.5rem;
}

.gallery-form h3 {
    margin: 0;
}

.gallery-form input[type="text"],
.gallery-form textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 12px;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.gallery-form input[type="text"]:focus,
.gallery-form textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
    outline: none;
}

.file-upload {
    position: relative;
    display: inline-block;
    padding: 0.875rem 1rem;
    border: 2px dashed #cbd5f5;
    border-radius: 12px;
    color: #4f46e5;
    background: #eef2ff;
    text-align: center;
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease;
}

.file-upload:hover {
    background: #e0e7ff;
    border-color: #6366f1;
}

.file-upload input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
}

.gallery-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.gallery-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
}

.gallery-card__thumb {
    position: relative;
    padding-top: 65%;
    background: #f1f5f9;
}

.gallery-card__thumb img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-card__body {
    padding: 1.25rem 1.5rem 1.5rem;
}

.gallery-card__body h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    color: #111827;
}

.gallery-card__body .meta {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .gallery-form {
        padding: 1.25rem;
    }

    .gallery-grid {
        gap: 1rem;
    }
}
</style>

