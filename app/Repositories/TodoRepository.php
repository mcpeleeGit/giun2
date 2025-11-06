<?php

namespace App\Repositories;

use App\Models\Todo;
use App\Repositories\Common\Repository;
use PDO;
use PDOException;

class TodoRepository extends Repository
{
    private $tableEnsured = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function getByUser(int $userId): array
    {
        return $this->withTableRetry(function () use ($userId) {
            $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC');
            $stmt->execute([$userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                $todo = new Todo();
                $this->mapDataToObject($row, $todo);
                $todo->is_completed = (bool)$row['is_completed'];
                return $todo;
            }, $rows);
        }, []);
    }

    public function getRecentByUser(int $userId, int $limit): array
    {
        return $this->withTableRetry(function () use ($userId, $limit) {
            $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit');
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                $todo = new Todo();
                $this->mapDataToObject($row, $todo);
                $todo->is_completed = (bool)$row['is_completed'];
                return $todo;
            }, $rows);
        }, []);
    }

    public function findBetweenByUser(int $userId, string $startDate, string $endDate): array
    {
        return $this->withTableRetry(function () use ($userId, $startDate, $endDate) {
            $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE user_id = :user_id AND created_at BETWEEN :start AND :end ORDER BY created_at ASC');
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':start', $startDate);
            $stmt->bindValue(':end', $endDate);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($row) {
                $todo = new Todo();
                $this->mapDataToObject($row, $todo);
                $todo->is_completed = (bool)$row['is_completed'];
                return $todo;
            }, $rows);
        }, []);
    }

    public function create(int $userId, string $title): bool
    {
        return $this->withTableRetry(function () use ($userId, $title) {
            $stmt = $this->pdo->prepare('INSERT INTO todos (user_id, title, is_completed, created_at) VALUES (?, ?, 0, NOW())');
            return $stmt->execute([$userId, $title]);
        }, false);
    }

    public function update(int $todoId, int $userId, string $title): bool
    {
        return $this->withTableRetry(function () use ($todoId, $userId, $title) {
            $stmt = $this->pdo->prepare('UPDATE todos SET title = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
            $stmt->execute([$title, $todoId, $userId]);
            return $stmt->rowCount() > 0;
        }, false);
    }

    public function toggle(int $todoId, int $userId): bool
    {
        return $this->withTableRetry(function () use ($todoId, $userId) {
            $stmt = $this->pdo->prepare('UPDATE todos SET is_completed = CASE WHEN is_completed = 1 THEN 0 ELSE 1 END WHERE id = ? AND user_id = ?');
            $stmt->execute([$todoId, $userId]);
            return $stmt->rowCount() > 0;
        }, false);
    }

    public function delete(int $todoId, int $userId): bool
    {
        return $this->withTableRetry(function () use ($todoId, $userId) {
            $stmt = $this->pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
            $stmt->execute([$todoId, $userId]);
            return $stmt->rowCount() > 0;
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
            CREATE TABLE IF NOT EXISTS todos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                is_completed TINYINT(1) NOT NULL DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL,
                INDEX idx_todos_user_id (user_id),
                CONSTRAINT fk_todos_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }
}
