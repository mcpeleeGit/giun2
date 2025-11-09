<section class="hero">
    <div class="container hero-layout<?= !empty($currentUser) ? ' hero-layout--workout-only' : ''; ?>">
        <?php if (!empty($message) || !empty($notice)): ?>
            <div class="hero-messages">
                <?php if (!empty($message)): ?>
                    <div class="message message-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
                <?php if (!empty($notice)): ?>
                    <div class="message message-info"><?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($currentUser)): ?>
            <div class="hero-content">
                <span class="tag">나만의 라이프 매니저</span>
                <h1>작은 계획부터 관리하세요.</h1>
                <p>TO-DO 리스트로 일상을 정리하고, 회원 게시판에서 이야기를 나누며, 마이페이지에서 나만의 기록을 돌아볼 수 있는 공간입니다.</p>
                <div class="hero-actions">
                    <a href="/register" class="btn btn-primary">회원가입으로 시작하기</a>
                    <a href="/login" class="btn btn-outline">이미 계정이 있으신가요?</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($currentUser)): ?>
            <div class="hero-workout hero-workout--expanded">
                <div class="surface-card workout-card">
                    <header class="workout-card__header">
                        <h2>주간 운동 루틴</h2>
                        <p>요일별로 계획을 작성하고 규칙적인 루틴을 완성해 보세요.</p>
                    </header>

                    <?php if (!empty($workoutMessage)): ?>
                        <div class="message message-success"><?= htmlspecialchars($workoutMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <?php if (!empty($workoutError)): ?>
                        <div class="message message-error"><?= htmlspecialchars($workoutError, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="/workout-routines" class="workout-form">
                        <?= csrf_field(); ?>
                        <?php
                        $workoutTodayIndex = isset($workoutTodayIndex) ? (int)$workoutTodayIndex : 0;
                        $workoutTodayLabel = $workoutWeekdays[$workoutTodayIndex] ?? ($workoutWeekdays[0] ?? '');
                        ?>
                        <div class="workout-mobile-controls">
                            <?php if ($workoutTodayLabel !== ''): ?>
                                <span class="workout-mobile-label">오늘은 <?= htmlspecialchars($workoutTodayLabel, ENT_QUOTES, 'UTF-8'); ?> 루틴부터 확인해 보세요.</span>
                            <?php endif; ?>
                            <button type="button" class="btn btn-ghost workout-toggle" data-workout-toggle aria-expanded="false">모든 요일 펼치기</button>
                        </div>
                        <div class="workout-grid" data-workout-grid data-expanded="false" data-today-index="<?= (int)$workoutTodayIndex; ?>">
                            <?php foreach ($workoutWeekdays as $index => $weekday): ?>
                                <?php
                                $routineValue = $workoutRoutines[$index] ?? '';
                                $dayClasses = 'workout-day' . ((int)$index === $workoutTodayIndex ? ' is-today' : '');
                                ?>
                                <div class="<?= $dayClasses; ?>" data-day-index="<?= (int)$index; ?>">
                                    <div class="workout-day__header">
                                        <label class="workout-day__label" for="workout-<?= $index; ?>"><?= htmlspecialchars($weekday, ENT_QUOTES, 'UTF-8'); ?></label>
                                        <button type="button" class="workout-day__add" data-workout-add data-target="workout-<?= $index; ?>" aria-label="<?= htmlspecialchars($weekday, ENT_QUOTES, 'UTF-8'); ?> 운동 추가">
                                            <span aria-hidden="true">+</span>
                                        </button>
                                    </div>
                                    <textarea id="workout-<?= $index; ?>" name="routines[<?= $index; ?>]" rows="4" placeholder="예: 상체 근력 + 스트레칭"><?= htmlspecialchars($routineValue, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="workout-actions">
                            <button type="submit" class="btn btn-primary">운동 루틴 저장</button>
                            <button type="submit" class="btn btn-outline" formaction="/workout-routines/todos">주간 운동 To-Do 저장</button>
                            <p class="workout-hint">빈 칸으로 두면 해당 요일의 루틴이 삭제됩니다. To-Do 저장 버튼을 누르면 입력한 루틴이 TO-DO 리스트에 추가됩니다.</p>
                        </div>
                    </form>
                    <div class="workout-popup" data-workout-popup hidden aria-hidden="true">
                        <div class="workout-popup__overlay" data-workout-popup-overlay></div>
                        <div class="workout-popup__dialog" role="dialog" aria-modal="true" aria-labelledby="workout-popup-title">
                            <div class="workout-popup__header">
                                <h3 id="workout-popup-title">운동 추가</h3>
                                <button type="button" class="workout-popup__close" data-workout-popup-close aria-label="운동 추가 창 닫기">×</button>
                            </div>
                            <div class="workout-popup__body">
                                <label class="workout-popup__field" for="workout-body-select">
                                    <span>신체 부위</span>
                                    <select id="workout-body-select" data-workout-body>
                                        <option value="상체">상체</option>
                                        <option value="하체">하체</option>
                                        <option value="코어">코어</option>
                                        <option value="유산소">유산소</option>
                                        <option value="유연성">유연성</option>
                                    </select>
                                </label>
                                <label class="workout-popup__field" for="workout-exercise-select">
                                    <span>운동 종류</span>
                                    <select id="workout-exercise-select" data-workout-exercise>
                                    </select>
                                </label>
                            </div>
                            <div class="workout-popup__actions">
                                <button type="button" class="btn btn-outline" data-workout-popup-cancel>취소</button>
                                <button type="button" class="btn btn-primary" data-workout-popup-apply>추가</button>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var workoutGrid = document.querySelector('[data-workout-grid]');
                            if (!workoutGrid) {
                                return;
                            }

                            var toggleButton = document.querySelector('[data-workout-toggle]');
                            var todayIndex = parseInt(workoutGrid.getAttribute('data-today-index') || '0', 10);

                            workoutGrid.querySelectorAll('[data-day-index]').forEach(function (dayElement) {
                                var dayIndex = parseInt(dayElement.getAttribute('data-day-index') || '-1', 10);
                                if (dayIndex === todayIndex) {
                                    dayElement.classList.add('is-today');
                                }
                            });

                            if (!toggleButton) {
                                return;
                            }

                            var updateState = function (expanded) {
                                workoutGrid.setAttribute('data-expanded', expanded ? 'true' : 'false');
                                toggleButton.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                                toggleButton.textContent = expanded ? '요일 접기' : '모든 요일 펼치기';
                            };

                            updateState(false);

                            toggleButton.addEventListener('click', function () {
                                var isExpanded = workoutGrid.getAttribute('data-expanded') === 'true';
                                updateState(!isExpanded);
                            });

                            var addButtons = document.querySelectorAll('[data-workout-add]');
                            var workoutPopup = document.querySelector('[data-workout-popup]');
                            if (!workoutPopup || addButtons.length === 0) {
                                return;
                            }

                            var workoutOptions = {
                                '상체': ['푸시업', '벤치 프레스', '덤벨 컬', '렛 풀다운'],
                                '하체': ['스쿼트', '런지', '레그 프레스', '데드리프트'],
                                '코어': ['플랭크', '러시안 트위스트', '레그 레이즈', '마운틴 클라이머'],
                                '유산소': ['러닝', '사이클', '줄넘기', '로잉 머신'],
                                '유연성': ['요가 스트레칭', '필라테스', '폼롤러 마사지', '전신 스트레칭']
                            };

                            var popupOverlay = workoutPopup.querySelector('[data-workout-popup-overlay]');
                            var popupCloseButtons = workoutPopup.querySelectorAll('[data-workout-popup-close], [data-workout-popup-cancel]');
                            var popupApplyButton = workoutPopup.querySelector('[data-workout-popup-apply]');
                            var bodySelect = workoutPopup.querySelector('[data-workout-body]');
                            var exerciseSelect = workoutPopup.querySelector('[data-workout-exercise]');
                            if (!bodySelect || !exerciseSelect) {
                                return;
                            }

                            var activeTextarea = null;
                            var handleKeydown;

                            var populateExercises = function (bodyPart) {
                                var exercises = workoutOptions[bodyPart] || [];
                                exerciseSelect.innerHTML = '';
                                exercises.forEach(function (exercise) {
                                    var option = document.createElement('option');
                                    option.value = exercise;
                                    option.textContent = exercise;
                                    exerciseSelect.appendChild(option);
                                });
                                if (exerciseSelect.options.length === 0) {
                                    var placeholder = document.createElement('option');
                                    placeholder.value = '';
                                    placeholder.textContent = '선택 가능한 운동이 없습니다';
                                    exerciseSelect.appendChild(placeholder);
                                }
                                exerciseSelect.selectedIndex = 0;
                            };

                            var openPopup = function () {
                                workoutPopup.hidden = false;
                                workoutPopup.setAttribute('aria-hidden', 'false');
                                var selectedBody = bodySelect.value || bodySelect.options[0].value;
                                bodySelect.value = selectedBody;
                                populateExercises(selectedBody);
                                bodySelect.focus();
                                handleKeydown = function (event) {
                                    if (event.key === 'Escape') {
                                        event.preventDefault();
                                        closePopup();
                                    }
                                };
                                document.addEventListener('keydown', handleKeydown);
                            };

                            var closePopup = function () {
                                workoutPopup.hidden = true;
                                workoutPopup.setAttribute('aria-hidden', 'true');
                                if (handleKeydown) {
                                    document.removeEventListener('keydown', handleKeydown);
                                    handleKeydown = null;
                                }
                                if (activeTextarea) {
                                    activeTextarea.focus();
                                }
                                activeTextarea = null;
                            };

                            bodySelect.addEventListener('change', function () {
                                populateExercises(bodySelect.value);
                                exerciseSelect.focus();
                            });

                            addButtons.forEach(function (button) {
                                button.addEventListener('click', function () {
                                    var targetId = button.getAttribute('data-target');
                                    activeTextarea = document.getElementById(targetId);
                                    if (!activeTextarea) {
                                        return;
                                    }
                                    openPopup();
                                });
                            });

                            popupCloseButtons.forEach(function (button) {
                                button.addEventListener('click', function () {
                                    closePopup();
                                });
                            });

                            if (popupOverlay) {
                                popupOverlay.addEventListener('click', function () {
                                    closePopup();
                                });
                            }

                            if (popupApplyButton) {
                                popupApplyButton.addEventListener('click', function () {
                                    if (!activeTextarea) {
                                        return;
                                    }

                                    var bodyPart = bodySelect.value;
                                    var exercise = exerciseSelect.value;
                                    if (!bodyPart || !exercise) {
                                        return;
                                    }

                                    var newLine = bodyPart + ' - ' + exercise;
                                    var currentValue = activeTextarea.value;
                                    var trimmedValue = currentValue.replace(/\s+$/, '');
                                    activeTextarea.value = trimmedValue ? trimmedValue + "\n" + newLine : newLine;
                                    closePopup();
                                });
                            }
                        });
                    </script>
                </div>
            </div>
        <?php else: ?>
            <ul class="hero-highlights">
                <li>TO-DO 리스트로 하루의 우선순위를 빠르게 정리</li>
                <li>회원 게시판과 갤러리에서 생생한 커뮤니티 소식을 확인</li>
                <li>나만의 블로그에 하루의 생각을 조용히 기록</li>
                <li>마이페이지에서 활동 기록과 통계를 한눈에</li>
            </ul>
        <?php endif; ?>
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
                    ? '이번 달 일정을 확인하고, 버튼으로 전달과 다음 달까지 펼쳐보세요.'
                    : '이번 달 게시글 현황을 확인하고, 버튼으로 전달과 다음 달까지 펼쳐보세요.';
                ?>
                <h2><?= htmlspecialchars($calendarTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><?= htmlspecialchars($calendarDescription, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
            <?php
            $validPositions = ['previous', 'current', 'next'];
            $normalizedMonths = [
                'previous' => null,
                'current' => null,
                'next' => null,
            ];

            foreach ($calendarMonths as $calendarMonth) {
                $position = $calendarMonth['position'] ?? 'adjacent';
                if (!in_array($position, $validPositions, true)) {
                    $position = 'previous';
                }
                $calendarMonth['position'] = $position;
                $normalizedMonths[$position] = $calendarMonth;
            }

            $renderCalendarMonth = static function (?array $calendarMonth) use ($calendarWeekdays, $calendarIcons) {
                if (empty($calendarMonth)) {
                    return;
                }

                $position = $calendarMonth['position'] ?? 'previous';
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
                                        <?php
                                        $dayEntries = $day['entries'] ?? [];
                                        $entriesToShow = array_slice($dayEntries, 0, 3);
                                        $remainingCount = count($dayEntries) - count($entriesToShow);
                                        ?>
                                        <td class="calendar-day <?= $day['isCurrentMonth'] ? '' : 'is-outside'; ?>">
                                            <span class="calendar-date"><?= htmlspecialchars($day['day'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php if (!empty($dayEntries)): ?>
                                                <div class="calendar-entries">
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
                                                        $entryClasses = ['calendar-entry'];
                                                        if (!empty($entry['isCompleted'])) {
                                                            $entryClasses[] = 'calendar-entry--completed';
                                                        }
                                                        $classAttribute = htmlspecialchars(implode(' ', $entryClasses), ENT_QUOTES, 'UTF-8');
                                                        $hrefAttribute = $link ? ' href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '"' : '';
                                                        ?>
                                                        <<?= $tagName; ?> class="<?= $classAttribute; ?>"<?= $hrefAttribute; ?>>
                                                            <span class="calendar-entry-icon"><?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8'); ?></span>
                                                            <span class="calendar-entry-title"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </<?= $tagName; ?>>
                                                    <?php endforeach; ?>
                                                    <?php if ($remainingCount > 0): ?>
                                                        <?php
                                                        $overlayEntries = [];
                                                        foreach ($dayEntries as $entry) {
                                                            $overlayEntries[] = [
                                                                'icon' => $calendarIcons[$entry['type']] ?? '•',
                                                                'title' => $entry['title'] ?? '',
                                                                'url' => $entry['url'] ?? null,
                                                                'isCompleted' => !empty($entry['isCompleted']),
                                                            ];
                                                        }
                                                        $overlayEntriesJson = htmlspecialchars(json_encode($overlayEntries, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
                                                        $dateIso = $day['date'] ?? '';
                                                        $dateLabel = $dateIso !== '' ? $dateIso . ' 일정' : '일정 상세';
                                                        if ($dateIso !== '') {
                                                            try {
                                                                $dateObject = new \DateTimeImmutable($dateIso);
                                                                $dateLabel = $dateObject->format('Y년 n월 j일 일정');
                                                            } catch (\Exception $exception) {
                                                                $dateLabel = $dateIso . ' 일정';
                                                            }
                                                        }
                                                        ?>
                                                        <button type="button"
                                                            class="calendar-entry calendar-entry--more"
                                                            data-calendar-more
                                                            data-calendar-date="<?= htmlspecialchars($dateIso, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-calendar-date-label="<?= htmlspecialchars($dateLabel, ENT_QUOTES, 'UTF-8'); ?>"
                                                            data-calendar-entries="<?= $overlayEntriesJson; ?>">
                                                            +<?= $remainingCount; ?> 더보기
                                                        </button>
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
                <?php
            };
            ?>
            <div class="calendar-controls">
                <?php if (!empty($normalizedMonths['previous'])): ?>
                    <button type="button" class="btn btn-outline calendar-toggle" data-calendar-toggle="previous" aria-expanded="false">지난 달 펼치기</button>
                <?php endif; ?>
                <?php if (!empty($normalizedMonths['next'])): ?>
                    <button type="button" class="btn btn-outline calendar-toggle" data-calendar-toggle="next" aria-expanded="false">다음 달 펼치기</button>
                <?php endif; ?>
            </div>
            <div class="calendar-stack" data-calendar-stack data-show-previous="false" data-show-next="false">
                <?php foreach (['previous', 'current', 'next'] as $position): ?>
                    <?php if (!empty($normalizedMonths[$position])): ?>
                        <?php $renderCalendarMonth($normalizedMonths[$position]); ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="calendar-overlay" data-calendar-overlay hidden>
                <div class="calendar-overlay__dialog" role="dialog" aria-modal="true" aria-labelledby="calendar-overlay-title" tabindex="-1">
                    <div class="calendar-overlay__header">
                        <h3 id="calendar-overlay-title" data-calendar-overlay-title>일정 상세</h3>
                        <button type="button" class="calendar-overlay__close" data-calendar-overlay-dismiss aria-label="닫기">&times;</button>
                    </div>
                    <ul class="calendar-overlay__list" data-calendar-overlay-list></ul>
                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarStack = document.querySelector('[data-calendar-stack]');
        if (calendarStack) {
            if (!calendarStack.dataset.showPrevious) {
                calendarStack.dataset.showPrevious = 'false';
            }
            if (!calendarStack.dataset.showNext) {
                calendarStack.dataset.showNext = 'false';
            }

            var labelMap = {
                previous: { expand: '지난 달 펼치기', collapse: '지난 달 접기' },
                next: { expand: '다음 달 펼치기', collapse: '다음 달 접기' }
            };

            document.querySelectorAll('[data-calendar-toggle]').forEach(function (button) {
                var position = button.getAttribute('data-calendar-toggle');
                if (!position || !labelMap[position]) {
                    return;
                }

                var dataKey = position === 'previous' ? 'showPrevious' : 'showNext';

                var updateButton = function () {
                    var isExpanded = calendarStack.dataset[dataKey] === 'true';
                    button.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
                    button.textContent = isExpanded ? labelMap[position].collapse : labelMap[position].expand;
                };

                updateButton();

                button.addEventListener('click', function () {
                    var isExpanded = calendarStack.dataset[dataKey] === 'true';
                    calendarStack.dataset[dataKey] = isExpanded ? 'false' : 'true';
                    updateButton();
                });
            });
        }

        var calendarOverlay = document.querySelector('[data-calendar-overlay]');
        if (!calendarOverlay) {
            return;
        }

        var overlayDialog = calendarOverlay.querySelector('.calendar-overlay__dialog');
        var overlayTitle = calendarOverlay.querySelector('[data-calendar-overlay-title]');
        var overlayList = calendarOverlay.querySelector('[data-calendar-overlay-list]');
        var activeTrigger = null;

        var handleKeyDown = function (event) {
            if (event.key === 'Escape') {
                closeOverlay();
            }
        };

        var closeOverlay = function () {
            calendarOverlay.hidden = true;
            calendarOverlay.removeAttribute('data-open');
            document.removeEventListener('keydown', handleKeyDown);
            if (activeTrigger) {
                activeTrigger.focus();
                activeTrigger = null;
            }
        };

        var openOverlay = function (trigger, dateLabel, entries) {
            overlayList.innerHTML = '';

            if (Array.isArray(entries) && entries.length > 0) {
                entries.forEach(function (entry) {
                    var item = document.createElement('li');
                    item.className = 'calendar-overlay__item';
                    if (entry.isCompleted) {
                        item.classList.add('calendar-overlay__item--completed');
                    }

                    var icon = document.createElement('span');
                    icon.className = 'calendar-entry-icon calendar-overlay__icon';
                    icon.textContent = entry.icon || '•';

                    var titleElement;
                    if (entry.url) {
                        titleElement = document.createElement('a');
                        titleElement.href = entry.url;
                        titleElement.className = 'calendar-overlay__link';
                    } else {
                        titleElement = document.createElement('span');
                        titleElement.className = 'calendar-overlay__text';
                    }

                    titleElement.textContent = entry.title || '제목 없는 일정';

                    item.appendChild(icon);
                    item.appendChild(titleElement);
                    overlayList.appendChild(item);
                });
            } else {
                var emptyItem = document.createElement('li');
                emptyItem.className = 'calendar-overlay__item calendar-overlay__item--empty';
                emptyItem.textContent = '표시할 일정이 없습니다.';
                overlayList.appendChild(emptyItem);
            }

            overlayTitle.textContent = dateLabel || '일정 상세';
            calendarOverlay.hidden = false;
            calendarOverlay.setAttribute('data-open', 'true');
            activeTrigger = trigger;
            overlayDialog.focus();
            document.addEventListener('keydown', handleKeyDown);
        };

        document.addEventListener('click', function (event) {
            var moreButton = event.target.closest('[data-calendar-more]');
            if (moreButton) {
                event.preventDefault();
                var entriesData = moreButton.getAttribute('data-calendar-entries') || '[]';
                var parsedEntries;
                try {
                    parsedEntries = JSON.parse(entriesData);
                } catch (error) {
                    parsedEntries = [];
                }
                var dateLabel = moreButton.getAttribute('data-calendar-date-label') || '일정 상세';
                openOverlay(moreButton, dateLabel, Array.isArray(parsedEntries) ? parsedEntries : []);
            }
        });

        calendarOverlay.addEventListener('click', function (event) {
            if (event.target.hasAttribute('data-calendar-overlay-dismiss') || event.target === calendarOverlay) {
                closeOverlay();
            }
        });
    });
</script>
<?php endif; ?>

<?php $homeGalleryItems = $homeGalleryItems ?? []; ?>
<section class="section section--muted">
    <div class="container">
        <div class="surface-card">
            <?php if (empty($currentUser)): ?>
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
            <?php else: ?>
                <div class="section-header">
                    <h2>갤러리 하이라이트</h2>
                    <p>로그인한 회원을 위한 최신 커뮤니티 작품을 둘러보세요.</p>
                </div>
                <?php if (!empty($homeGalleryItems)): ?>
                    <div class="home-gallery">
                        <?php foreach ($homeGalleryItems as $item): ?>
                            <?php
                            $itemId = (int)($item->id ?? 0);
                            $itemTitle = $item->title ?? '갤러리 항목';
                            $itemDescription = $item->description ?? '';
                            if (function_exists('mb_strimwidth')) {
                                $itemExcerpt = mb_strimwidth($itemDescription, 0, 90, '…', 'UTF-8');
                            } else {
                                $itemExcerpt = strlen($itemDescription) > 90 ? substr($itemDescription, 0, 90) . '…' : $itemDescription;
                            }
                            ?>
                            <article class="home-gallery__item">
                                <a class="home-gallery__thumb" href="/gallery/<?= $itemId; ?>" aria-label="<?= htmlspecialchars($itemTitle . ' 상세 보기', ENT_QUOTES, 'UTF-8'); ?>">
                                    <img src="<?= htmlspecialchars($item->image_path ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>">
                                </a>
                                <div class="home-gallery__body">
                                    <h3 class="home-gallery__title"><?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <?php if ($itemExcerpt !== ''): ?>
                                        <p class="home-gallery__excerpt"><?= htmlspecialchars($itemExcerpt, ENT_QUOTES, 'UTF-8'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="home-gallery__more">
                        <a class="btn btn-ghost" href="/gallery">갤러리 전체 보기</a>
                    </div>
                <?php else: ?>
                    <p class="message message-info">아직 등록된 갤러리 작품이 없습니다. 첫 번째 작품을 공유해 보세요!</p>
                <?php endif; ?>
            <?php endif; ?>
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
