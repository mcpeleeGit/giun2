<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 페이지</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
    <script src="/assets/js/common.js" defer></script>
</head>
<body>
<header>
    <nav aria-label="관리자 메뉴">
        <a href="/admin">대시보드</a>
        <a href="/admin/users">사용자 관리</a>
        <a href="/admin/posts">블로그 관리</a>
        <a href="/admin/gallery">갤러리 관리</a>
        <a href="/admin/board">회원 게시판 관리</a>
        <a href="/admin/analytics">접속 통계</a>
        <a href="/logout">로그아웃</a>
    </nav>
</header>
<main>
    <?php $notice = flash('admin_notice'); ?>
    <?php $error = flash('admin_error'); ?>
    <?php if ($notice): ?>
        <div class="alert alert-success" role="status"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
