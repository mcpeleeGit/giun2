<?php
namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\BoardService;
use App\Services\TodoService;
use App\Services\WorkoutRoutineService;
use function current_user;
use function flash;
use function view;

class HomeController {
    private $boardService;
    private $todoService;
    private $blogService;
    private $workoutRoutineService;

    public function __construct()
    {
        $this->boardService = new BoardService();
        $this->todoService = new TodoService();
        $this->blogService = new BlogService();
        $this->workoutRoutineService = new WorkoutRoutineService();
    }

    public function home() {
        $currentUser = current_user();
        $calendarData = $this->prepareCalendarData($currentUser);

        view('home', [
            'currentUser' => $currentUser,
            'recentPosts' => $this->boardService->getRecentPosts(3),
            'recentTodos' => $currentUser ? $this->todoService->getRecentTodos($currentUser->id, 3) : [],
            'message' => flash('home_message'),
            'notice' => flash('auth_notice'),
            'workoutMessage' => flash('workout_message'),
            'workoutError' => flash('workout_error'),
            'workoutRoutines' => $currentUser ? $this->workoutRoutineService->getRoutineMapForUser($currentUser->id) : [],
            'workoutWeekdays' => ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'],
            'calendarMonths' => $calendarData['months'],
            'calendarIcons' => $calendarData['icons'],
            'calendarWeekdays' => $calendarData['weekdayLabels'],
            'calendarLegend' => $calendarData['legend'],
            'calendarMode' => $calendarData['mode'],
        ]);
    }

    private function prepareCalendarData($currentUser): array
    {
        $currentMonth = new \DateTimeImmutable('first day of this month 00:00:00');
        $monthOffsets = [-1, 0, 1];
        $monthPeriods = [];

        foreach ($monthOffsets as $index => $offset) {
            $targetMonth = $currentMonth->modify(($offset >= 0 ? '+' : '') . $offset . ' month');
            $monthStart = $targetMonth->setTime(0, 0, 0);
            $monthEnd = $targetMonth->modify('last day of this month')->setTime(23, 59, 59);

            $monthPeriods[] = [
                'start' => $monthStart,
                'end' => $monthEnd,
                'position' => $offset < 0 ? 'previous' : ($offset > 0 ? 'next' : 'current'),
            ];
        }

        $rangeStart = $monthPeriods[0]['start'];
        $rangeEnd = $monthPeriods[count($monthPeriods) - 1]['end'];

        if ($currentUser) {
            $boardPosts = $this->boardService->getPostsBetweenForUser($currentUser->id, $rangeStart, $rangeEnd);
            $blogPosts = $this->blogService->getPostsBetweenForUser($currentUser->id, $currentUser->name, $rangeStart, $rangeEnd);
            $todos = $this->todoService->getTodosBetween($currentUser->id, $rangeStart, $rangeEnd);
        } else {
            $boardPosts = $this->boardService->getPostsBetween($rangeStart, $rangeEnd);
            $blogPosts = $this->blogService->getPostsBetween($rangeStart, $rangeEnd);
            $todos = [];
        }

        $entriesByDate = [];

        foreach ($boardPosts as $post) {
            $dateKey = (new \DateTimeImmutable($post->created_at))->format('Y-m-d');
            $entriesByDate[$dateKey][] = [
                'type' => 'board',
                'title' => $post->title,
                'url' => '/board#board-post-' . $post->id,
            ];
        }

        foreach ($blogPosts as $post) {
            $dateKey = (new \DateTimeImmutable($post->created_at))->format('Y-m-d');
            $entriesByDate[$dateKey][] = [
                'type' => 'blog',
                'title' => $post->title,
                'url' => '/blog/' . $post->id,
            ];
        }

        foreach ($todos as $todo) {
            $dateKey = (new \DateTimeImmutable($todo->created_at))->format('Y-m-d');
            $entriesByDate[$dateKey][] = [
                'type' => 'todo',
                'title' => $todo->title,
                'url' => '/todo#todo-' . $todo->id,
            ];
        }

        $months = [];

        foreach ($monthPeriods as $period) {
            $monthStart = $period['start'];
            $monthEnd = $period['end'];

            $startOffset = (int)$monthStart->format('w');
            $calendarStart = $monthStart->modify('-' . $startOffset . ' days');

            $endOffset = 6 - (int)$monthEnd->format('w');
            $calendarEnd = $monthEnd->modify('+' . $endOffset . ' days');

            $weeks = [];
            $currentDay = $calendarStart;

            while ($currentDay <= $calendarEnd) {
                $week = [];
                for ($i = 0; $i < 7; $i++) {
                    $dateKey = $currentDay->format('Y-m-d');
                    $week[] = [
                        'date' => $dateKey,
                        'day' => $currentDay->format('j'),
                        'isCurrentMonth' => $currentDay->format('n') === $monthStart->format('n'),
                        'entries' => $entriesByDate[$dateKey] ?? [],
                    ];
                    $currentDay = $currentDay->modify('+1 day');
                }

                $weeks[] = $week;
            }

            $months[] = [
                'label' => $monthStart->format('Y년 n월'),
                'weeks' => $weeks,
                'position' => $period['position'] ?? ($monthStart->format('Ym') === $currentMonth->format('Ym') ? 'current' : 'previous'),
                'isCurrent' => ($period['position'] ?? null) === 'current',
            ];
        }

        $icons = [
            'board' => '🗂',
            'blog' => '✍️',
        ];

        $legend = [
            'board' => '회원 게시판',
            'blog' => $currentUser ? '나의 블로그' : '블로그',
        ];

        if ($currentUser) {
            $icons['todo'] = '✅';
            $legend['todo'] = 'TO-DO 리스트';
        }

        return [
            'months' => $months,
            'icons' => $icons,
            'legend' => $legend,
            'mode' => $currentUser ? 'personal' : 'community',
            'weekdayLabels' => ['일', '월', '화', '수', '목', '금', '토'],
        ];
    }
}
