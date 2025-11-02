<?php

namespace App\Http\AdminControllers;

use App\Http\AdminControllers\Common\Controller;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct()
    {
        parent::__construct();
        $this->userService = new UserService();
    }

    public function index(): void
    {
        $users = $this->userService->getAllUsers();

        adminView('users', [
            'users' => $users,
            'admin' => $this->adminUser,
        ]);
    }

    public function delete(): void
    {
        $this->ensurePostWithCsrf();

        $userId = $this->getPostInt('user_id');

        if ($userId === null) {
            $this->redirectWithError('/admin/users', '잘못된 요청입니다.');
        }

        if ($userId === $this->adminUser->id) {
            $this->redirectWithError('/admin/users', '본인 계정은 삭제할 수 없습니다.');
        }

        if (!$this->userService->deleteUser($userId)) {
            $this->redirectWithError('/admin/users', '사용자 삭제에 실패했습니다.');
        }

        $this->redirectWithSuccess('/admin/users', '사용자를 삭제했습니다.');
    }
}
