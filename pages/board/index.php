<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>회원 게시판</h2>
            <p>커뮤니티 구성원들과 함께 일상과 노하우를 공유해 보세요.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($currentUser)): ?>
            <form method="POST" action="/board" class="board-form card">
                <h3>새 글 작성</h3>
                <input type="text" name="title" placeholder="게시글 제목" required>
                <textarea name="content" rows="6" placeholder="서로에게 힘이 되는 이야기를 들려주세요." required></textarea>
                <button type="submit" class="btn btn-primary">게시글 등록</button>
            </form>
        <?php else: ?>
            <div class="message message-info">게시글 작성은 로그인한 회원만 이용할 수 있습니다. <a href="/login" class="link">로그인하기</a></div>
        <?php endif; ?>

        <?php if (!empty($posts)): ?>
            <?php foreach ($posts as $post): ?>
                <article class="board-post">
                    <header>
                        <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="meta">작성자 <?= htmlspecialchars($post->user_name, ENT_QUOTES, 'UTF-8'); ?> · <?= date('Y.m.d H:i', strtotime($post->created_at)); ?></div>
                    </header>
                    <div><?= nl2br(htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8')); ?></div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="message message-info">아직 작성된 게시글이 없습니다. 첫 번째 글의 주인공이 되어 보세요!</p>
        <?php endif; ?>
    </div>
</section>
