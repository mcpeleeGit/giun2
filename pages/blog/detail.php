<?php
if (!$post) {
    echo "<section class=\"section\"><div class=\"container\"><p class=\"message message-error\">게시물을 찾을 수 없습니다.</p></div></section>";
    return;
}
?>

<section class="section">
    <div class="container">
        <article class="surface-card blog-detail">
            <header>
                <h2><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h2>
                <dl class="meta">
                    <div>
                        <dt>작성자</dt>
                        <dd><?= htmlspecialchars($post->author ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                    <div>
                        <dt>작성일</dt>
                        <dd><?= htmlspecialchars($post->created_at ?? '', ENT_QUOTES, 'UTF-8'); ?></dd>
                    </div>
                </dl>
            </header>
            <div class="content">
                <?= render_rich_text($post->content ?? ''); ?>
            </div>
            <footer>
                <a class="btn btn-ghost" href="/blog">목록으로 돌아가기</a>
            </footer>
        </article>
    </div>
</section>

<style>
.blog-detail {
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
}

.blog-detail header h2 {
    margin: 0 0 1rem;
    font-size: 2rem;
    color: #111827;
}

.blog-detail .meta {
    display: flex;
    gap: 2rem;
    margin: 0 0 1.5rem;
    color: #6b7280;
    font-size: 0.95rem;
}

.blog-detail .meta dt {
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #374151;
}

.blog-detail .content {
    line-height: 1.8;
    color: #1f2937;
    margin-bottom: 2rem;
}

.blog-detail .content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.blog-detail .content th,
.blog-detail .content td {
    border: 1px solid #e5e7eb;
    padding: 0.75rem;
    text-align: left;
    vertical-align: top;
}

.blog-detail .content thead th {
    background: #f3f4f6;
    font-weight: 600;
}

.blog-detail .content a {
    color: #4f46e5;
    text-decoration: underline;
}

.blog-detail .content a:hover,
.blog-detail .content a:focus {
    text-decoration: none;
}
</style>
