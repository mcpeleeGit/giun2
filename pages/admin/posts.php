<?php $user = current_user(); ?>

<section>
    <h2>게시물 관리</h2>
    
    <!-- 게시물 등록 버튼 -->
    <div class="register-button-div">
        <button id="openModal">등록</button>
    </div>

    <!-- 모달 창 -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="postForm" action="/admin/posts/create" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id">
                <label for="title">제목:</label>
                <input type="text" id="title" name="title" required>
                
                <label for="author">작성자:</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($user->name ?? '') ?>" readonly>
                
                <label for="content">내용:</label>
                <textarea id="content" name="content" rows="4" required></textarea>
                
                <button type="submit" id="submitButton">등록</button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>제목</th>
                <th>작성자</th>
                <th>작성일</th>
                <th>삭제</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post->id) ?></td>
                    <td><?= htmlspecialchars($post->title) ?></td>
                    <td><?= htmlspecialchars($post->author) ?></td>
                    <td><?= htmlspecialchars($post->created_at) ?></td>
                    <td>
                        <form action="/admin/posts/delete" method="post" style="display:inline;">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id" value="<?= htmlspecialchars($post->id) ?>">
                            <button type="submit" onclick="return confirm('정말로 이 게시물을 삭제하시겠습니까?');">삭제</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($posts ?? [])): ?>
                <tr>
                    <td colspan="5">등록된 게시물이 없습니다.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<script type="module">


document.getElementById('openModal').onclick = function() {
    document.getElementById('modal').style.display = 'block';
}

document.querySelector('.close').onclick = function() {
    document.getElementById('modal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('modal')) {
        document.getElementById('modal').style.display = 'none';
    }
}

document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('click', event => {
        if (event.target.closest('button')) {
            return;
        }

        document.getElementById('modal').style.display = 'block';
        const postId = row.children[0].textContent.trim();

        fetchApi(`/api/blog/${postId}`)
            .then(data => {
                document.getElementById('id').value = data.id;
                document.getElementById('title').value = data.title;
                document.getElementById('author').value = data.author;
                document.getElementById('content').value = data.content;
                document.getElementById('submitButton').textContent = '수정';
                document.getElementById('postForm').action = '/admin/posts/update';
            })
            .catch(error => {
                console.error('게시물 조회 중 오류 발생:', error);
            });
    });
});

// 모달이 열릴 때마다 폼 액션과 버튼 텍스트를 초기화
function openModal() {
    document.getElementById('modal').style.display = 'block';
    document.getElementById('postForm').reset();
    document.getElementById('id').value = '';
    document.getElementById('submitButton').textContent = '등록';
    document.getElementById('postForm').action = '/admin/posts/create';
}

document.getElementById('openModal').onclick = openModal;
</script> 