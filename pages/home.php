<section class="hero">
    <div class="container hero-layout">
        <div class="hero-content">
            <?php if (!empty($message)): ?>
                <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (!empty($notice)): ?>
                <div class="message message-info"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <span class="tag">나만의 라이프 매니저</span>
            <h1>하루의 작은 계획부터 커뮤니티까지, MyLife Hub에서 관리하세요.</h1>
            <p>TO-DO 리스트로 일상을 정리하고, 회원 게시판에서 이야기를 나누며, 마이페이지에서 나만의 기록을 돌아볼 수 있는 공간입니다.</p>
            <div class="hero-actions">
                <?php if (!empty($currentUser)): ?>
                    <a href="/todo" class="btn btn-primary">오늘의 할 일 작성하기</a>
                    <a href="/board" class="btn btn-outline">커뮤니티 참여하기</a>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary">회원가입으로 시작하기</a>
                    <a href="/login" class="btn btn-outline">이미 계정이 있으신가요?</a>
                <?php endif; ?>
            </div>
        </div>
        <ul class="hero-highlights">
            <li>TO-DO 리스트로 하루의 우선순위를 빠르게 정리</li>
            <li>회원 게시판에서 생생한 커뮤니티 소식을 확인</li>
            <li>마이페이지에서 나만의 기록과 통계를 한눈에</li>
        </ul>
    </div>
</section>

<section class="section section--muted">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2>MyLife Hub의 핵심 메뉴</h2>
                <p>일정 관리, 커뮤니티 소통, 계정 관리까지 개인 홈페이지에서 모두 해결하세요.</p>
            </div>
            <div class="feature-grid">
                <article class="card">
                    <h3>TO-DO 리스트</h3>
                    <p>할 일을 추가하고 완료 표시까지! 오늘 해야 할 일을 깔끔하게 관리해 보세요.</p>
                </article>
                <article class="card">
                    <h3>회원 게시판</h3>
                    <p>커뮤니티 게시판에서 일상의 이야기와 노하우를 서로 나눌 수 있습니다.</p>
                </article>
                <article class="card">
                    <h3>마이페이지</h3>
                    <p>나의 활동 기록과 할 일 현황을 한눈에 확인할 수 있는 개인 공간입니다.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2>회원 게시판 새 글</h2>
                <p>우리 커뮤니티에서 방금 올라온 이야기를 확인해 보세요.</p>
            </div>
            <?php if (!empty($recentPosts)): ?>
                <div class="board-grid">
                    <?php foreach ($recentPosts as $post): ?>
                        <article class="board-post">
                            <header>
                                <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                                <span class="meta">작성자 <?= htmlspecialchars($post->user_name, ENT_QUOTES, 'UTF-8'); ?> · <?= date('Y.m.d H:i', strtotime($post->created_at)); ?></span>
                            </header>
                            <?php
                            $content = $post->content ?? '';
                            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                                $excerpt = mb_strlen($content, 'UTF-8') > 120 ? mb_substr($content, 0, 120, 'UTF-8') . '…' : $content;
                            } else {
                                $excerpt = strlen($content) > 120 ? substr($content, 0, 120) . '…' : $content;
                            }
                            ?>
                            <p><?= nl2br(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8')); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="message message-info">아직 게시글이 없습니다. 첫 번째 이야기를 들려주세요!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($currentUser)): ?>
<section class="section section--accent">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2><?= htmlspecialchars($currentUser->name, ENT_QUOTES, 'UTF-8'); ?>님의 최근 할 일</h2>
                <p>마이페이지에서 전체 목록을 확인하고 더 자세하게 관리할 수 있어요.</p>
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
                <p class="message message-info">아직 작성한 할 일이 없습니다. 오늘의 할 일을 추가해 보세요!</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
