<?php

namespace App\Services;

use App\Repositories\UserRepository;

class UserService {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function getAllUsers() {
        // 모든 사용자 목록을 가져옵니다.
        return $this->userRepository->findAll();
    }

    public function getUserById(int $userId)
    {
        return $this->userRepository->findById($userId);
    }

    public function updateProfile(int $userId, string $name, string $email, ?string $password = null)
    {
        $existingUser = $this->userRepository->findByEmail($email);

        if ($existingUser && $existingUser->id !== $userId) {
            return null;
        }

        $passwordHash = null;
        if ($password !== null) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        }

        $updated = $this->userRepository->update($userId, $name, $email, $passwordHash);

        if (!$updated) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }

    public function deleteUser($userId) {
        // 사용자 삭제
        return $this->userRepository->delete($userId);
    }
}
