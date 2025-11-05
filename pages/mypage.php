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

<section class="section">
    <div class="container">
        <div class="section-header">
            <h3>내 정보 관리</h3>
            <p>계정 정보를 최신 상태로 유지하고, 필요하다면 안전하게 회원탈퇴할 수 있습니다.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($deleteError)): ?>
            <div class="message message-error"><?= htmlspecialchars($deleteError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div class="account-grid">
            <div class="card">
                <h3>내 정보 수정</h3>
                <p>이름과 이메일을 변경하려면 현재 비밀번호를 입력해 주세요. 비밀번호 변경은 선택 사항입니다.</p>
                <form method="POST" action="/mypage/update" class="form-card">
                    <label class="form-label" for="name">이름</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label class="form-label" for="email">이메일</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?>" required>

                    <label class="form-label" for="current_password">현재 비밀번호</label>
                    <input type="password" id="current_password" name="current_password" placeholder="현재 비밀번호" required>

                    <div class="form-grid two-columns">
                        <div>
                            <label class="form-label" for="new_password">새 비밀번호</label>
                            <input type="password" id="new_password" name="new_password" placeholder="변경 시에만 입력">
                        </div>
                        <div>
                            <label class="form-label" for="new_password_confirmation">새 비밀번호 확인</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" placeholder="다시 입력">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">정보 저장하기</button>
                </form>
            </div>

            <?php if (!empty($kakaoLoginEnabled)): ?>
                <div class="card">
                    <h3>카카오 로그인 연동</h3>
                    <?php if (!empty($user->kakao_id)): ?>
                        <p>카카오 계정이 연동되어 있어 비밀번호 없이도 간편하게 로그인할 수 있습니다.</p>
                        <span class="tag">연동 완료</span>
                    <?php else: ?>
                        <p>카카오 계정을 연동하면 이메일과 비밀번호 없이도 로그인할 수 있습니다.</p>
                        <a href="/auth/kakao/redirect?action=link" class="btn btn-kakao btn-social">카카오 계정 연동하기</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="card card-danger">
                <h3>회원탈퇴</h3>
                <p>작성한 할 일과 게시글은 함께 삭제됩니다. 정말 탈퇴하시려면 아래 입력란을 작성해 주세요.</p>
                <form method="POST" action="/mypage/delete" class="form-card danger">
                    <label class="form-label" for="delete_confirm">확인 문구</label>
                    <input type="text" id="delete_confirm" name="confirm" placeholder="DELETE" required>

                    <label class="form-label" for="delete_password">비밀번호</label>
                    <input type="password" id="delete_password" name="current_password" placeholder="현재 비밀번호" required>

                    <button type="submit" class="btn btn-danger">정말 회원탈퇴 하기</button>
                </form>
            </div>
        </div>
    </div>
</section>
