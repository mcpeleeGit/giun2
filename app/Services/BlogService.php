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

    public function getPostsBetween(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->blogRepository->findBetween($start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'));
    }

    public function getForUser(int $userId, string $authorName): array
    {
        return $this->blogRepository->findByUser($userId, $authorName);
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

    public function updatePostForUser($post, string $authorName): bool
    {
        return $this->blogRepository->updateForUser($post, $authorName);
    }

    public function deletePost($id) {
        return $this->blogRepository->delete($id);
    }

    public function deletePostForUser(int $id, int $userId, string $authorName): bool
    {
        return $this->blogRepository->deleteForUser($id, $userId, $authorName);
    }
}
