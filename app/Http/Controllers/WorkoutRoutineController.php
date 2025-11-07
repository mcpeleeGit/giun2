<?php

namespace App\Http\Controllers;

use App\Services\WorkoutRoutineService;

class WorkoutRoutineController
{
    private $workoutRoutineService;

    public function __construct()
    {
        $this->workoutRoutineService = new WorkoutRoutineService();
    }

    public function save()
    {
        $user = require_login();

        require_csrf_token($_POST['csrf_token'] ?? null);

        $routines = $_POST['routines'] ?? [];
        if (!is_array($routines)) {
            flash('workout_error', '요청 형식이 올바르지 않습니다. 다시 시도해 주세요.');
            redirect('/');
        }

        $entries = [];
        foreach (range(0, 6) as $dayIndex) {
            $value = $routines[$dayIndex] ?? '';
            if (!is_string($value)) {
                continue;
            }

            $activity = trim($value);
            if ($activity === '') {
                continue;
            }

            if (function_exists('mb_strimwidth')) {
                $activity = mb_strimwidth($activity, 0, 255, '', 'UTF-8');
            } elseif (strlen($activity) > 255) {
                $activity = substr($activity, 0, 255);
            }

            $entries[] = [
                'day_of_week' => $dayIndex,
                'activity' => $activity,
            ];
        }

        try {
            if ($this->workoutRoutineService->replaceUserRoutines($user->id, $entries)) {
                flash('workout_message', '주간 운동 루틴이 저장되었습니다.');
            } else {
                flash('workout_error', '운동 루틴을 저장하지 못했습니다. 잠시 후 다시 시도해 주세요.');
            }
        } catch (\Throwable $e) {
            error_log('Workout routine save failed: ' . $e->getMessage());
            flash('workout_error', '운동 루틴을 저장하는 중 문제가 발생했습니다.');
        }

        redirect('/');
    }
}
