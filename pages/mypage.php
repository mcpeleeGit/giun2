<section class="section">
    <div class="container">
        <div class="section-header">
            <h2><?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>님의 마이페이지</h2>
            <p>나의 할 일 현황과 커뮤니티 활동을 한눈에 확인할 수 있는 공간입니다.</p>
        </div>

        <?php if (!empty($notice)): ?>
            <div class="message message-success"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <article class="card stats-card">
                <div class="number"><?= $todoStats['total']; ?></div>
                <p>총 할 일</p>
            </article>
            <article class="card stats-card">
                <div class="number"><?= $todoStats['completed']; ?></div>
                <p>완료된 할 일</p>
            </article>
            <article class="card stats-card">
                <div class="number"><?= $todoStats['pending']; ?></div>
                <p>남은 할 일</p>
            </article>
        </div>

        <div class="hero-actions" style="margin-top: 2.5rem;">
            <a href="/todo" class="btn btn-primary">TO-DO 리스트 관리하기</a>
            <a href="/board" class="btn btn-outline">게시판에서 소통하기</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h3>최근에 작성한 할 일</h3>
            <p>가장 최근에 추가한 할 일을 확인할 수 있습니다.</p>
        </div>
        <?php if (!empty($recentTodos)): ?>
            <ul class="todo-list">
                <?php foreach ($recentTodos as $todo): ?>
                    <li class="todo-item <?= $todo->is_completed ? 'completed' : ''; ?>">
                        <div>
                            <div class="todo-title"><?= htmlspecialchars($todo->title, ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="todo-meta">작성일 <?= date('Y.m.d H:i', strtotime($todo->created_at)); ?></div>
                        </div>
                        <span class="tag"><?= $todo->is_completed ? '완료' : '진행중'; ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="message message-info">아직 등록된 할 일이 없습니다. 지금 바로 새로운 할 일을 추가해 보세요!</p>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h3>내가 남긴 게시글</h3>
            <p>커뮤니티에서 나눈 이야기를 모아두었습니다.</p>
        </div>
        <?php if (!empty($recentPosts)): ?>
            <?php foreach ($recentPosts as $post): ?>
                <article class="board-post">
                    <header>
                        <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="meta">작성일 <?= date('Y.m.d H:i', strtotime($post->created_at)); ?></div>
                    </header>
                    <div><?= nl2br(htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8')); ?></div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="message message-info">아직 게시글을 작성하지 않았습니다. 게시판에서 이야기를 시작해 보세요!</p>
        <?php endif; ?>
    </div>
</section>
