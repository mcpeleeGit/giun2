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
                <?php
                    $isOwner = $currentUser && (int)$currentUser->id === (int)$post->user_id;
                    $editTarget = 'board-' . $post->id;
                ?>
                <article class="board-post">
                    <header>
                        <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="meta">
                            작성자 <?= htmlspecialchars($post->user_name, ENT_QUOTES, 'UTF-8'); ?> · <?= date('Y.m.d H:i', strtotime($post->created_at)); ?>
                            <?php if (!empty($post->updated_at) && $post->updated_at !== $post->created_at): ?>
                                <span class="meta-updated">(수정 <?= date('Y.m.d H:i', strtotime($post->updated_at)); ?>)</span>
                            <?php endif; ?>
                        </div>
                    </header>
                    <div class="board-post-content"><?= nl2br(htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8')); ?></div>
                    <?php if ($isOwner): ?>
                        <div class="board-post-actions">
                            <button type="button" class="link-button" data-edit-toggle="<?= $editTarget; ?>">수정</button>
                            <form method="POST" action="/board/<?= $post->id; ?>/delete" onsubmit="return confirm('정말 삭제하시겠어요?');">
                                <button type="submit" class="link-button danger">삭제</button>
                            </form>
                        </div>
                        <form method="POST" action="/board/<?= $post->id; ?>/update" class="board-edit-form" data-edit-form="<?= $editTarget; ?>" hidden>
                            <label class="sr-only" for="board-title-<?= $post->id; ?>">게시글 제목 수정</label>
                            <input id="board-title-<?= $post->id; ?>" type="text" name="title" value="<?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?>" required>
                            <label class="sr-only" for="board-content-<?= $post->id; ?>">게시글 내용 수정</label>
                            <textarea id="board-content-<?= $post->id; ?>" name="content" rows="6" required><?= htmlspecialchars($post->content, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            <div class="board-edit-actions">
                                <button type="submit" class="btn btn-primary">저장</button>
                                <button type="button" class="btn btn-ghost" data-edit-cancel="<?= $editTarget; ?>">취소</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="message message-info">아직 작성된 게시글이 없습니다. 첫 번째 글의 주인공이 되어 보세요!</p>
        <?php endif; ?>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-edit-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.getAttribute('data-edit-toggle');
                var form = document.querySelector('[data-edit-form="' + target + '"]');

                if (form) {
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

                if (form) {
                    form.hidden = true;
                }
            });
        });
    });
</script>
