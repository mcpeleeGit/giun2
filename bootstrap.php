<?php

use App\Models\User;

// 공통 초기화
session_start();

// 에러 표시 (개발 환경)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 환경 변수 로드
global $config; // 전역 변수로 설정
$config = parse_ini_file(__DIR__ . '/config.ini', true);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

function currentAdminUser(): ?User
{
    if (!isset($_SESSION['user'])) {
        return null;
    }

    $user = @unserialize($_SESSION['user'], ['allowed_classes' => [User::class]]);

    return $user instanceof User ? $user : null;
}

// 관리자 권한 확인 함수
function checkAdminAccess(): void
{
    $user = currentAdminUser();

    if (!$user || ($user->role ?? null) !== 'ADMIN') {
        header('Location: /login');
        exit;
    }
}

// AdminControllers 호출 시 권한 확인
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($uri, '/admin') === 0) {
    checkAdminAccess();
}
