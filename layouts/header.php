<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon">
<?php include "layouts/seo.php"; ?>
</head>
<body>
<header class="site-header">
    <div class="container nav-container">
        <a href="/" class="brand">MyLife Hub</a>
        <nav class="site-nav">
            <a href="/todo">TO-DO 리스트</a>
            <a href="/board">회원 게시판</a>
        </nav>
        <div class="nav-actions">
            <?php $currentUser = current_user(); ?>
            <?php if ($currentUser): ?>
                <span class="welcome">👋 <?= htmlspecialchars($currentUser->name, ENT_QUOTES, 'UTF-8'); ?>님</span>
                <a href="/mypage" class="link">마이페이지</a>
                <a href="/logout" class="btn btn-ghost">로그아웃</a>
            <?php else: ?>
                <a href="/login" class="link">로그인</a>
                <a href="/register" class="btn btn-primary">회원가입</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main">
