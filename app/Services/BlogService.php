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
}
