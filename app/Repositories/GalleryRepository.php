<?php

namespace App\Repositories;

use App\Repositories\Common\Repository;
use App\Models\Gallery;
use PDOException;

class GalleryRepository extends Repository {
    private $tableEnsured = false;

    public function getAll() {
        return $this->withTableRetry(function () {
            $stmt = $this->pdo->query("SELECT * FROM gallery_items ORDER BY created_at DESC");
            $galleryData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $galleryItems = [];

            foreach ($galleryData as $data) {
                $gallery = new Gallery();
                $galleryItems[] = $this->mapDataToObject($data, $gallery);
            }

            return $galleryItems;
        }, []);
    }

    public function getById($id) {
        return $this->withTableRetry(function () use ($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM gallery_items WHERE id = ?");
            $stmt->execute([$id]);
            $galleryData = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($galleryData) {
                $gallery = new Gallery();
                return $this->mapDataToObject($galleryData, $gallery);
            }

            return null;
        }, null);
    }

    public function create($gallery) {
        return $this->withTableRetry(function () use ($gallery) {
            $stmt = $this->pdo->prepare("INSERT INTO gallery_items (title, description, image_path, author, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt->execute([$gallery->title, $gallery->description, $gallery->image_path, $gallery->author]);
        }, false);
    }

    public function update($gallery) {
        return $this->withTableRetry(function () use ($gallery) {
            $stmt = $this->pdo->prepare("UPDATE gallery_items SET title = ?, description = ?, image_path = ?, author = ? WHERE id = ?");
            return $stmt->execute([$gallery->title, $gallery->description, $gallery->image_path, $gallery->author, $gallery->id]);
        }, false);
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

        // Ensure users table exists first for FK constraint (if needed in future)
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
            CREATE TABLE IF NOT EXISTS gallery_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                image_path VARCHAR(500) NOT NULL,
                author VARCHAR(100) NOT NULL,
                is_twitter TINYINT(1) DEFAULT 0,
                is_threads TINYINT(1) DEFAULT 0,
                is_blog TINYINT(1) DEFAULT 0,
                is_facebook TINYINT(1) DEFAULT 0,
                is_instagram TINYINT(1) DEFAULT 0,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }

}