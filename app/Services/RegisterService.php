<?php
namespace App\Services;

use App\Repositories\UserRepository;

class RegisterService
{
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function register($name, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->userRepository->create($name, $email, $hash, null);
    }
}