<?php

namespace App\Repositories;

use App\Models\Seo;
use App\Repositories\Common\Repository;
use PDO;
use PDOException;

class SeoRepository extends Repository
{
    private bool $tableEnsured = false;

    public function findByPath($path): ?Seo
    {
        return $this->withTableRetry(function () use ($path) {
            $stmt = $this->pdo->prepare('SELECT * FROM seo WHERE path = ? LIMIT 1');
            $stmt->execute([$path]);
            $seoData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($seoData) {
                $seo = new Seo();
                return $this->mapDataToObject($seoData, $seo);
            }

            return null;
        });
    }

    private function withTableRetry(callable $operation)
    {
        try {
            return $operation();
        } catch (PDOException $e) {
            if ($this->isMissingTableError($e) && $this->ensureTableExists()) {
                return $operation();
            }

            if ($this->isMissingTableError($e)) {
                return null;
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
            CREATE TABLE IF NOT EXISTS seo (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                path VARCHAR(255) NOT NULL UNIQUE,
                title VARCHAR(255) NULL,
                description TEXT NULL,
                image VARCHAR(255) NULL,
                url VARCHAR(255) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NULL DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }
}
