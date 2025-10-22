<?php

namespace App\Http\Controllers;

use App\Services\TodoService;

class TodoController
{
    private $todoService;

    public function __construct()
    {
        $this->todoService = new TodoService();
    }

    public function index()
    {
        $currentUser = current_user();
        $todos = $currentUser ? $this->todoService->getTodosForUser($currentUser->id) : [];

        view('todo/index', [
            'currentUser' => $currentUser,
            'todos' => $todos,
            'message' => flash('todo_message'),
            'error' => flash('todo_error')
        ]);
    }

    public function store()
    {
        $user = require_login();

        $title = trim($_POST['title'] ?? '');

        if ($title === '') {
            flash('todo_error', '할 일 내용을 입력해 주세요.');
            redirect('/todo');
        }

        $created = $this->todoService->createTodo($user->id, $title);

        if ($created) {
            flash('todo_message', '새로운 할 일이 추가되었습니다.');
        } else {
            flash('todo_error', '할 일을 추가하는 중 문제가 발생했습니다. 잠시 후 다시 시도해 주세요.');
        }

        redirect('/todo');
    }

    public function toggle($todoId)
    {
        $user = require_login();

        if ($this->todoService->toggleTodo((int)$todoId, $user->id)) {
            flash('todo_message', '할 일 상태가 업데이트되었습니다.');
        } else {
            flash('todo_error', '요청하신 할 일을 찾을 수 없습니다.');
        }

        redirect('/todo');
    }

    public function delete($todoId)
    {
        $user = require_login();

        if ($this->todoService->deleteTodo((int)$todoId, $user->id)) {
            flash('todo_message', '할 일이 삭제되었습니다.');
        } else {
            flash('todo_error', '삭제할 할 일을 찾을 수 없습니다.');
        }

        redirect('/todo');
    }
}
