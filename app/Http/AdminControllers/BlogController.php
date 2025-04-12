<?php

namespace App\Http\AdminControllers;

use App\Services\BlogService;
use App\Http\AdminControllers\Common\Controller;

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
} 