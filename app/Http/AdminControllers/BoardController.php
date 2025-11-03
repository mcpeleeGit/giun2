<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Services\BoardService;

class BoardController extends Controller
{
    private BoardService $boardService;

    public function __construct()
    {
        parent::__construct();
        $this->boardService = new BoardService();
    }

    public function index(): void
    {
        $posts = $this->boardService->getAllPosts();

        adminView('board', [
            'posts' => $posts,
            'admin' => $this->adminUser,
        ]);
    }

    public function delete(): void
    {
        $this->ensurePostWithCsrf();

        $postId = $this->getPostInt('id');
        if ($postId === null) {
            $this->redirectWithError('/admin/board', '잘못된 요청입니다.');
        }

        if ($this->boardService->deletePostAsAdmin($postId)) {
            $this->redirectWithSuccess('/admin/board', '게시글이 삭제되었습니다.');
        }

        $this->redirectWithError('/admin/board', '게시글을 삭제하지 못했습니다. 다시 시도해 주세요.');
    }
}
