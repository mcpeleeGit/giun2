<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>나의 멋진 홈페이지</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header>
    <nav>
        <a href="/">홈</a>
        <a href="/blog">블로그</a>
        <a href="/gallery">갤러리</a>

        <?php
        if (isset($_SESSION['user'])):
            $user = unserialize($_SESSION['user']); // 세션에서 User 객체를 역직렬화
        ?>
            <span>👤 <?= htmlspecialchars($user->name) ?>님</span>
            <a href="/logout">로그아웃</a>
            <?php if ($user->role === 'ADMIN'): // 사용자의 역할이 'ADMIN'인지 확인 ?>
                <a href="/admin">관리자 페이지</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/register">회원가입</a>
            <a href="/login">로그인</a>
        <?php endif; ?>
    </nav>
</header>
<main>
