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

    $viewExists = is_file($viewPath);

    if (!$viewExists) {
        http_response_code(404);
    }

    include $header;

    if ($viewExists) {
        include $viewPath;
    } else {
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

    $viewExists = is_file($viewPath);

    if (!$viewExists) {
        http_response_code(404);
    }

    include $header;

    if ($viewExists) {
        include $viewPath;
    } else {
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

function sanitize_rich_text(string $html): string
{
    $html = trim($html);

    if ($html === '') {
        return '';
    }

    $allowedTags = [
        'p' => ['style', 'class'],
        'br' => [],
        'strong' => ['style', 'class'],
        'em' => ['style', 'class'],
        'u' => ['style', 'class'],
        's' => ['style', 'class'],
        'span' => ['style', 'class'],
        'div' => ['style', 'class'],
        'sup' => ['style', 'class'],
        'sub' => ['style', 'class'],
        'ul' => ['style', 'class'],
        'ol' => ['style', 'class'],
        'li' => ['style', 'class'],
        'blockquote' => ['style', 'class'],
        'pre' => ['style', 'class'],
        'code' => ['class'],
        'h1' => ['style', 'class'],
        'h2' => ['style', 'class'],
        'h3' => ['style', 'class'],
        'h4' => ['style', 'class'],
        'h5' => ['style', 'class'],
        'h6' => ['style', 'class'],
        'figure' => ['style', 'class'],
        'figcaption' => ['style', 'class'],
        'a' => ['href', 'title', 'target', 'rel', 'class', 'style'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'class', 'style'],
        'table' => ['class', 'style', 'border', 'cellpadding', 'cellspacing'],
        'thead' => ['class', 'style'],
        'tbody' => ['class', 'style'],
        'tfoot' => ['class', 'style'],
        'tr' => ['class', 'style'],
        'th' => ['class', 'style', 'colspan', 'rowspan', 'scope'],
        'td' => ['class', 'style', 'colspan', 'rowspan'],
        'colgroup' => ['class', 'style', 'span'],
        'col' => ['class', 'style', 'span'],
        'caption' => ['class', 'style'],
        'hr' => ['class', 'style'],
    ];

    $document = new \DOMDocument();
    $previousUseInternalErrors = libxml_use_internal_errors(true);

    $document->loadHTML('<?xml encoding="utf-8"?><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    libxml_clear_errors();
    libxml_use_internal_errors($previousUseInternalErrors);

    $allowedAnchorSchemes = ['http', 'https', 'mailto'];

    $sanitizeNode = function (\DOMNode $node) use (&$sanitizeNode, $allowedTags, $allowedAnchorSchemes) {
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $tagName = strtolower($node->nodeName);

            if (!isset($allowedTags[$tagName])) {
                $parent = $node->parentNode;
                if ($parent) {
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);
                }
                return;
            }

            $allowedAttributes = $allowedTags[$tagName];

            if ($node->hasAttributes()) {
                foreach (iterator_to_array($node->attributes) as $attribute) {
                    $attrName = strtolower($attribute->nodeName);
                    $value = trim($attribute->nodeValue);

                    if (!in_array($attrName, $allowedAttributes, true) || $value === '') {
                        $node->removeAttributeNode($attribute);
                        continue;
                    }

                    if ($attrName === 'style') {
                        $cleanStyle = sanitize_rich_text_clean_style($value);

                        if ($cleanStyle === '') {
                            $node->removeAttribute($attrName);
                            continue;
                        }

                        $node->setAttribute($attrName, $cleanStyle);
                        continue;
                    }

                    if ($tagName === 'a' && $attrName === 'href') {
                        if (!sanitize_rich_text_is_allowed_url($value, $allowedAnchorSchemes, true)) {
                            $node->removeAttribute($attrName);
                            continue;
                        }
                    }

                    if ($tagName === 'a' && $attrName === 'target') {
                        $allowedTargets = ['_self', '_blank'];
                        if (!in_array(strtolower($value), $allowedTargets, true)) {
                            $node->removeAttribute($attrName);
                            continue;
                        }
                    }

                    if ($tagName === 'img' && $attrName === 'src') {
                        if (!sanitize_rich_text_is_allowed_url($value, ['http', 'https'], true)) {
                            $node->removeAttribute($attrName);
                            continue;
                        }
                    }

                    if ($tagName === 'img' && in_array($attrName, ['width', 'height'], true)) {
                        if (!preg_match('/^\d+(?:\.\d+)?(?:%)?$/', $value)) {
                            $node->removeAttribute($attrName);
                        }
                        continue;
                    }

                    if (in_array($tagName, ['th', 'td'], true) && in_array($attrName, ['colspan', 'rowspan'], true)) {
                        if (!preg_match('/^\d+$/', $value)) {
                            $node->removeAttribute($attrName);
                        }
                        continue;
                    }

                    if ($tagName === 'th' && $attrName === 'scope') {
                        $allowedScopes = ['row', 'col', 'rowgroup', 'colgroup'];
                        if (!in_array(strtolower($value), $allowedScopes, true)) {
                            $node->removeAttribute($attrName);
                        }
                        continue;
                    }

                    if ($tagName === 'table' && in_array($attrName, ['border', 'cellpadding', 'cellspacing'], true)) {
                        if (!preg_match('/^\d+$/', $value)) {
                            $node->removeAttribute($attrName);
                        }
                        continue;
                    }
                }
            }

            if ($tagName === 'a' && strtolower($node->getAttribute('target')) === '_blank') {
                $existingRel = $node->getAttribute('rel');
                $relParts = array_filter(array_map('trim', explode(' ', $existingRel . ' noopener noreferrer')));
                $node->setAttribute('rel', implode(' ', array_unique($relParts)));
            }

            if ($tagName === 'img' && !$node->hasAttribute('alt')) {
                $node->setAttribute('alt', '');
            }
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            $sanitizeNode($child);
        }
    };

    $wrapper = $document->getElementsByTagName('div')->item(0);

    if (!$wrapper) {
        $wrapper = $document->documentElement;
    }

    if ($wrapper) {
        foreach (iterator_to_array($wrapper->childNodes) as $child) {
            $sanitizeNode($child);
        }
    }

    $sanitized = '';
    if ($wrapper) {
        foreach ($wrapper->childNodes as $child) {
            $sanitized .= $document->saveHTML($child);
        }
    }

    return $sanitized;
}

function sanitize_rich_text_is_allowed_url(string $url, array $allowedSchemes, bool $allowRelative = false): bool
{
    $url = trim($url);

    if ($url === '') {
        return false;
    }

    if ($allowRelative && (str_starts_with($url, '/') || str_starts_with($url, '#'))) {
        return true;
    }

    $parsed = parse_url($url);

    if (!$parsed || empty($parsed['scheme'])) {
        return false;
    }

    return in_array(strtolower($parsed['scheme']), $allowedSchemes, true);
}

function sanitize_rich_text_clean_style(string $style): string
{
    $style = trim($style);

    if ($style === '') {
        return '';
    }

    $style = preg_replace('/\/\*.*?\*\//s', '', $style);

    $allowedProperties = [
        'background-color',
        'border',
        'border-bottom',
        'border-bottom-color',
        'border-bottom-style',
        'border-bottom-width',
        'border-collapse',
        'border-color',
        'border-left',
        'border-left-color',
        'border-left-style',
        'border-left-width',
        'border-radius',
        'border-right',
        'border-right-color',
        'border-right-style',
        'border-right-width',
        'border-spacing',
        'border-style',
        'border-top',
        'border-top-color',
        'border-top-style',
        'border-top-width',
        'border-width',
        'color',
        'display',
        'font-family',
        'font-size',
        'font-style',
        'font-weight',
        'height',
        'letter-spacing',
        'line-height',
        'margin',
        'margin-bottom',
        'margin-left',
        'margin-right',
        'margin-top',
        'max-height',
        'max-width',
        'min-height',
        'min-width',
        'padding',
        'padding-bottom',
        'padding-left',
        'padding-right',
        'padding-top',
        'text-align',
        'text-decoration',
        'text-transform',
        'vertical-align',
        'white-space',
        'width',
        'word-break',
        'word-spacing',
    ];

    $cleanRules = [];

    foreach (explode(';', $style) as $rule) {
        $rule = trim($rule);

        if ($rule === '' || strpos($rule, ':') === false) {
            continue;
        }

        [$property, $value] = array_map('trim', explode(':', $rule, 2));

        $propertyLower = strtolower($property);

        if (!in_array($propertyLower, $allowedProperties, true)) {
            continue;
        }

        $valueLower = strtolower($value);

        if ($valueLower === ''
            || strpos($valueLower, 'expression') !== false
            || strpos($valueLower, 'javascript:') !== false
            || strpos($valueLower, 'vbscript:') !== false
            || preg_match('/url\s*\(/i', $valueLower)) {
            continue;
        }

        $cleanRules[] = $propertyLower . ': ' . $value;
    }

    return implode('; ', $cleanRules);
}

function render_rich_text(?string $content): string
{
    if (!is_string($content)) {
        return '';
    }

    $content = trim($content);

    if ($content === '') {
        return '';
    }

    $sanitized = sanitize_rich_text($content);

    if ($sanitized === '') {
        return '';
    }

    if ($sanitized === strip_tags($sanitized)) {
        return nl2br($sanitized);
    }

    return $sanitized;
}
