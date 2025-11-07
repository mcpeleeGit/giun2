<?php

namespace App\Services;

use App\Repositories\WorkoutRoutineRepository;

class WorkoutRoutineService
{
    private $workoutRoutineRepository;

    public function __construct()
    {
        $this->workoutRoutineRepository = new WorkoutRoutineRepository();
    }

    public function getRoutineMapForUser(int $userId): array
    {
        $routines = $this->workoutRoutineRepository->getByUser($userId);
        $map = [];

        foreach ($routines as $routine) {
            $map[(int)$routine->day_of_week] = $routine->activity;
        }

        return $map;
    }

    /**
     * @param array $entries [[day_of_week => int, activity => string], ...]
     */
    public function replaceUserRoutines(int $userId, array $entries): bool
    {
        return $this->workoutRoutineRepository->replaceAllForUser($userId, $entries);
    }
}
