<?php
$galleryItems = $galleryItems ?? [];
$currentUser = $currentUser ?? null;
$message = $message ?? null;
$error = $error ?? null;
$galleryCount = count($galleryItems);
?>

<section class="section">
    <div class="container">
        <div class="section-header section-header--with-meta">
            <div>
                <h2>썸네일 갤러리</h2>
                <p>회원들이 공유한 이미지를 함께 감상해 보세요.</p>
            </div>
            <div class="section-header__meta">
                <span class="tag">총 <?= number_format($galleryCount); ?>개 작품</span>
                <?php if (!empty($currentUser)): ?>
                    <a class="btn btn-ghost" href="#gallery-form">내 작품 올리기</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($currentUser)): ?>
            <form id="gallery-form" method="POST" action="/gallery" enctype="multipart/form-data" class="gallery-form surface-card">
                <header class="gallery-form__header">
                    <div>
                        <h3>내 이미지 공유하기</h3>
                        <p>하루의 기록처럼 작품을 남겨보세요. 썸네일과 함께 간단한 설명을 적으면 더욱 좋습니다.</p>
                    </div>
                </header>
                <?= csrf_field(); ?>
                <div class="form-grid">
                    <label class="form-field">
                        <span>이미지 제목</span>
                        <input id="gallery-title" type="text" name="title" placeholder="이미지 제목을 입력하세요" maxlength="120" required>
                    </label>
                    <label class="form-field form-field--full">
                        <span>설명</span>
                        <textarea id="gallery-description" name="description" rows="3" placeholder="작품에 대한 짧은 설명을 남겨 주세요." maxlength="1000"></textarea>
                    </label>
                    <div class="form-field form-field--full">
                        <span>이미지 파일</span>
                        <label class="file-upload">
                            <strong>이미지 파일을 선택해 주세요</strong>
                            <span>JPG, PNG, GIF, WEBP 형식 · 최대 5MB</span>
                            <input type="file" name="image" accept="image/*" required>
                        </label>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">갤러리에 등록</button>
                </div>
            </form>
        <?php else: ?>
            <div class="message message-info">
                갤러리에 이미지를 등록하려면 <a href="/login" class="link">로그인</a>이 필요합니다.
            </div>
        <?php endif; ?>

        <?php if (!empty($galleryItems)): ?>
            <div class="gallery-grid">
                <?php foreach ($galleryItems as $item): ?>
                    <?php
                        $rawDescription = $item->description ?? '';
                        if (function_exists('mb_strimwidth')) {
                            $excerpt = mb_strimwidth($rawDescription, 0, 120, '...', 'UTF-8');
                        } else {
                            $excerpt = strlen($rawDescription) > 60 ? substr($rawDescription, 0, 57) . '...' : $rawDescription;
                        }
                    ?>
                    <a class="gallery-card surface-card" href="/gallery/<?= $item->id; ?>" aria-label="<?= htmlspecialchars(($item->title ?? '갤러리 항목') . ' 자세히 보기', ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="gallery-card__thumb">
                            <img src="<?= htmlspecialchars($item->image_path ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item->title ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="gallery-card__body">
                            <h3><?= htmlspecialchars($item->title ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                            <?php if (!empty($rawDescription)): ?>
                                <p class="gallery-card__excerpt"><?= htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                        </div>
                        <footer class="gallery-card__footer">
                            <span class="meta">작성자 <?= htmlspecialchars($item->author ?? '알 수 없음', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="gallery-card__link">자세히 보기 →</span>
                        </footer>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="surface-card gallery-empty">
                <h3>아직 등록된 갤러리 이미지가 없습니다.</h3>
                <p>첫 번째 작품의 주인공이 되어 감성을 나눠보세요.</p>
                <a class="btn btn-primary" href="<?= !empty($currentUser) ? '#gallery-form' : '/login'; ?>">
                    <?= !empty($currentUser) ? '내 작품 올리기' : '로그인하고 시작하기'; ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.section-header--with-meta {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.section-header--with-meta h2 {
    margin-bottom: 0.35rem;
}

.section-header--with-meta p {
    margin: 0;
}

.section-header__meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-header__meta .btn {
    padding: 0.6rem 1.2rem;
}

.gallery-form {
    margin-bottom: 3rem;
    display: flex;
    flex-direction: column;
    gap: 1.75rem;
}

.gallery-form__header h3 {
    margin: 0 0 0.5rem;
    font-size: 1.5rem;
    color: #111827;
}

.gallery-form__header p {
    margin: 0;
    color: #6b7280;
    line-height: 1.6;
}

.gallery-form .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1.5rem;
}

.gallery-form .form-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-weight: 600;
    color: #1f2937;
}

.gallery-form .form-field span {
    font-size: 0.95rem;
}

.gallery-form .form-field input,
.gallery-form .form-field textarea {
    width: 100%;
    padding: 0.875rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.9rem;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.gallery-form .form-field input:focus,
.gallery-form .form-field textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    outline: none;
}

.gallery-form .form-field--full {
    grid-column: 1 / -1;
}

.gallery-form .file-upload {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 1.25rem;
    border: 2px dashed rgba(99, 102, 241, 0.35);
    border-radius: 1rem;
    color: #4f46e5;
    background: rgba(79, 70, 229, 0.06);
    text-align: center;
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.gallery-form .file-upload strong {
    font-size: 1rem;
}

.gallery-form .file-upload span {
    font-size: 0.85rem;
    color: #6366f1;
}

.gallery-form .file-upload input[type="file"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.gallery-form .file-upload:hover {
    background: rgba(79, 70, 229, 0.12);
    border-color: #4f46e5;
    transform: translateY(-2px);
}

.gallery-form .form-actions {
    display: flex;
    justify-content: flex-end;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.gallery-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    overflow: hidden;
    padding: 0;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.gallery-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 28px 55px rgba(15, 23, 42, 0.14);
}

.gallery-card__thumb {
    position: relative;
    padding-top: 66%;
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
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.gallery-card__body h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #111827;
}

.gallery-card__excerpt {
    margin: 0;
    color: #4b5563;
    line-height: 1.5;
    min-height: 3.2rem;
}

.gallery-card__footer {
    margin: 0;
    padding: 0 1.5rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 0.75rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.gallery-card__link {
    font-weight: 600;
    color: #4f46e5;
}

.gallery-empty {
    text-align: center;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    align-items: center;
}

.gallery-empty h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #111827;
}

.gallery-empty p {
    margin: 0;
    color: #6b7280;
}

@media (max-width: 768px) {
    .section-header__meta {
        width: 100%;
        justify-content: space-between;
    }

    .gallery-grid {
        gap: 1.5rem;
    }

    .gallery-card__excerpt {
        min-height: auto;
    }

    .gallery-form .form-actions {
        justify-content: stretch;
    }

    .gallery-form .form-actions .btn {
        width: 100%;
    }
}
</style>

