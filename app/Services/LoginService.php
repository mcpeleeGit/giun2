<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;

class LoginService {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function login($email, $password) {
        $user = $this->userRepository->findByEmail($email);

        if ($user && password_verify($password, $user->password)) {
            return $user;
        }

        return null;
    }
}
