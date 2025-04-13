<?php
namespace App\Http\Controllers;

use App\Services\BlogService;
use Exception;

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

        // 게시물 목록을 뷰에 전달
        return view('blog/index', ['posts' => $posts]);
    }

    public function show($id)
    {
        $post = $this->service->getById($id);
        return view('blog/detail', ['post' => $post]);
    }

    public function getById($id)
    {
        try {
            $post = $this->service->getById($id);
            if ($post) {
                echo json_encode($post);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Post not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred']);
        }
    }
}
