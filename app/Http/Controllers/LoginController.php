<?php

namespace App\Http\Controllers;

use App\Services\KakaoService;
use App\Services\LoginService;

class LoginController {
    private $loginService;

    public function __construct()
    {
        $this->loginService = new LoginService(); // ✅ 생성자에서 생성
    }


    public function login() {
        view('login', [
            'error' => flash('auth_error'),
            'notice' => flash('auth_notice'),
            'kakaoLoginEnabled' => KakaoService::isConfigured(),
        ]);
    }

    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($email === '' || $password === '') {
                flash('auth_error', '이메일과 비밀번호를 모두 입력해 주세요.');
                redirect('/login');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('auth_error', '유효한 이메일 주소인지 다시 확인해 주세요.');
                redirect('/login');
            }

            $user = $this->loginService->login($email, $password);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['user'] = serialize($user); // User 객체를 직렬화하여 세션에 저장
                if (($user->role ?? null) === 'ADMIN') {
                    flash('admin_notice', $user->name . '님, 안전한 관리자 페이지에 접속했습니다.');
                    redirect('/admin');
                }
                flash('mypage_notice', $user->name . '님, 환영합니다! 오늘의 계획을 완성해 볼까요?');
                redirect('/mypage');
            } else {
                flash('auth_error', '이메일 또는 비밀번호가 올바르지 않습니다. 다시 확인해 주세요.');
                redirect('/login');
            }
        }
    }
}
