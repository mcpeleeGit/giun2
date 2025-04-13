<?php
namespace App\Http\Controllers;

use App\Services\RegisterService;

class RegisterController
{
    private $registerService;

    public function __construct()
    {
        $this->registerService = new RegisterService(); // ✅ 생성자에서 생성
    }

    public function register() { 
        view('register'); 
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $success = $this->registerService->register($name, $email, $password);

            if ($success) {
                header("Location: /login");
                exit;
            } else {
                echo "<p>회원가입 실패. 다시 시도하세요.</p>";
            }
        }
    }
}
