<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Services\UserService;

class UserController extends Controller {
    private $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    public function index() {
        // 사용자 목록 가져오기
        $users = $this->userService->getAllUsers();

        // 사용자 목록을 뷰에 전달
        adminView('users', ['users' => $users]);
    }
}
