<?php
namespace App\Repositories;

use App\Models\User;
use App\Repositories\Common\Repository;

class UserRepository extends Repository {

    public function __construct() {
        parent::__construct(); // 부모 클래스의 생성자 호출
    }

    public function create($name, $email, $password) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email, $password]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData) {
            $user = new User();
            return $this->mapDataToObject($userData, $user);
        }

        return null;
    }

    public function findAll() {
        $stmt = $this->pdo->query("SELECT * FROM users");
        $usersData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $users = [];

        foreach ($usersData as $userData) {
            $user = new User();
            $users[] = $this->mapDataToObject($userData, $user);
        }

        return $users;
    }

    public function delete($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
}
