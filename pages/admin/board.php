<?php
$posts = $posts ?? [];
?>

<section>
    <h2>회원 게시판 관리</h2>
    <p class="description">회원 게시판에 등록된 글을 확인하고 관리할 수 있습니다.</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">제목</th>
                <th scope="col">작성자</th>
                <th scope="col">작성일</th>
                <th scope="col">내용</th>
                <th scope="col">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post->id); ?></td>
                    <td><?= htmlspecialchars($post->title); ?></td>
                    <td><?= htmlspecialchars($post->user_name ?? ''); ?></td>
                    <td><?= htmlspecialchars($post->created_at); ?></td>
                    <td class="admin-table__content">
                        <div class="content-preview">
                            <?= nl2br(htmlspecialchars($post->content ?? '', ENT_QUOTES, 'UTF-8')); ?>
                        </div>
                    </td>
                    <td>
                        <form action="/admin/board/delete" method="post" onsubmit="return confirm('정말 삭제하시겠습니까?');">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($post->id); ?>">
                            <button type="submit" class="btn btn-danger">삭제</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <tr>
                    <td colspan="6" class="empty">등록된 게시글이 없습니다.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    border-radius: 12px;
    overflow: hidden;
}

.admin-table th,
.admin-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    text-align: left;
    vertical-align: top;
}

.admin-table thead th {
    background: #f9fafb;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1f2937;
}

.admin-table tbody tr:hover {
    background: #f3f4f6;
}

.admin-table__content {
    max-width: 320px;
}

.content-preview {
    max-height: 120px;
    overflow-y: auto;
    line-height: 1.6;
}

.btn.btn-danger {
    background: #ef4444;
    color: #fff;
    border: none;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    cursor: pointer;
}

.btn.btn-danger:hover {
    background: #dc2626;
}

.description {
    margin-bottom: 1rem;
    color: #4b5563;
}

.empty {
    text-align: center;
    color: #6b7280;
    padding: 2rem 1rem;
}
</style>
