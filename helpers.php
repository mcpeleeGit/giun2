<?php
use App\Models\Seo;
use App\Models\User;
use App\Repositories\SeoRepository;

function current_user(): ?User {
    if (!isset($_SESSION['user'])) {
        return null;
    }

    $user = @unserialize($_SESSION['user'], ['allowed_classes' => [User::class]]);

    return $user instanceof User ? $user : null;
}

function require_login(): User {
    $user = current_user();

    if (!$user) {
        flash('auth_notice', '로그인이 필요한 서비스입니다. 먼저 로그인해 주세요.');
        redirect('/login');
    }

    return $user;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function flash(string $key, ?string $value = null): ?string {
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }

    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    return null;
}

function view($name, $data = []) {
    extract($data);
    $seo = getSeo($data['seo'] ?? null);
    
    // index.php가 있는 디렉토리를 기준으로 경로 계산
    // index.php에서 helpers.php를 로드하므로, index.php의 위치를 기준으로 함
    $scriptDir = dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__);
    $baseDir = $scriptDir;
    
    $header = $baseDir . '/layouts/header.php';
    $footer = $baseDir . '/layouts/footer.php';
    $viewPath = $baseDir . "/pages/{$name}.php";
    $errorView = $baseDir . '/pages/errors/404.php';

    include $header;

    if (is_file($viewPath)) {
        include $viewPath;
    } else {
        http_response_code(404);
        if (is_file($errorView)) {
            include $errorView;
        } else {
            echo "<main class=\"container\"><h1>페이지를 찾을 수 없습니다.</h1></main>";
        }
    }

    include $footer;
}

function adminView($name, $data = []) {
    extract($data);
    $baseDir = __DIR__;
    $header = $baseDir . '/pages/admin/layouts/header.php';
    $footer = $baseDir . '/pages/admin/layouts/footer.php';
    $viewPath = $baseDir . "/pages/admin/{$name}.php";
    $errorView = $baseDir . '/pages/errors/404.php';

    include $header;

    if (is_file($viewPath)) {
        include $viewPath;
    } else {
        http_response_code(404);
        if (is_file($errorView)) {
            include $errorView;
        } else {
            echo "<main class=\"container\"><h1>페이지를 찾을 수 없습니다.</h1></main>";
        }
    }

    include $footer;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            error_log('CSRF 토큰 생성에 실패했습니다: ' . $e->getMessage());
            $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
        }
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function validate_csrf_token(?string $token): bool
{
    $sessionToken = $_SESSION['csrf_token'] ?? null;
    if (!$sessionToken || !is_string($token)) {
        return false;
    }

    return hash_equals($sessionToken, $token);
}

function require_csrf_token(?string $token): void
{
    if (!validate_csrf_token($token)) {
        http_response_code(419);
        exit('잘못된 요청입니다. 다시 시도해 주세요.');
    }
}

function require_admin(): User
{
    $user = require_login();

    if (($user->role ?? null) !== 'ADMIN') {
        flash('auth_error', '관리자 권한이 필요합니다.');
        redirect('/');
    }

    return $user;
}

function getSeo($seo_data) {
    $seoRepository = new SeoRepository(); // path 에 따라 저장된 seo 데이터를 가져오는 경우
    $seo = $seoRepository->findByPath($_SERVER['REQUEST_URI']);

    if (!$seo) {
        $seo = new Seo();
        $seo->path = $_SERVER['REQUEST_URI'];
        $seo->title = '소셜 서비스 연동 도구 - 온라인 도구 제공 사이트';
        $seo->description = '각종 소셜 서비스와 연동할 수 있는 다양한 온라인 도구를 제공합니다.';
        $seo->image = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/assets/images/favicon.png';
        $seo->url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    if (isset($seo_data)) { // 게시판 상세와 같이 페이지 별로 활용가능한 seo 데이터가 있는 경우
        if(isset($seo_data->title)) $seo->title = $seo_data->title;
        if(isset($seo_data->description)) $seo->description = $seo_data->description;
        if(isset($seo_data->image)) $seo->image = $seo_data->image;
        if(isset($seo_data->url)) $seo->url = $seo_data->url; 
    }

    return $seo;
}
