<?php

namespace App\Repositories;

use App\Repositories\Common\Repository;
use PDO;
use PDOException;

class AccessLogRepository extends Repository
{
    private $tableEnsured = false;

    public function logAccess(array $data): bool
    {
        return $this->withTableRetry(function () use ($data) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO access_logs (user_id, path, method, ip_address, user_agent, referer, created_at)
                 VALUES (:user_id, :path, :method, :ip_address, :user_agent, :referer, NOW())"
            );

            if ($data['user_id'] !== null) {
                $stmt->bindValue(':user_id', $data['user_id'], PDO::PARAM_INT);
            } else {
                $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
            }

            $stmt->bindValue(':path', $data['path']);
            $stmt->bindValue(':method', $data['method']);
            $stmt->bindValue(':ip_address', $data['ip_address']);
            $stmt->bindValue(':user_agent', $data['user_agent']);
            $stmt->bindValue(':referer', $data['referer']);

            return $stmt->execute();
        }, false);
    }

    public function getDailyCounts(string $startDate): array
    {
        return $this->withTableRetry(function () use ($startDate) {
            $stmt = $this->pdo->prepare(
                "SELECT DATE(created_at) AS visit_date, COUNT(*) AS visit_count
                 FROM access_logs
                 WHERE created_at >= :start_date
                 GROUP BY visit_date
                 ORDER BY visit_date ASC"
            );
            $stmt->bindValue(':start_date', $startDate);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }, []);
    }

    public function getTopPaths(string $startDate, int $limit): array
    {
        return $this->withTableRetry(function () use ($startDate, $limit) {
            $stmt = $this->pdo->prepare(
                "SELECT path, COUNT(*) AS visit_count
                 FROM access_logs
                 WHERE created_at >= :start_date
                 GROUP BY path
                 ORDER BY visit_count DESC, path ASC
                 LIMIT :limit"
            );
            $stmt->bindValue(':start_date', $startDate);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }, []);
    }

    public function getRecentVisits(int $limit): array
    {
        return $this->withTableRetry(function () use ($limit) {
            $stmt = $this->pdo->prepare(
                "SELECT path, method, ip_address, referer, user_agent, created_at
                 FROM access_logs
                 ORDER BY created_at DESC
                 LIMIT :limit"
            );
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }, []);
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
            CREATE TABLE IF NOT EXISTS access_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NULL,
                path VARCHAR(255) NOT NULL,
                method VARCHAR(10) NOT NULL,
                ip_address VARCHAR(45) NULL,
                user_agent VARCHAR(500) NULL,
                referer VARCHAR(500) NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_access_logs_created_at (created_at),
                INDEX idx_access_logs_path (path),
                CONSTRAINT fk_access_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        SQL);

        $this->tableEnsured = true;

        return true;
    }
}
