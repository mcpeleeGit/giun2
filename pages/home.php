<section class="hero">
    <div class="container hero-layout">
        <div class="hero-content">
            <?php if (!empty($message)): ?>
                <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <?php if (!empty($notice)): ?>
                <div class="message message-info"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <span class="tag">나만의 라이프 매니저</span>
            <h1>하루의 작은 계획부터 커뮤니티까지, MyLife Hub에서 관리하세요.</h1>
            <p>TO-DO 리스트로 일상을 정리하고, 회원 게시판에서 이야기를 나누며, 마이페이지에서 나만의 기록을 돌아볼 수 있는 공간입니다.</p>
            <div class="hero-actions">
                <?php if (!empty($currentUser)): ?>
                    <a href="/todo" class="btn btn-primary">오늘의 할 일 작성하기</a>
                    <a href="/board" class="btn btn-outline">커뮤니티 참여하기</a>
                <?php else: ?>
                    <a href="/register" class="btn btn-primary">회원가입으로 시작하기</a>
                    <a href="/login" class="btn btn-outline">이미 계정이 있으신가요?</a>
                <?php endif; ?>
            </div>
        </div>
        <ul class="hero-highlights">
            <li>TO-DO 리스트로 하루의 우선순위를 빠르게 정리</li>
            <li>회원 게시판과 갤러리에서 생생한 커뮤니티 소식을 확인</li>
            <li>나만의 블로그에 하루의 생각을 조용히 기록</li>
            <li>마이페이지에서 활동 기록과 통계를 한눈에</li>
        </ul>
    </div>
</section>

