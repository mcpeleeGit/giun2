<section>
    <h2>회원가입</h2>
    <form id="registerForm" method="POST" action="/register">
        <input type="text" name="name" placeholder="이름" required>
        <input type="email" name="email" placeholder="이메일" required>
        <input type="password" name="password" placeholder="비밀번호" required>
        <button type="submit">가입하기</button>
    </form>
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
        return;
    }
});

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
</script>
