<?php
namespace App\Services;

use App\Repositories\BlogRepository;

class BlogService
{
    protected $blogRepository;

    public function __construct()
    {
        $this->blogRepository = new BlogRepository();
    }

    public function getAll()
    {
        return $this->blogRepository->getAll();
    }

    public function getById($id)
    {
        return $this->blogRepository->getById($id);
    }

    public function createPost($post) {
        // 게시물 생성
        return $this->blogRepository->create($post);
    }

    public function updatePost($post) {
        // 게시물 업데이트
        return $this->blogRepository->update($post);
    }

    public function deletePost($id) {
        return $this->blogRepository->delete($id);
    }
}
