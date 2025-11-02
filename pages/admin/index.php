<section class="dashboard">
    <header class="dashboard__header">
        <h1>관리자 대시보드</h1>
        <?php if (isset($admin)): ?>
            <p class="dashboard__welcome">안녕하세요, <?= htmlspecialchars($admin->name ?? '관리자', ENT_QUOTES, 'UTF-8'); ?>님!</p>
        <?php endif; ?>
        <p class="dashboard__description">서비스 현황과 최근 활동을 한눈에 확인할 수 있습니다.</p>
    </header>

    <div class="dashboard__stats">
        <article class="stat-card">
            <h2>전체 회원</h2>
            <p class="stat-card__value"><?= number_format($stats['users'] ?? 0); ?>명</p>
        </article>
        <article class="stat-card">
            <h2>게시글 수</h2>
            <p class="stat-card__value"><?= number_format($stats['posts'] ?? 0); ?>개</p>
        </article>
        <article class="stat-card">
            <h2>갤러리 항목</h2>
            <p class="stat-card__value"><?= number_format($stats['gallery'] ?? 0); ?>개</p>
        </article>
    </div>

    <div class="dashboard__grid">
        <section class="panel">
            <h3>최근 가입 회원</h3>
            <ul>
                <?php foreach ($recentUsers ?? [] as $user): ?>
                    <li>
                        <strong><?= htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?= htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($recentUsers ?? [])): ?>
                    <li>최근 가입한 회원이 없습니다.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="panel">
            <h3>최근 게시글</h3>
            <ul>
                <?php foreach ($recentPosts ?? [] as $post): ?>
                    <li>
                        <strong><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?= htmlspecialchars($post->author, ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($recentPosts ?? [])): ?>
                    <li>등록된 게시글이 없습니다.</li>
                <?php endif; ?>
            </ul>
        </section>

        <section class="panel">
            <h3>최근 갤러리</h3>
            <ul>
                <?php foreach ($recentGallery ?? [] as $item): ?>
                    <li>
                        <strong><?= htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <span><?= htmlspecialchars($item->author, ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
                <?php if (empty($recentGallery ?? [])): ?>
                    <li>등록된 갤러리 항목이 없습니다.</li>
                <?php endif; ?>
            </ul>
        </section>
    </div>
</section>
