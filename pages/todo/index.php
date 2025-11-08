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
                            <?php $editTarget = 'todo-' . $todo->id; ?>
                            <li id="todo-<?= $todo->id; ?>" class="todo-item <?= $todo->is_completed ? 'completed' : ''; ?>">
                                <div class="todo-content">
                                    <div>
                                        <div class="todo-title"><?= htmlspecialchars($todo->title, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="todo-meta">작성일 <?= date('Y.m.d H:i', strtotime($todo->created_at)); ?></div>
                                    </div>
                                    <div class="todo-actions">
                                        <button
                                            type="button"
                                            class="link-button todo-action-button"
                                            data-edit-toggle="<?= $editTarget; ?>"
                                            aria-label="수정"
                                        >
                                            <span class="todo-action-icon" aria-hidden="true">
                                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12.59 4.58L15.42 7.41L6.83 16H4V13.17L12.59 4.58Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
                                            <span class="todo-action-label">수정</span>
                                        </button>
                                        <form method="POST" action="/todo/<?= $todo->id; ?>/toggle">
                                            <?php
                                            $isCompleted = (bool) $todo->is_completed;
                                            $toggleLabel = $isCompleted ? '되돌리기' : '완료하기';
                                            ?>
                                            <button type="submit" class="link-button todo-action-button" aria-label="<?= $toggleLabel; ?>">
                                                <span class="todo-action-icon" aria-hidden="true">
                                                    <?php if ($isCompleted): ?>
                                                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 8V4.5C5 4.22 5.22 4 5.5 4H9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M5 4L3 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M4.8 11.5C5.62988 13.8705 7.85014 15.5 10.5 15.5C13.8137 15.5 16.5 12.8137 16.5 9.5C16.5 6.18629 13.8137 3.5 10.5 3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    <?php else: ?>
                                                        <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 10L8.25 13.25L15 6.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    <?php endif; ?>
                                                </span>
                                                <span class="todo-action-label"><?= $toggleLabel; ?></span>
                                            </button>
                                        </form>
                                        <form method="POST" action="/todo/<?= $todo->id; ?>/delete" onsubmit="return confirm('정말 삭제하시겠어요?');">
                                            <button type="submit" class="link-button todo-action-button danger" aria-label="삭제">
                                                <span class="todo-action-icon" aria-hidden="true">
                                                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4 6H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8 6V4H12V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M15 6V15C15 15.55 14.55 16 14 16H6C5.45 16 5 15.55 5 15V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M8.5 9V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        <path d="M11.5 9V13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </span>
                                                <span class="todo-action-label">삭제</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <form method="POST" action="/todo/<?= $todo->id; ?>/update" class="todo-edit-form" data-edit-form="<?= $editTarget; ?>" hidden>
                                    <label class="sr-only" for="todo-title-<?= $todo->id; ?>">할 일 내용 수정</label>
                                    <input id="todo-title-<?= $todo->id; ?>" type="text" name="title" value="<?= htmlspecialchars($todo->title, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    <div class="todo-edit-actions">
                                        <button type="submit" class="btn btn-primary">저장</button>
                                        <button type="button" class="btn btn-ghost" data-edit-cancel="<?= $editTarget; ?>">취소</button>
                                    </div>
                                </form>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        function closeAllEditForms() {
            document.querySelectorAll('.todo-edit-form').forEach(function (openForm) {
                if (!openForm.hidden) {
                    openForm.hidden = true;
                    var item = openForm.closest('.todo-item');
                    var content = item ? item.querySelector('.todo-content') : null;
                    if (content) {
                        content.hidden = false;
                    }
                }
            });
        }

        document.querySelectorAll('[data-edit-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-edit-toggle');
                var form = document.querySelector('[data-edit-form="' + target + '"]');
                var item = button.closest('.todo-item');
                var content = item ? item.querySelector('.todo-content') : null;

                if (form && content) {
                    closeAllEditForms();
                    content.hidden = true;
                    form.hidden = false;

                    var input = form.querySelector('input, textarea');
                    if (input) {
                        input.focus();
                        if (input.setSelectionRange) {
                            var length = input.value.length;
                            input.setSelectionRange(length, length);
                        }
                    }
                }
            });
        });

        document.querySelectorAll('[data-edit-cancel]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-edit-cancel');
                var form = document.querySelector('[data-edit-form="' + target + '"]');
                var item = button.closest('.todo-item');
                var content = item ? item.querySelector('.todo-content') : null;

                if (form && content) {
                    form.hidden = true;
                    content.hidden = false;
                }
            });
        });
    });
</script>
