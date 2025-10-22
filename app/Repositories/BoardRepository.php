<?php

namespace App\Repositories;

use App\Models\BoardPost;
use App\Repositories\Common\Repository;
use PDO;

class BoardRepository extends Repository
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'hydratePost'], $rows);
    }

    public function findRecent(int $limit): array
    {
        $stmt = $this->pdo->prepare('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'hydratePost'], $rows);
    }

    public function findByUser(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id WHERE b.user_id = :user_id ORDER BY b.created_at DESC');
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([$this, 'hydratePost'], $rows);
    }

    public function create(int $userId, string $title, string $content): bool
    {
        $stmt = $this->pdo->prepare('INSERT INTO board_posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())');
        return $stmt->execute([$userId, $title, $content]);
    }

    private function hydratePost(array $row): BoardPost
    {
        $post = new BoardPost();
        $this->mapDataToObject($row, $post);
        return $post;
    }
}
