<?php

namespace App\Repositories;

use App\Models\Todo;
use App\Repositories\Common\Repository;
use PDO;

class TodoRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM todos WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            $todo = new Todo();
            $this->mapDataToObject($row, $todo);
            $todo->is_completed = (bool)$row['is_completed'];
            return $todo;
        }, $rows);
    }

    public function getRecentByUser(int $userId, int $limit): array
    {
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
    }

    public function create(int $userId, string $title): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO todos (user_id, title, is_completed, created_at) VALUES (?, ?, 0, NOW())');
        return $stmt->execute([$userId, $title]);
    }

    public function toggle(int $todoId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE todos SET is_completed = CASE WHEN is_completed = 1 THEN 0 ELSE 1 END WHERE id = ? AND user_id = ?');
        $stmt->execute([$todoId, $userId]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $todoId, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?');
        $stmt->execute([$todoId, $userId]);
        return $stmt->rowCount() > 0;
    }
}
