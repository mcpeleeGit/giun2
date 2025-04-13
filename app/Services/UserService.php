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

    public function deleteUser($userId) {
        // 사용자 삭제
        return $this->userRepository->delete($userId);
    }
}
