<?php

namespace App\Http\Controllers;

use App\Services\LoginService;

class LoginController {
    private $loginService;

    public function __construct()
    {
        $this->loginService = new LoginService(); // ✅ 생성자에서 생성
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $user = $this->loginService->login($email, $password);

            if ($user) {
                session_start();
                $_SESSION['user'] = serialize($user); // User 객체를 직렬화하여 세션에 저장
                header("Location: /");
                exit;
            } else {
                echo "<p>로그인 실패. 이메일 또는 비밀번호를 확인하세요.</p>";
            }
        }
    }
}
