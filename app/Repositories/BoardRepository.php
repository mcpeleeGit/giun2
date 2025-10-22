<?php

namespace App\Repositories;

use App\Models\BoardPost;
use App\Repositories\Common\Repository;
use PDO;
use PDOException;

class BoardRepository extends Repository
{
    private $tableEnsured = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function findAll(): array
    {
        return $this->withTableRetry(function () {
            $stmt = $this->pdo->query('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC');
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([$this, 'hydratePost'], $rows);
        }, []);
    }

    public function findRecent(int $limit): array
    {
        return $this->withTableRetry(function () use ($limit) {
            $stmt = $this->pdo->prepare('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC LIMIT :limit');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([$this, 'hydratePost'], $rows);
        }, []);
    }

    public function findByUser(int $userId): array
    {
        return $this->withTableRetry(function () use ($userId) {
            $stmt = $this->pdo->prepare('SELECT b.*, u.name AS user_name FROM board_posts b INNER JOIN users u ON b.user_id = u.id WHERE b.user_id = :user_id ORDER BY b.created_at DESC');
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([$this, 'hydratePost'], $rows);
        }, []);
    }

    public function create(int $userId, string $title, string $content): bool
    {
        return $this->withTableRetry(function () use ($userId, $title, $content) {
            $stmt = $this->pdo->prepare('INSERT INTO board_posts (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())');
            return $stmt->execute([$userId, $title, $content]);
        }, false);
    }

    private function hydratePost(array $row): BoardPost
    {
        $post = new BoardPost();
        $this->mapDataToObject($row, $post);
        return $post;
    }

    private function withTableRetry(callable $operation, $fallback)
    {
        try {
            return $operation();
        } catch (PDOException $e) {
            if ($this->isMissingTableError($e) && $this->ensureTableExists()) {
                return $operation();
            }

            if ($this->isMissingTableError($e)) {
                return $fallback;
            }

            throw $e;
        }
    }

    private function isMissingTableError(PDOException $e): bool
    {
        return $e->getCode() === '42S02';
    }

    private function ensureTableExists(): bool
    {
        if ($this->tableEnsured) {
            return false;
        }

        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS board_posts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL,
                INDEX idx_board_posts_user_id (user_id),
                CONSTRAINT fk_board_posts_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }
}
