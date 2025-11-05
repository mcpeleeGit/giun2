<?php

namespace App\Http\Controllers;

use App\Services\BoardService;
use App\Services\KakaoService;
use App\Services\TodoService;
use App\Services\UserService;

class MyPageController
{
    private $todoService;
    private $boardService;
    private $userService;

    public function __construct()
    {
        $this->todoService = new TodoService();
        $this->boardService = new BoardService();
        $this->userService = new UserService();
    }

    public function index()
    {
        $user = require_login();
        $todos = $this->todoService->getTodosForUser($user->id);
        $posts = $this->boardService->getPostsByUser($user->id);

        $completed = array_reduce($todos, function ($carry, $todo) {
            return $carry + ($todo->is_completed ? 1 : 0);
        }, 0);

        view('mypage', [
            'user' => $user,
            'todoStats' => [
                'total' => count($todos),
                'completed' => $completed,
                'pending' => count($todos) - $completed
            ],
            'recentTodos' => array_slice($todos, 0, 3),
            'recentPosts' => array_slice($posts, 0, 3),
            'notice' => flash('mypage_notice'),
            'error' => flash('mypage_error'),
            'deleteError' => flash('mypage_delete_error'),
            'kakaoLoginEnabled' => KakaoService::isConfigured(),
        ]);
    }

    public function update()
    {
        $user = require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/mypage');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $newPasswordConfirmation = trim($_POST['new_password_confirmation'] ?? '');

        if ($name === '' || $email === '') {
            flash('mypage_error', '이름과 이메일을 모두 입력해 주세요.');
            redirect('/mypage');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('mypage_error', '유효한 이메일 형식인지 다시 확인해 주세요.');
            redirect('/mypage');
        }

        if ($currentPassword === '' || !password_verify($currentPassword, $user->password)) {
            flash('mypage_error', '현재 비밀번호가 올바르지 않습니다. 다시 입력해 주세요.');
            redirect('/mypage');
        }

        $passwordToSet = null;
        if ($newPassword !== '' || $newPasswordConfirmation !== '') {
            if ($newPassword !== $newPasswordConfirmation) {
                flash('mypage_error', '새 비밀번호가 서로 일치하는지 확인해 주세요.');
                redirect('/mypage');
            }

            if (strlen($newPassword) < 6) {
                flash('mypage_error', '새 비밀번호는 최소 6자 이상이어야 합니다.');
                redirect('/mypage');
            }

            $passwordToSet = $newPassword;
        }

        $updatedUser = $this->userService->updateProfile($user->id, $name, $email, $passwordToSet);

        if (!$updatedUser) {
            flash('mypage_error', '이미 사용 중인 이메일이거나 정보를 저장할 수 없습니다.');
            redirect('/mypage');
        }

        $_SESSION['user'] = serialize($updatedUser);

        flash('mypage_notice', '내 정보가 성공적으로 업데이트되었습니다.');
        redirect('/mypage');
    }

    public function delete()
    {
        $user = require_login();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/mypage');
        }

        $confirmation = trim($_POST['confirm'] ?? '');
        $password = trim($_POST['current_password'] ?? '');

        if (strcasecmp($confirmation, 'DELETE') !== 0) {
            flash('mypage_delete_error', "회원탈퇴를 진행하려면 'DELETE'를 입력해 주세요.");
            redirect('/mypage');
        }

        if ($password === '' || !password_verify($password, $user->password)) {
            flash('mypage_delete_error', '비밀번호가 일치하지 않아 회원탈퇴를 진행할 수 없습니다.');
            redirect('/mypage');
        }

        if ($this->userService->deleteUser($user->id)) {
            unset($_SESSION['user']);
            session_regenerate_id(true);
            flash('auth_notice', '회원탈퇴가 완료되었습니다. 그동안 이용해 주셔서 감사합니다.');
            redirect('/');
        }

        flash('mypage_delete_error', '회원탈퇴 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
        redirect('/mypage');
    }
}
