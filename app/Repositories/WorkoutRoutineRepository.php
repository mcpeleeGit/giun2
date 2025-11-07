<?php

namespace App\Repositories;

use App\Models\WorkoutRoutine;
use App\Repositories\Common\Repository;
use PDO;
use PDOException;

class WorkoutRoutineRepository extends Repository
{
    private $tableEnsured = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getByUser(int $userId): array
    {
        return $this->withTableRetry(function () use ($userId) {
            $stmt = $this->pdo->prepare('SELECT * FROM workout_routines WHERE user_id = :user_id ORDER BY day_of_week ASC');
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                $routine = new WorkoutRoutine();
                $this->mapDataToObject($row, $routine);
                $routine->day_of_week = (int)$row['day_of_week'];
                return $routine;
            }, $rows);
        }, []);
    }

    /**
     * @param int   $userId
     * @param array $entries [[day_of_week => int, activity => string], ...]
     */
    public function replaceAllForUser(int $userId, array $entries): bool
    {
        return $this->withTableRetry(function () use ($userId, $entries) {
            $this->pdo->beginTransaction();

            try {
                $deleteStmt = $this->pdo->prepare('DELETE FROM workout_routines WHERE user_id = :user_id');
                $deleteStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                $deleteStmt->execute();

                if (!empty($entries)) {
                    $insertStmt = $this->pdo->prepare(
                        'INSERT INTO workout_routines (user_id, day_of_week, activity, created_at, updated_at)
                         VALUES (:user_id, :day_of_week, :activity, NOW(), NOW())'
                    );

                    foreach ($entries as $entry) {
                        $insertStmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
                        $insertStmt->bindValue(':day_of_week', $entry['day_of_week'], PDO::PARAM_INT);
                        $insertStmt->bindValue(':activity', $entry['activity'], PDO::PARAM_STR);
                        $insertStmt->execute();
                    }
                }

                $this->pdo->commit();
            } catch (\Throwable $e) {
                $this->pdo->rollBack();
                throw $e;
            }

            return true;
        }, false);
    }

    private function withTableRetry(callable $operation, $fallback)
    {
        try {
            return $operation();
        } catch (PDOException $e) {
            if ($this->isMissingTableError($e) && $this->ensureTableExists()) {
                return $operation();
            }

            if ($this->isMissingTableError($e)) {
                return $fallback;
            }

            throw $e;
        }
    }

    private function isMissingTableError(PDOException $e): bool
    {
        return $e->getCode() === '42S02';
    }

    private function ensureTableExists(): bool
    {
        if ($this->tableEnsured) {
            return false;
        }

        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS workout_routines (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                day_of_week TINYINT NOT NULL,
                activity VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL,
                UNIQUE KEY uniq_workout_user_day (user_id, day_of_week),
                CONSTRAINT fk_workout_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }
}
