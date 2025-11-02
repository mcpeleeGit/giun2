<?php

namespace App\Http\Controllers;

use App\Services\BoardService;

class BoardController
{
    private $boardService;

    public function __construct()
    {
        $this->boardService = new BoardService();
    }

    public function index()
    {
        view('board/index', [
            'currentUser' => current_user(),
            'posts' => $this->boardService->getAllPosts(),
            'message' => flash('board_message'),
            'error' => flash('board_error')
        ]);
    }

    public function store()
    {
        $user = require_login();

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            flash('board_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/board');
        }

        $created = $this->boardService->createPost($user->id, $title, $content);

        if ($created) {
            flash('board_message', '게시글이 등록되었습니다. 소중한 이야기를 나눠주셔서 감사합니다!');
        } else {
            flash('board_error', '게시글을 등록하는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
        }

        redirect('/board');
    }

    public function update($postId)
    {
        $user = require_login();

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if ($title === '' || $content === '') {
            flash('board_error', '제목과 내용을 모두 입력해 주세요.');
            redirect('/board');
        }

        if ($this->boardService->updatePost((int)$postId, $user->id, $title, $content)) {
            flash('board_message', '게시글이 수정되었습니다.');
        } else {
            flash('board_error', '게시글을 수정할 수 없습니다. 다시 시도해 주세요.');
        }

        redirect('/board');
    }

    public function delete($postId)
    {
        $user = require_login();

        if ($this->boardService->deletePost((int)$postId, $user->id)) {
            flash('board_message', '게시글이 삭제되었습니다.');
        } else {
            flash('board_error', '게시글을 삭제할 수 없습니다.');
        }

        redirect('/board');
    }
}
