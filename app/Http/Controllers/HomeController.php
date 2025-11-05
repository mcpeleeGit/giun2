<?php
namespace App\Http\Controllers;

use App\Services\BlogService;
use App\Services\BoardService;
use App\Services\TodoService;

class HomeController {
    private $boardService;
    private $todoService;
    private $blogService;

    public function __construct()
    {
        $this->boardService = new BoardService();
        $this->todoService = new TodoService();
        $this->blogService = new BlogService();
    }

    public function home() {
        $currentUser = current_user();
        $calendarData = $this->prepareCalendarData();

        view('home', [
            'currentUser' => $currentUser,
            'recentPosts' => $this->boardService->getRecentPosts(3),
            'recentTodos' => $currentUser ? $this->todoService->getRecentTodos($currentUser->id, 3) : [],
            'message' => flash('home_message'),
            'notice' => flash('auth_notice'),
            'calendarMonths' => $calendarData['months'],
            'calendarIcons' => $calendarData['icons'],
            'calendarWeekdays' => $calendarData['weekdayLabels'],
        ]);
    }

    private function prepareCalendarData(): array
    {
        $currentMonth = new \DateTimeImmutable('first day of this month 00:00:00');
        $monthOffsets = [-1, 0, 1];
        $monthPeriods = [];

        foreach ($monthOffsets as $offset) {
            $targetMonth = $currentMonth->modify(($offset >= 0 ? '+' : '') . $offset . ' month');
            $monthStart = $targetMonth->setTime(0, 0, 0);
            $monthEnd = $targetMonth->modify('last day of this month')->setTime(23, 59, 59);

            $monthPeriods[] = [
                'start' => $monthStart,
                'end' => $monthEnd,
            ];
        }

        $rangeStart = $monthPeriods[0]['start'];
        $rangeEnd = $monthPeriods[count($monthPeriods) - 1]['end'];

        $boardPosts = $this->boardService->getPostsBetween($rangeStart, $rangeEnd);
        $blogPosts = $this->blogService->getPostsBetween($rangeStart, $rangeEnd);

        $entriesByDate = [];

        foreach ($boardPosts as $post) {
            $dateKey = (new \DateTimeImmutable($post->created_at))->format('Y-m-d');
            $entriesByDate[$dateKey][] = [
                'type' => 'board',
                'title' => $post->title,
            ];
        }

        foreach ($blogPosts as $post) {
            $dateKey = (new \DateTimeImmutable($post->created_at))->format('Y-m-d');
            $entriesByDate[$dateKey][] = [
                'type' => 'blog',
                'title' => $post->title,
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
            ];
        }

        return [
            'months' => $months,
            'icons' => [
                'board' => '🗂',
                'blog' => '✍️',
            ],
            'weekdayLabels' => ['일', '월', '화', '수', '목', '금', '토'],
        ];
    }
}
