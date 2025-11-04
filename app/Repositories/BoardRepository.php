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

    public function update(int $postId, int $userId, string $title, string $content): bool
    {
        return $this->withTableRetry(function () use ($postId, $userId, $title, $content) {
            $stmt = $this->pdo->prepare('UPDATE board_posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
            $stmt->execute([$title, $content, $postId, $userId]);
            return $stmt->rowCount() > 0;
        }, false);
    }

    public function delete(int $postId, int $userId): bool
    {
        return $this->withTableRetry(function () use ($postId, $userId) {
            $stmt = $this->pdo->prepare('DELETE FROM board_posts WHERE id = ? AND user_id = ?');
            $stmt->execute([$postId, $userId]);
            return $stmt->rowCount() > 0;
        }, false);
    }

    public function deleteAsAdmin(int $postId): bool
    {
        return $this->withTableRetry(function () use ($postId) {
            $stmt = $this->pdo->prepare('DELETE FROM board_posts WHERE id = ?');
            $stmt->execute([$postId]);
            return $stmt->rowCount() > 0;
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

        // Ensure users table exists first for FK constraint
        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(20) NOT NULL DEFAULT 'USER',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->pdo->exec(<<<SQL
            CREATE TABLE IF NOT EXISTS board_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
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
