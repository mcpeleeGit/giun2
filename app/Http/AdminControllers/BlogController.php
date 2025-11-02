<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Models\Post;
use App\Services\BlogService;

class BlogController extends Controller
{
    private BlogService $blogService;

    public function __construct()
    {
        parent::__construct();
        $this->blogService = new BlogService();
    }

    public function index(): void
    {
        $posts = $this->blogService->getAll();

        adminView('posts', [
            'posts' => $posts,
            'admin' => $this->adminUser,
        ]);
    }

    public function create(): void
    {
        $this->ensurePostWithCsrf();

        $title = $this->getPostString('title');
        $author = $this->getPostString('author');
        $content = $this->getPostString('content');

        if ($title === null || $author === null || $content === null) {
            $this->redirectWithError('/admin/posts', '모든 필드를 입력해 주세요.');
        }

        $post = new Post();
        $post->title = $title;
        $post->author = $author;
        $post->content = $content;

        if (!$this->blogService->createPost($post)) {
            $this->redirectWithError('/admin/posts', '게시물 등록 중 오류가 발생했습니다.');
        }

        $this->redirectWithSuccess('/admin/posts', '게시물이 등록되었습니다.');
    }

    public function update(): void
    {
        $this->ensurePostWithCsrf();

        $id = $this->getPostInt('id');
        $title = $this->getPostString('title');
        $author = $this->getPostString('author');
        $content = $this->getPostString('content');

        if ($id === null || $title === null || $author === null || $content === null) {
            $this->redirectWithError('/admin/posts', '입력값을 다시 확인해 주세요.');
        }

        $post = new Post();
        $post->id = $id;
        $post->title = $title;
        $post->author = $author;
        $post->content = $content;

        if (!$this->blogService->updatePost($post)) {
            $this->redirectWithError('/admin/posts', '게시물 수정 중 오류가 발생했습니다.');
        }

        $this->redirectWithSuccess('/admin/posts', '게시물이 수정되었습니다.');
    }

    public function delete(): void
    {
        $this->ensurePostWithCsrf();

        $postId = $this->getPostInt('id');

        if ($postId === null) {
            $this->redirectWithError('/admin/posts', '잘못된 요청입니다.');
        }

        if (!$this->blogService->deletePost($postId)) {
            $this->redirectWithError('/admin/posts', '게시물 삭제에 실패했습니다.');
        }

        $this->redirectWithSuccess('/admin/posts', '게시물이 삭제되었습니다.');
    }
}
