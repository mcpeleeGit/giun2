<?php

namespace App\Services;

use App\Repositories\BoardRepository;

class BoardService
{
    private $boardRepository;

    public function __construct()
    {
        $this->boardRepository = new BoardRepository();
    }

    public function getAllPosts(): array
    {
        return $this->boardRepository->findAll();
    }

    public function getRecentPosts(int $limit = 3): array
    {
        return $this->boardRepository->findRecent($limit);
    }

    public function getPostsBetween(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->boardRepository->findBetween($start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s'));
    }

    public function getPostsByUser(int $userId): array
    {
        return $this->boardRepository->findByUser($userId);
    }

    public function createPost(int $userId, string $title, string $content): bool
    {
        return $this->boardRepository->create($userId, $title, $content);
    }

    public function updatePost(int $postId, int $userId, string $title, string $content): bool
    {
        return $this->boardRepository->update($postId, $userId, $title, $content);
    }

    public function deletePost(int $postId, int $userId): bool
    {
        return $this->boardRepository->delete($postId, $userId);
    }

    public function deletePostAsAdmin(int $postId): bool
    {
        return $this->boardRepository->deleteAsAdmin($postId);
    }
}
