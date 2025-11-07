<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="/assets/images/favicon.png" type="image/x-icon">
<?php include "layouts/seo.php"; ?>
</head>
<body>
<header class="site-header">
    <div class="container nav-container">
        <?php
        $currentUser = current_user();
        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $normalizedPath = rtrim($currentPath, '/') ?: '/';
        $navItems = [
            ['href' => '/', 'label' => '홈'],
            ['href' => '/todo', 'label' => 'TO-DO 리스트'],
            ['href' => '/board', 'label' => '회원 게시판'],
            ['href' => '/gallery', 'label' => '갤러리'],
        ];
        if ($currentUser) {
            $navItems[] = ['href' => '/blog', 'label' => '나의 블로그'];
            if (($currentUser->role ?? null) === 'ADMIN') {
                $navItems[] = ['href' => '/admin', 'label' => '관리자 페이지'];
            }

            $navItems[] = ['href' => '/mypage', 'label' => '마이페이지'];
        }

        $isActive = static function (string $href) use ($normalizedPath): bool {
            if ($href === '/') {
                return $normalizedPath === '/';
            }

            $prefixLength = strlen($href);
            if ($prefixLength === 0) {
                return false;
            }

            if (strncmp($normalizedPath, $href, $prefixLength) !== 0) {
                return false;
            }

            if (strlen($normalizedPath) === $prefixLength) {
                return true;
            }

            return $normalizedPath[$prefixLength] === '/';
        };
        ?>
        <a href="/" class="brand">MyLife Hub</a>
        <nav class="site-nav" aria-label="주요 메뉴">
            <?php foreach ($navItems as $item): ?>
                <?php $activeClass = $isActive($item['href']) ? ' is-active' : ''; ?>
                <a href="<?= $item['href']; ?>" class="nav-link<?= $activeClass; ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></a>
            <?php endforeach; ?>
        </nav>
        <div class="nav-actions">
            <?php if ($currentUser): ?>
                <span class="welcome">👋 <?= htmlspecialchars($currentUser->name, ENT_QUOTES, 'UTF-8'); ?>님</span>
                <a href="/logout" class="btn btn-ghost">로그아웃</a>
            <?php else: ?>
                <a href="/login" class="link">로그인</a>
                <a href="/register" class="btn btn-primary">회원가입</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="site-main">
