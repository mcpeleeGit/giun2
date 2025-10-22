<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>나의 TO-DO 리스트</h2>
            <p>오늘의 일정을 정리하고 완료된 일에는 뿌듯함을 표시해 보세요.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (empty($currentUser)): ?>
            <div class="message message-info">할 일 관리는 로그인 후 이용할 수 있습니다. <a href="/login" class="link">로그인하기</a></div>
        <?php else: ?>
            <div class="todo-wrapper">
                <form method="POST" action="/todo" class="todo-form">
                    <input type="text" name="title" placeholder="새로운 할 일을 입력해 주세요" required>
                    <button type="submit" class="btn btn-primary">추가하기</button>
                </form>

                <?php if (!empty($todos)): ?>
                    <ul class="todo-list">
                        <?php foreach ($todos as $todo): ?>
                            <li class="todo-item <?= $todo->is_completed ? 'completed' : ''; ?>">
                                <div>
                                    <div class="todo-title"><?= htmlspecialchars($todo->title, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="todo-meta">작성일 <?= date('Y.m.d H:i', strtotime($todo->created_at)); ?></div>
                                </div>
                                <div class="todo-actions">
                                    <form method="POST" action="/todo/<?= $todo->id; ?>/toggle">
                                        <button type="submit" class="link-button"><?= $todo->is_completed ? '되돌리기' : '완료하기'; ?></button>
                                    </form>
                                    <form method="POST" action="/todo/<?= $todo->id; ?>/delete" onsubmit="return confirm('정말 삭제하시겠어요?');">
                                        <button type="submit" class="link-button danger">삭제</button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="message message-info">아직 등록된 할 일이 없습니다. 첫 번째 할 일을 추가해 보세요!</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
