<section class="section">
    <div class="container narrow">
        <h2 style="text-align:center;">회원가입</h2>
        <p style="text-align:center; color: var(--color-muted);">간단한 정보 입력으로 MyLife Hub의 모든 기능을 이용해 보세요.</p>

        <?php if (!empty($error)): ?>
            <div class="message message-error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="/register" class="form-card">
            <input type="text" name="name" placeholder="이름" required>
            <input type="email" name="email" placeholder="이메일" required>
            <input type="password" name="password" placeholder="비밀번호 (6자 이상)" required minlength="6">
            <button type="submit" class="btn btn-primary">가입하기</button>
            <p style="text-align:center; color: var(--color-muted);">이미 계정이 있으신가요? <a href="/login" class="link">로그인하기</a></p>
        </form>
    </div>
</section>

<script>
document.getElementById('registerForm').addEventListener('submit', function(event) {
    var name = document.querySelector('input[name="name"]').value.trim();
    var email = document.querySelector('input[name="email"]').value.trim();
    var password = document.querySelector('input[name="password"]').value.trim();

    if (name === '' || email === '' || password === '') {
        alert('모든 필드를 입력해 주세요.');
        event.preventDefault();
        return;
    }

    if (!validateEmail(email)) {
        alert('유효한 이메일 주소를 입력해 주세요.');
        event.preventDefault();
        return;
    }

    if (password.length < 6) {
        alert('비밀번호는 최소 6자 이상이어야 합니다.');
        event.preventDefault();
    }
});

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
</script>
