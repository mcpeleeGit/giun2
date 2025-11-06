<section class="analytics">
    <header class="analytics__header">
        <h1>접속 통계</h1>
        <p class="analytics__description">최근 <?= htmlspecialchars((string)($periodDays ?? 30), ENT_QUOTES, 'UTF-8'); ?>일 동안의 방문 추이를 확인하세요.</p>
    </header>

    <div class="analytics__stats">
        <article class="stat-card">
            <h2>총 방문 수</h2>
            <p class="stat-card__value"><?= number_format($dailyStats['total'] ?? 0); ?></p>
        </article>
        <article class="stat-card">
            <h2>일평균 방문</h2>
            <?php
                $dayCount = max(1, (int)($periodDays ?? 30));
                $average = ($dailyStats['total'] ?? 0) / $dayCount;
            ?>
            <p class="stat-card__value"><?= number_format($average, 1); ?></p>
        </article>
        <article class="stat-card">
            <h2>표시된 페이지 수</h2>
            <p class="stat-card__value"><?= number_format(count($topPages ?? [])); ?></p>
        </article>
    </div>

    <section class="panel analytics__chart" aria-labelledby="access-chart-title">
        <h2 id="access-chart-title">일별 방문 추이</h2>
        <canvas id="accessChart" role="img" aria-label="일별 방문 추이 그래프"></canvas>
        <?php if (($dailyStats['total'] ?? 0) === 0): ?>
            <p class="empty-state">아직 수집된 방문 데이터가 없습니다.</p>
        <?php endif; ?>
    </section>

    <div class="analytics__grid">
        <section class="panel">
            <h2>인기 페이지 TOP <?= htmlspecialchars((string) min(5, count($topPages ?? [])), ENT_QUOTES, 'UTF-8'); ?></h2>
            <?php if (!empty($topPages)): ?>
                <table>
                    <thead>
                        <tr>
                            <th scope="col">페이지 경로</th>
                            <th scope="col">방문 수</th>
                            <th scope="col">비율</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topPages as $page): ?>
                            <tr>
                                <td><?= htmlspecialchars($page['path'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= number_format($page['count']); ?></td>
                                <td><?= htmlspecialchars(number_format($page['ratio'], 1), ENT_QUOTES, 'UTF-8'); ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">표시할 인기 페이지가 없습니다.</p>
            <?php endif; ?>
        </section>

        <section class="panel">
            <h2>최근 접속 이력</h2>
            <?php if (!empty($recentVisits)): ?>
                <table>
                    <thead>
                        <tr>
                            <th scope="col">접속 시각</th>
                            <th scope="col">경로</th>
                            <th scope="col">방법</th>
                            <th scope="col">IP</th>
                            <th scope="col">User-Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentVisits as $visit): ?>
                            <?php
                                $agentLabel = $visit['user_agent'] ?: '알 수 없음';
                                if (function_exists('mb_strimwidth')) {
                                    $agentShort = mb_strimwidth($agentLabel, 0, 60, '…', 'UTF-8');
                                } else {
                                    $agentShort = strlen($agentLabel) > 60 ? substr($agentLabel, 0, 57) . '...' : $agentLabel;
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($visit['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($visit['path'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($visit['method'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($visit['ip_address'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td title="<?= htmlspecialchars($agentLabel, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($agentShort, ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">최근 접속 이력이 없습니다.</p>
            <?php endif; ?>
        </section>

        <section class="panel">
            <h2>User-Agent별 접속 이력</h2>
            <?php if (!empty($userAgentStats)): ?>
                <table>
                    <thead>
                        <tr>
                            <th scope="col">User-Agent</th>
                            <th scope="col">방문 수</th>
                            <th scope="col">비율</th>
                            <th scope="col">최근 접속</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userAgentStats as $stat): ?>
                            <?php
                                $uaLabel = $stat['user_agent'];
                                if (function_exists('mb_strimwidth')) {
                                    $uaShort = mb_strimwidth($uaLabel, 0, 60, '…', 'UTF-8');
                                } else {
                                    $uaShort = strlen($uaLabel) > 60 ? substr($uaLabel, 0, 57) . '...' : $uaLabel;
                                }
                            ?>
                            <tr>
                                <td title="<?= htmlspecialchars($uaLabel, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars($uaShort, ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td><?= number_format($stat['count']); ?></td>
                                <td><?= htmlspecialchars(number_format($stat['ratio'], 1), ENT_QUOTES, 'UTF-8'); ?>%</td>
                                <td><?= htmlspecialchars($stat['last_visit'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="empty-state">표시할 User-Agent 데이터가 없습니다.</p>
            <?php endif; ?>
        </section>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" integrity="sha384-4xVb4g1QcYeM7vq4XPOGrGxn/JOGj7u4loMIXja+6JGZveHFkNjEcNWef39/C4h2" crossorigin="anonymous"></script>
<script>
    (function() {
        const ctx = document.getElementById('accessChart');
        if (!ctx) {
            return;
        }

        const labels = <?= json_encode($dailyStats['labels'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
        const counts = <?= json_encode($dailyStats['counts'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

        if (!labels.length) {
            return;
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: '방문 수',
                    data: counts,
                    fill: false,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('ko-KR') + '회';
                            }
                        }
                    }
                }
            }
        });
    })();
</script>
