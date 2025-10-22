<?php
namespace App\Http\Controllers;

use App\Services\BoardService;
use App\Services\TodoService;

class HomeController {
    private $boardService;
    private $todoService;

    public function __construct()
    {
        $this->boardService = new BoardService();
        $this->todoService = new TodoService();
    }

    public function home() {
        $currentUser = current_user();

        view('home', [
            'currentUser' => $currentUser,
            'recentPosts' => $this->boardService->getRecentPosts(3),
            'recentTodos' => $currentUser ? $this->todoService->getRecentTodos($currentUser->id, 3) : [],
            'message' => flash('home_message'),
            'notice' => flash('auth_notice'),
        ]);
    }
}
