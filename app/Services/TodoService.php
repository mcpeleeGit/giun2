<?php

namespace App\Services;

use App\Repositories\TodoRepository;

class TodoService
{
    private $todoRepository;

    public function __construct()
    {
        $this->todoRepository = new TodoRepository();
    }

    public function getTodosForUser(int $userId): array
    {
        return $this->todoRepository->getByUser($userId);
    }

    public function getRecentTodos(int $userId, int $limit = 3): array
    {
        return $this->todoRepository->getRecentByUser($userId, $limit);
    }

    public function getTodosBetween(int $userId, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->todoRepository->findBetweenByUser(
            $userId,
            $start->format('Y-m-d H:i:s'),
            $end->format('Y-m-d H:i:s')
        );
    }

    public function createTodo(int $userId, string $title): bool
    {
        return $this->todoRepository->create($userId, $title);
    }

    public function updateTodo(int $todoId, int $userId, string $title): bool
    {
        return $this->todoRepository->update($todoId, $userId, $title);
    }

    public function toggleTodo(int $todoId, int $userId): bool
    {
        return $this->todoRepository->toggle($todoId, $userId);
    }

    public function deleteTodo(int $todoId, int $userId): bool
    {
        return $this->todoRepository->delete($todoId, $userId);
    }

    /**
     * @param array $entries [[day_of_week => int, activity => string], ...]
     */
    public function createWorkoutTodos(int $userId, array $entries): int
    {
        $weekdayLabels = [
            0 => '월요일',
            1 => '화요일',
            2 => '수요일',
            3 => '목요일',
            4 => '금요일',
            5 => '토요일',
            6 => '일요일',
        ];

        $createdCount = 0;
        $now = new \DateTimeImmutable('now');

        foreach ($entries as $entry) {
            $dayIndex = isset($entry['day_of_week']) ? (int)$entry['day_of_week'] : null;
            $activity = isset($entry['activity']) ? trim((string)$entry['activity']) : '';

            if ($activity === '' || !array_key_exists($dayIndex, $weekdayLabels)) {
                continue;
            }

            $normalizedActivity = preg_replace('/\s+/u', ' ', $activity);
            if (!is_string($normalizedActivity) || $normalizedActivity === '') {
                $normalizedActivity = $activity;
            }

            $title = $weekdayLabels[$dayIndex] . ' 운동 루틴: ' . $normalizedActivity;

            if (function_exists('mb_strimwidth')) {
                $title = mb_strimwidth($title, 0, 255, '', 'UTF-8');
            } elseif (strlen($title) > 255) {
                $title = substr($title, 0, 255);
            }

            if ($title === '') {
                continue;
            }

            $scheduledDate = $this->getUpcomingDateForWeekday($dayIndex, $now);

            if ($this->todoRepository->create($userId, $title, $scheduledDate)) {
                $createdCount++;
            }
        }

        return $createdCount;
    }

    private function getUpcomingDateForWeekday(int $targetWeekday, \DateTimeImmutable $reference): \DateTimeImmutable
    {
        $currentWeekday = ((int)$reference->format('N')) - 1; // Monday=0
        $daysToAdd = ($targetWeekday - $currentWeekday + 7) % 7;

        if ($daysToAdd === 0) {
            return $reference;
        }

        return $reference->modify(sprintf('+%d days', $daysToAdd));
    }
}
