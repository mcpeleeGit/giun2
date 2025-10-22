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
        view('register', [
            'error' => flash('register_error'),
        ]);
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($name === '' || $email === '' || $password === '') {
                flash('register_error', '모든 정보를 빠짐없이 입력해 주세요.');
                redirect('/register');
            }

            if (strlen($password) < 6) {
                flash('register_error', '비밀번호는 최소 6자 이상이어야 합니다.');
                redirect('/register');
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('register_error', '유효한 이메일 주소를 입력해 주세요.');
                redirect('/register');
            }

            $success = $this->registerService->register($name, $email, $password);

            if ($success) {
                flash('auth_notice', '회원가입이 완료되었습니다. 로그인 후 나만의 공간을 시작해 보세요!');
                redirect('/login');
            } else {
                flash('register_error', '이미 가입된 이메일이거나 등록할 수 없습니다. 다른 이메일로 시도해 주세요.');
                redirect('/register');
            }
        }
    }
}
