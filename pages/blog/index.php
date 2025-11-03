<?php
$currentUser = $currentUser ?? null;
$posts = $posts ?? [];
$message = $message ?? null;
$error = $error ?? null;
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?= htmlspecialchars(($currentUser->name ?? '나의') . ' 블로그', ENT_QUOTES, 'UTF-8'); ?></h2>
            <p>하루의 생각과 기록을 남겨 보세요. 나만 볼 수 있는 공간입니다.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($currentUser)): ?>
            <div class="message message-info">블로그 관리는 로그인 후 이용할 수 있습니다. <a href="/login" class="link">로그인하기</a></div>
        <?php else: ?>
            <form method="POST" action="/blog" class="blog-form">
                <?= csrf_field(); ?>
                <div class="form-grid">
                    <label class="form-field">
                        <span>제목</span>
                        <input type="text" name="title" placeholder="글 제목을 입력해 주세요" required>
                    </label>
                    <label class="form-field form-field--full">
                        <span>내용</span>
                        <textarea name="content" rows="4" placeholder="오늘의 생각을 기록해 보세요." required></textarea>
                    </label>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">기록하기</button>
                </div>
            </form>

            <div class="blog-list">
                <?php foreach ($posts as $post): ?>
                    <?php $editTarget = 'post-' . $post->id; ?>
                    <article class="blog-entry">
                        <header class="blog-entry__header">
                            <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                            <span class="blog-entry__meta">작성일 <?= htmlspecialchars(date('Y.m.d H:i', strtotime($post->created_at ?? 'now'))); ?></span>
                        </header>
                        <div class="blog-entry__content">
                            <p><?= nl2br(htmlspecialchars($post->content ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
                        </div>
                        <div class="blog-entry__actions">
                            <button type="button" class="link-button" data-edit-toggle="<?= $editTarget; ?>">수정</button>
                            <form method="POST" action="/blog/<?= $post->id; ?>/delete" onsubmit="return confirm('정말 삭제하시겠어요?');">
                                <?= csrf_field(); ?>
                                <button type="submit" class="link-button danger">삭제</button>
                            </form>
                        </div>
                        <form method="POST" action="/blog/<?= $post->id; ?>/update" class="blog-entry__edit" data-edit-form="<?= $editTarget; ?>" hidden>
                            <?= csrf_field(); ?>
                            <label class="form-field">
                                <span>제목</span>
                                <input type="text" name="title" value="<?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?>" required>
                            </label>
                            <label class="form-field">
                                <span>내용</span>
                                <textarea name="content" rows="4" required><?= htmlspecialchars($post->content ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </label>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">저장</button>
                                <button type="button" class="btn btn-ghost" data-edit-cancel="<?= $editTarget; ?>">취소</button>
                            </div>
                        </form>
                    </article>
                <?php endforeach; ?>

                <?php if (empty($posts)): ?>
                    <p class="message message-info">아직 작성한 글이 없습니다. 첫 글을 남겨 보세요!</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.blog-form {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
}

.form-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    font-weight: 600;
    color: #1f2937;
}

.form-field span {
    font-size: 0.95rem;
}

.form-field input,
.form-field textarea {
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-field input:focus,
.form-field textarea:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    outline: none;
}

.form-field--full {
    grid-column: 1 / -1;
}

.form-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

.blog-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.blog-entry {
    padding: 1.75rem;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 12px 35px rgba(15, 23, 42, 0.05);
    position: relative;
}

.blog-entry__header {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.blog-entry__header h3 {
    margin: 0;
    font-size: 1.5rem;
    color: #111827;
}

.blog-entry__meta {
    font-size: 0.9rem;
    color: #6b7280;
}

.blog-entry__content {
    color: #374151;
    line-height: 1.6;
    white-space: pre-wrap;
}

.blog-entry__actions {
    margin-top: 1.5rem;
    display: flex;
    gap: 1rem;
}

.blog-entry__actions form {
    display: inline;
}

.blog-entry__edit {
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px dashed #d1d5db;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.link-button {
    background: none;
    border: none;
    color: #4f46e5;
    font-weight: 600;
    cursor: pointer;
    padding: 0;
}

.link-button:hover {
    text-decoration: underline;
}

.link-button.danger {
    color: #dc2626;
}

@media (max-width: 768px) {
    .blog-entry {
        padding: 1.25rem;
    }

    .blog-entry__actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-edit-toggle');
                const form = document.querySelector(`[data-edit-form="${target}"]`);
                if (form) {
                    form.hidden = false;
                    const firstInput = form.querySelector('input, textarea');
                    if (firstInput) {
                        firstInput.focus();
                        if (firstInput.setSelectionRange) {
                            const length = firstInput.value.length;
                            firstInput.setSelectionRange(length, length);
                        }
                    }
                }
            });
        });

        document.querySelectorAll('[data-edit-cancel]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-edit-cancel');
                const form = document.querySelector(`[data-edit-form="${target}"]`);
                if (form) {
                    form.hidden = true;
                }
            });
        });
    });
</script>