<?php if (!empty($calendarMonths ?? [])): ?>
<section class="section">
    <div class="container">
        <div class="surface-card calendar-card">
            <div class="section-header">
                <?php
                $calendarMode = $calendarMode ?? 'community';
                $calendarTitle = $calendarMode === 'personal' ? '나의 일정 캘린더' : '커뮤니티 일정 캘린더';
                $calendarDescription = $calendarMode === 'personal'
                    ? '전달부터 다음 달까지 내가 작성한 게시글과 TO-DO를 한눈에 확인하세요.'
                    : '전달부터 다음 달까지 게시글 등록 현황을 한눈에 확인하세요.';
                ?>
                <h2><?= htmlspecialchars($calendarTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?= htmlspecialchars($calendarDescription, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <div class="calendar-grid">
                <?php foreach ($calendarMonths as $calendarMonth): ?>
                    <?php
                    $position = $calendarMonth['position'] ?? 'adjacent';
                    $validPositions = ['previous', 'current', 'next'];
                    if (!in_array($position, $validPositions, true)) {
                        $position = 'previous';
                    }
                    $monthClasses = ['calendar-month', 'calendar-month--' . $position];
                    $monthClassAttribute = htmlspecialchars(implode(' ', $monthClasses), ENT_QUOTES, 'UTF-8');
                    $isCurrentMonth = !empty($calendarMonth['isCurrent']);
                    ?>
                    <div class="<?= $monthClassAttribute; ?>"<?= $isCurrentMonth ? ' aria-current="date"' : ''; ?>>
                        <header class="calendar-month__header">
                            <h3><?= htmlspecialchars($calendarMonth['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        </header>
                        <table class="calendar-table">
                            <thead>
                                <tr>
                                    <?php foreach ($calendarWeekdays as $weekday): ?>
                                        <th scope="col"><?= htmlspecialchars($weekday, ENT_QUOTES, 'UTF-8'); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($calendarMonth['weeks'] as $week): ?>
                                    <tr>
                                        <?php foreach ($week as $day): ?>
                                            <td class="calendar-day <?= $day['isCurrentMonth'] ? '' : 'is-outside'; ?>">
                                                <span class="calendar-date"><?= htmlspecialchars($day['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php if (!empty($day['entries'])): ?>
                                                    <div class="calendar-entries">
                                                        <?php $entriesToShow = array_slice($day['entries'], 0, 3); ?>
                                                        <?php foreach ($entriesToShow as $entry): ?>
                                                            <?php
                                                            $icon = $calendarIcons[$entry['type']] ?? '•';
                                                            $title = $entry['title'] ?? '';
                                                            if (function_exists('mb_strimwidth')) {
                                                                $title = mb_strimwidth($title, 0, 24, '…', 'UTF-8');
                                                            } else {
                                                                $title = strlen($title) > 24 ? substr($title, 0, 24) . '…' : $title;
                                                            }
                                                            $link = $entry['url'] ?? null;
                                                            $tagName = $link ? 'a' : 'div';
                                                            $hrefAttribute = $link ? ' href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '"' : '';
                                                            ?>
                                                            <<?= $tagName; ?> class="calendar-entry"<?= $hrefAttribute; ?>>
                                                                <span class="calendar-entry-icon"><?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?></span>
                                                                <span class="calendar-entry-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            </<?= $tagName; ?>>
                                                        <?php endforeach; ?>
                                                        <?php $remainingCount = count($day['entries']) - count($entriesToShow); ?>
                                                        <?php if ($remainingCount > 0): ?>
                                                            <div class="calendar-entry calendar-entry--more">+<?= $remainingCount; ?> 더보기</div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (!empty($calendarLegend ?? [])): ?>
                <div class="calendar-legend">
                    <?php foreach ($calendarLegend as $type => $label): ?>
                        <div class="calendar-legend__item">
                            <span class="calendar-entry-icon"><?= htmlspecialchars($calendarIcons[$type] ?? '•', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section--muted">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2>MyLife Hub의 핵심 메뉴</h2>
                <p>일정 관리, 커뮤니티 소통, 계정 관리까지 개인 홈페이지에서 모두 해결하세요.</p>
            </div>
            <div class="feature-grid">
                <article class="card">
                    <h3>TO-DO 리스트</h3>
                    <p>할 일을 추가하고 완료 표시까지! 오늘 해야 할 일을 깔끔하게 관리해 보세요.</p>
                </article>
                <article class="card">
                    <h3>회원 게시판 &amp; 갤러리</h3>
                    <p>커뮤니티 게시판과 갤러리에서 일상의 이야기와 사진을 함께 나눌 수 있습니다.</p>
                </article>
                <article class="card">
                    <h3>나의 블로그 &amp; 마이페이지</h3>
                    <p>나만 볼 수 있는 블로그와 마이페이지에서 오늘의 기록과 활동 현황을 되돌아보세요.</p>
                </article>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2>회원 게시판 새 글</h2>
                <p>우리 커뮤니티에서 방금 올라온 이야기를 확인해 보세요.</p>
            </div>
            <?php if (!empty($recentPosts)): ?>
                <div class="board-grid">
                    <?php foreach ($recentPosts as $post): ?>
                        <article class="board-post">
                            <header>
                                <h3><?= htmlspecialchars($post->title, ENT_QUOTES, 'UTF-8'); ?></h3>
                                <span class="meta">작성자 <?= htmlspecialchars($post->user_name, ENT_QUOTES, 'UTF-8'); ?> · <?= date('Y.m.d H:i', strtotime($post->created_at)); ?></span>
                            </header>
                            <?php
                            $content = $post->content ?? '';
                            if (function_exists('mb_strlen') && function_exists('mb_substr')) {
                                $excerpt = mb_strlen($content, 'UTF-8') > 120 ? mb_substr($content, 0, 120, 'UTF-8') . '…' : $content;
                            } else {
                                $excerpt = strlen($content) > 120 ? substr($content, 0, 120) . '…' : $content;
                            }
                            ?>
                            <p><?= nl2br(htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8')); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="message message-info">아직 게시글이 없습니다. 첫 번째 이야기를 들려주세요!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if (!empty($currentUser)): ?>
<section class="section section--accent">
    <div class="container">
        <div class="surface-card">
            <div class="section-header">
                <h2><?= htmlspecialchars($currentUser->name, ENT_QUOTES, 'UTF-8'); ?>님의 최근 할 일</h2>
                <p>마이페이지에서 전체 목록을 확인하고 더 자세하게 관리할 수 있어요.</p>
            </div>
            <?php if (!empty($recentTodos)): ?>
                <ul class="todo-list">
                    <?php foreach ($recentTodos as $todo): ?>
                        <li class="todo-item <?= $todo->is_completed ? 'completed' : ''; ?>">
                            <div>
                                <div class="todo-title"><?= htmlspecialchars($todo->title, ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="todo-meta">작성일 <?= date('Y.m.d H:i', strtotime($todo->created_at)); ?></div>
                            </div>
                            <span class="tag"><?= $todo->is_completed ? '완료' : '진행중'; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="message message-info">아직 작성한 할 일이 없습니다. 오늘의 할 일을 추가해 보세요!</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
