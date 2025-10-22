<section class="section">
    <div class="container narrow">
        <h2 style="text-align:center;">로그인</h2>
        <p style="text-align:center; color: var(--color-muted);">가입한 계정으로 로그인하여 나만의 공간을 관리해 보세요.</p>

        <?php if (!empty($notice)): ?>
            <div class="message message-info"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST" action="/login" class="form-card">
            <input type="email" name="email" placeholder="이메일" required>
            <input type="password" name="password" placeholder="비밀번호" required>
            <button type="submit" class="btn btn-primary">로그인</button>
            <p style="text-align:center; color: var(--color-muted);">계정이 없으신가요? <a href="/register" class="link">회원가입하기</a></p>
        </form>
    </div>
</section>
