<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\KakaoService;
use App\Services\UserService;
use RuntimeException;

class KakaoAuthController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function redirect(): void
    {
        $action = $_GET['action'] ?? 'login';
        $action = $action === 'link' ? 'link' : 'login';

        if ($action === 'link') {
            $user = require_login();
            $userId = $user->id;
        } else {
            $userId = null;
        }

        $kakaoService = $this->createKakaoService($action);

        try {
            $state = bin2hex(random_bytes(16));
        } catch (\Exception $e) {
            $this->handleError($action, '로그인 요청을 준비하는 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.');
            return;
        }

        $_SESSION['oauth_state'] = [
            'value' => $state,
            'action' => $action,
            'user_id' => $userId,
        ];

        header('Location: ' . $kakaoService->getAuthorizationUrl($state));
        exit;
    }

    public function callback(): void
    {
        $stateData = $_SESSION['oauth_state'] ?? null;
        unset($_SESSION['oauth_state']);

        $action = is_array($stateData) && isset($stateData['action']) ? $stateData['action'] : 'login';
        $stateValue = is_array($stateData) && isset($stateData['value']) ? $stateData['value'] : null;

        $error = $_GET['error'] ?? null;
        if ($error) {
            $this->handleError($action, '카카오 로그인 과정이 취소되었습니다. 다시 시도해 주세요.');
            return;
        }

        $stateFromRequest = $_GET['state'] ?? null;
        if (!$stateData || !$stateValue || $stateValue !== $stateFromRequest) {
            $this->handleError($action, '잘못된 인증 요청입니다. 처음부터 다시 시도해 주세요.');
            return;
        }

        $code = $_GET['code'] ?? null;
        if (!$code) {
            $this->handleError($action, '카카오에서 전달된 인증 코드가 없습니다. 다시 시도해 주세요.');
            return;
        }

        $kakaoService = $this->createKakaoService($action);

        try {
            $tokenResponse = $kakaoService->fetchAccessToken($code);
        } catch (RuntimeException $e) {
            error_log('Kakao token request failed: ' . $e->getMessage());
            $this->handleError($action, '카카오 인증에 실패했습니다. 잠시 후 다시 시도해 주세요.');
            return;
        }

        $accessToken = $tokenResponse['access_token'] ?? null;
        if (!$accessToken) {
            $this->handleError($action, '카카오로부터 인증 정보를 받을 수 없습니다. 다시 시도해 주세요.');
            return;
        }

        try {
            $profile = $kakaoService->fetchUserProfile($accessToken);
        } catch (RuntimeException $e) {
            error_log('Kakao profile request failed: ' . $e->getMessage());
            $this->handleError($action, '카카오 사용자 정보를 불러오는 중 오류가 발생했습니다.');
            return;
        }

        $kakaoId = isset($profile['id']) ? (string) $profile['id'] : null;
        if (!$kakaoId) {
            $this->handleError($action, '카카오 계정 정보를 확인할 수 없습니다.');
            return;
        }

        $account = $profile['kakao_account'] ?? [];
        $email = isset($account['email']) ? trim(strtolower($account['email'])) : null;
        $nickname = $account['profile']['nickname'] ?? ($profile['properties']['nickname'] ?? '카카오 사용자');
        $nickname = trim($nickname) !== '' ? $nickname : '카카오 사용자';

        if ($action === 'link') {
            $this->linkCurrentUser((int) ($stateData['user_id'] ?? 0), $kakaoId);
            return;
        }

        $this->loginWithKakao($kakaoId, $email, $nickname);
    }

    private function loginWithKakao(string $kakaoId, ?string $email, string $nickname): void
    {
        $user = $this->userService->getUserByKakaoId($kakaoId);

        if (!$user && $email) {
            $user = $this->userService->getUserByEmail($email);
        }

        if ($user && empty($user->kakao_id)) {
            $linked = $this->userService->linkKakaoAccount($user->id, $kakaoId);
            if ($linked) {
                $user = $linked;
            }
        }

        if (!$user) {
            $emailToUse = $this->determineEmailForNewUser($kakaoId, $email);
            $passwordHash = $this->generateRandomPasswordHash();
            $user = $this->userService->createUserWithKakao($nickname, $emailToUse, $passwordHash, $kakaoId);

            if (!$user) {
                $this->handleError('login', '새로운 카카오 계정을 생성할 수 없습니다. 이미 사용 중인 이메일인지 확인해 주세요.');
                return;
            }
        }

        $this->completeLogin($user);
    }

    private function linkCurrentUser(int $userIdFromState, string $kakaoId): void
    {
        $currentUser = current_user();
        if (!$userIdFromState || !$currentUser || $currentUser->id !== $userIdFromState) {
            $this->handleError('link', '세션이 만료되었습니다. 다시 로그인한 뒤 연동을 진행해 주세요.');
            return;
        }

        $linkedUser = $this->userService->linkKakaoAccount($currentUser->id, $kakaoId);

        if (!$linkedUser) {
            $this->handleError('link', '이미 다른 계정에 연결된 카카오 ID 입니다.');
            return;
        }

        $_SESSION['user'] = serialize($linkedUser);
        flash('mypage_notice', '카카오 계정 연동이 완료되었습니다. 이제 카카오 로그인을 사용할 수 있습니다.');
        redirect('/mypage');
    }

    private function determineEmailForNewUser(string $kakaoId, ?string $email): string
    {
        if ($email) {
            return $email;
        }

        $base = 'kakao_' . $kakaoId . '@users.local';
        $candidate = $base;
        $suffix = 1;

        while ($this->userService->getUserByEmail($candidate)) {
            $candidate = sprintf('kakao_%s_%d@users.local', $kakaoId, ++$suffix);
        }

        return $candidate;
    }

    private function generateRandomPasswordHash(): string
    {
        try {
            $bytes = random_bytes(32);
        } catch (\Exception $e) {
            $bytes = openssl_random_pseudo_bytes(32);
            if ($bytes === false) {
                $bytes = uniqid((string) mt_rand(), true);
            }
        }

        $random = bin2hex($bytes);

        return password_hash($random, PASSWORD_DEFAULT);
    }

    private function completeLogin(User $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = serialize($user);

        if (($user->role ?? null) === 'ADMIN') {
            flash('admin_notice', $user->name . '님, 안전한 관리자 페이지에 접속했습니다.');
            redirect('/admin');
        }

        flash('mypage_notice', $user->name . '님, 환영합니다! 오늘의 계획을 완성해 볼까요?');
        redirect('/mypage');
    }

    private function createKakaoService(string $action): KakaoService
    {
        try {
            return new KakaoService();
        } catch (RuntimeException $e) {
            error_log('Kakao login is not configured: ' . $e->getMessage());
            $this->handleError($action, '카카오 로그인 설정이 완료되지 않았습니다. 관리자에게 문의해 주세요.');
        }

        throw new RuntimeException('Kakao login configuration missing.');
    }

    private function handleError(string $action, string $message): void
    {
        if ($action === 'link') {
            flash('mypage_error', $message);
            redirect('/mypage');
        }

        flash('auth_error', $message);
        redirect('/login');
    }
}
