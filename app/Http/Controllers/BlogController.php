<?php
namespace App\Http\Controllers;

use App\Services\BlogService;

class BlogController
{
    protected $service;

    public function __construct()
    {
        $this->service = new BlogService();
    }

    public function index()
    {
        $posts = $this->service->getAll();
        return view('blog/index', ['posts' => $posts]);
    }

    // 선택적으로 상세 페이지용 메서드도 만들 수 있어
    public function show($id)
    {
        $post = $this->service->getById($id);
        return view('blog-detail', ['post' => $post]);
    }
}
