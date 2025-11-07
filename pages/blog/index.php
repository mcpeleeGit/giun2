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
                        <textarea name="content" rows="10" class="js-rich-editor" placeholder="오늘의 생각을 기록해 보세요." required></textarea>
                        <p class="form-error" hidden>내용을 입력해 주세요.</p>
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
                            <?php
                            $rawContent = (string)($post->content ?? '');
                            $hasMarkup = preg_match('/<\s*(?:p|br|strong|em|u|s|ul|ol|li|blockquote|pre|code|figure|figcaption|a|img|h[1-4])/i', $rawContent) === 1;
                            echo $hasMarkup
                                ? $rawContent
                                : nl2br(htmlspecialchars($rawContent, ENT_QUOTES, 'UTF-8'));
                            ?>
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
                                <textarea name="content" rows="10" class="js-rich-editor" required><?= htmlspecialchars($post->content ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                <p class="form-error" hidden>내용을 입력해 주세요.</p>
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

.form-field.has-rich-editor .ck-editor {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-field.has-rich-editor .ck-toolbar {
    border-bottom: 1px solid #e5e7eb;
}

.form-field.has-rich-editor .ck-editor__editable {
    min-height: 240px;
    padding: 1rem;
}

.form-field.has-rich-editor .ck-editor__editable:focus {
    box-shadow: none;
}

.form-field.has-rich-editor .ck-focused {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.form-field--error textarea,
.form-field--error .ck-editor {
    border-color: #dc2626 !important;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

.form-error {
    margin: 0;
    font-size: 0.85rem;
    color: #dc2626;
    display: none;
}

.form-field--error .form-error {
    display: block;
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
    line-height: 1.7;
    word-break: break-word;
}

.blog-entry__content p,
.blog-entry__content h1,
.blog-entry__content h2,
.blog-entry__content h3,
.blog-entry__content h4,
.blog-entry__content ul,
.blog-entry__content ol,
.blog-entry__content blockquote,
.blog-entry__content pre {
    margin: 0 0 1rem;
}

.blog-entry__content ul,
.blog-entry__content ol {
    padding-left: 1.25rem;
}

.blog-entry__content img,
.blog-entry__content figure {
    max-width: 100%;
}

.blog-entry__content img {
    border-radius: 12px;
    display: block;
    height: auto;
    margin: 1.25rem auto;
}

.blog-entry__content figure {
    margin: 1.5rem 0;
    text-align: center;
}

.blog-entry__content figcaption {
    font-size: 0.9rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.blog-entry__content pre {
    background: #f3f4f6;
    border-radius: 8px;
    padding: 1rem;
    overflow-x: auto;
}

.blog-entry__content > :last-child {
    margin-bottom: 0;
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

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const editorInstances = new Map();

        class BlogImageUploadAdapter {
            constructor(loader, token) {
                this.loader = loader;
                this.csrfToken = token;
                this.abortController = new AbortController();
            }

            upload() {
                return this.loader.file.then((file) => new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('image', file);

                    const headers = {};
                    if (this.csrfToken) {
                        headers['X-CSRF-TOKEN'] = this.csrfToken;
                    }

                    fetch('/blog/upload-image', {
                        method: 'POST',
                        body: formData,
                        headers,
                        signal: this.abortController.signal,
                    })
                        .then((response) => {
                            if (!response.ok) {
                                return response.json().catch(() => ({})).then((data) => {
                                    const message = data.error ?? '이미지 업로드에 실패했습니다.';
                                    throw new Error(message);
                                });
                            }

                            return response.json();
                        })
                        .then((data) => {
                            if (data && data.url) {
                                resolve({ default: data.url });
                            } else {
                                reject(new Error('이미지 업로드 응답이 올바르지 않습니다.'));
                            }
                        })
                        .catch((error) => {
                            if (error.name === 'AbortError') {
                                return;
                            }
                            reject(error);
                        });
                }));
            }

            abort() {
                this.abortController.abort();
            }
        }

        function blogImageUploadPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => new BlogImageUploadAdapter(loader, csrfToken);
        }

        function initializeEditors() {
            if (typeof ClassicEditor === 'undefined') {
                console.error('에디터를 불러오지 못했습니다.');
                return;
            }

            document.querySelectorAll('textarea.js-rich-editor').forEach((textarea) => {
                if (editorInstances.has(textarea)) {
                    return;
                }

                ClassicEditor.create(textarea, {
                    extraPlugins: [blogImageUploadPlugin],
                    language: 'ko',
                    toolbar: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'underline',
                        'link',
                        'bulletedList',
                        'numberedList',
                        'blockQuote',
                        'insertTable',
                        'imageUpload',
                        'undo',
                        'redo',
                    ],
                }).then((editor) => {
                    editorInstances.set(textarea, editor);

                    textarea.removeAttribute('required');
                    textarea.required = false;

                    const field = textarea.closest('.form-field');
                    const errorMessage = field?.querySelector('.form-error');

                    const clearErrorState = () => {
                        if (field) {
                            field.classList.remove('form-field--error');
                        }
                        if (errorMessage) {
                            errorMessage.hidden = true;
                        }
                    };

                    if (field) {
                        field.classList.add('has-rich-editor');
                    }

                    editor.model.document.on('change:data', clearErrorState);

                    const form = textarea.closest('form');
                    if (form) {
                        form.addEventListener('submit', (event) => {
                            const data = editor.getData();
                            const tempContainer = document.createElement('div');
                            tempContainer.innerHTML = data;

                            const textContent = (tempContainer.textContent || '').replace(/\u00a0/g, '').trim();
                            const hasMediaContent = Boolean(tempContainer.querySelector('img, video, audio, iframe, embed, object, figure'));

                            if (!textContent && !hasMediaContent) {
                                event.preventDefault();
                                if (field) {
                                    field.classList.add('form-field--error');
                                }
                                if (errorMessage) {
                                    errorMessage.hidden = false;
                                }
                                editor.editing.view.focus();
                                return;
                            }

                            textarea.value = data;
                            clearErrorState();
                        });
                    }
                }).catch((error) => {
                    console.error('에디터 초기화에 실패했습니다.', error);
                });
            });
        }

        function focusForm(form) {
            requestAnimationFrame(() => {
                const editable = form.querySelector('.ck-editor__editable');
                if (editable) {
                    editable.focus();
                    return;
                }

                const firstInput = form.querySelector('input, textarea');
                if (firstInput) {
                    firstInput.focus();
                    if (typeof firstInput.setSelectionRange === 'function') {
                        const length = firstInput.value.length;
                        firstInput.setSelectionRange(length, length);
                    }
                }
            });
        }

        initializeEditors();

        document.querySelectorAll('[data-edit-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-edit-toggle');
                const form = document.querySelector(`[data-edit-form="${target}"]`);
                if (!form) {
                    return;
                }

                form.hidden = false;
                initializeEditors();
                focusForm(form);
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