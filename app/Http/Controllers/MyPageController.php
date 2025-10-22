<?php

namespace App\Http\Controllers;

use App\Services\BoardService;
use App\Services\TodoService;

class MyPageController
{
    private $todoService;
    private $boardService;

    public function __construct()
    {
        $this->todoService = new TodoService();
        $this->boardService = new BoardService();
    }

    public function index()
    {
        $user = require_login();
        $todos = $this->todoService->getTodosForUser($user->id);
        $posts = $this->boardService->getPostsByUser($user->id);

        $completed = array_reduce($todos, function ($carry, $todo) {
            return $carry + ($todo->is_completed ? 1 : 0);
        }, 0);

        view('mypage', [
            'user' => $user,
            'todoStats' => [
                'total' => count($todos),
                'completed' => $completed,
                'pending' => count($todos) - $completed
            ],
            'recentTodos' => array_slice($todos, 0, 3),
            'recentPosts' => array_slice($posts, 0, 3),
            'notice' => flash('mypage_notice'),
        ]);
    }
}
