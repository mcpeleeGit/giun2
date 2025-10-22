<?php

namespace App\Http\Controllers;

class LogoutController {
    public function logout() {
        unset($_SESSION['user']);
        flash('auth_notice', '안전하게 로그아웃되었습니다. 다시 만날 날을 기다릴게요!');
        redirect('/');
    }
}
