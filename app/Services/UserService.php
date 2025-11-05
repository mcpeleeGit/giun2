<?php

namespace App\Services;

use App\Models\User;
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

    public function getUserByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function getUserByKakaoId(string $kakaoId): ?User
    {
        return $this->userRepository->findByKakaoId($kakaoId);
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

    public function linkKakaoAccount(int $userId, string $kakaoId): ?User
    {
        $existing = $this->userRepository->findByKakaoId($kakaoId);

        if ($existing && $existing->id !== $userId) {
            return null;
        }

        if ($existing && $existing->id === $userId) {
            return $existing;
        }

        if (!$this->userRepository->updateKakaoId($userId, $kakaoId)) {
            return null;
        }

        return $this->userRepository->findById($userId);
    }

    public function createUserWithKakao(string $name, string $email, string $passwordHash, ?string $kakaoId = null): ?User
    {
        if (!$this->userRepository->create($name, $email, $passwordHash, $kakaoId)) {
            return null;
        }

        if ($kakaoId !== null) {
            return $this->userRepository->findByKakaoId($kakaoId);
        }

        return $this->userRepository->findByEmail($email);
    }
}
