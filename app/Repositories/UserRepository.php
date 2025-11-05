<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Common\Repository;

class UserRepository extends Repository {

    public function __construct() {
        parent::__construct(); // 부모 클래스의 생성자 호출
        $this->ensureKakaoColumnExists();
    }

    public function create($name, $email, $password, ?string $kakaoId = null) {
        try {
            $role = $email === 'admin@googsu.com' ? 'ADMIN' : 'USER';
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role, kakao_id) VALUES (?, ?, ?, ?, ?)");
            return $stmt->execute([$name, $email, $password, $role, $kakaoId]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $this->ensureAdminRole($userData);
            $user = new User();
            return $this->mapDataToObject($userData, $user);
        }

        return null;
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $this->ensureAdminRole($userData);
            $user = new User();
            return $this->mapDataToObject($userData, $user);
        }

        return null;
    }

    public function findByKakaoId(string $kakaoId): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE kakao_id = ?");
        $stmt->execute([$kakaoId]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $this->ensureAdminRole($userData);
            $user = new User();
            return $this->mapDataToObject($userData, $user);
        }

        return null;
    }

    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        $usersData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];

        foreach ($usersData as $userData) {
            $this->ensureAdminRole($userData);
            $user = new User();
            $users[] = $this->mapDataToObject($userData, $user);
        }

        return $users;
    }

    public function update($userId, $name, $email, ?string $passwordHash = null) {
        if ($passwordHash !== null) {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $params = [$name, $email, $passwordHash, $userId];
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $params = [$name, $email, $userId];
        }

        $updated = $stmt->execute($params);

        if ($updated && $email === 'admin@googsu.com') {
            $this->updateRole($userId, 'ADMIN');
        }

        return $updated;
    }

    public function delete($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function updateRole(int $userId, string $role): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        return $stmt->execute([$role, $userId]);
    }

    public function updateKakaoId(int $userId, ?string $kakaoId): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET kakao_id = ? WHERE id = ?");
        return $stmt->execute([$kakaoId, $userId]);
    }

    private function ensureAdminRole(array &$userData): void
    {
        if (($userData['email'] ?? null) === 'admin@googsu.com' && ($userData['role'] ?? null) !== 'ADMIN') {
            $this->updateRole((int)$userData['id'], 'ADMIN');
            $userData['role'] = 'ADMIN';
        }
    }

    private function ensureKakaoColumnExists(): void
    {
        try {
            $stmt = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'kakao_id'");
            $columnExists = $stmt->fetch();

            if (!$columnExists) {
                $this->pdo->exec("ALTER TABLE users ADD COLUMN kakao_id VARCHAR(64) UNIQUE NULL AFTER password");
            }
        } catch (\PDOException $e) {
            error_log('Failed to ensure kakao_id column exists: ' . $e->getMessage());
        }
    }
}
