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
}
