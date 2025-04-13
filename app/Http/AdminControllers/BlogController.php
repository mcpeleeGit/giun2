<?php

namespace App\Http\AdminControllers;

use App\Services\BlogService;
use App\Http\AdminControllers\Common\Controller;
use App\Models\Post;

class BlogController extends Controller {
    private $blogService;

    public function __construct() {
        $this->blogService = new BlogService();
    }

    public function index() {
        // 게시물 목록 가져오기
        $posts = $this->blogService->getAll();

        // 게시물 목록을 뷰에 전달
        adminView('posts', ['posts' => $posts]);
    }

    public function create() {
        // POST 데이터 가져오기
        $post = new Post();
        $this->mapRequestToObject($post, $_POST);

        // 게시물 생성
        $this->blogService->createPost($post);

        // 게시물 목록 페이지로 리다이렉트
        header('Location: /admin/posts');
        exit;
    }

    public function update() {
        // POST 데이터 가져오기
        $post = new Post();
        $this->mapRequestToObject($post, $_POST);

        // 게시물 업데이트
        $this->blogService->updatePost($post);

        // 게시물 목록 페이지로 리다이렉트
        header('Location: /admin/posts');
        exit;
    }

    public function delete() {
        $postId = $_POST['id'] ?? null;
        if ($postId) {
            $success = $this->blogService->deletePost($postId);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid post ID']);
        }

        // 게시물 목록 페이지로 리다이렉트
        header('Location: /admin/posts');
        exit;
    }
} 