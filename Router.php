<?php
class Router {
    protected static $routes = [];
    public static function get($path, $action) { self::$routes['GET'][$path] = $action; }
    public static function post($path, $action) { self::$routes['POST'][$path] = $action; }
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        
        // 스크립트 이름 처리 (서브디렉토리 배포 환경 대응)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $scriptDir = dirname($scriptName);
        
        // 서브디렉토리에서 실행되는 경우를 고려
        if ($scriptDir !== '/' && $scriptDir !== '.') {
            // URI에서 스크립트 디렉토리 경로 제거
            if (strpos($uri, $scriptDir) === 0) {
                $uri = substr($uri, strlen($scriptDir));
            }
        }
        
        // URI 정규화
        $uri = '/' . ltrim($uri, '/');
        $uri = rtrim($uri, '/') ?: '/';
        
        // 쿼리 스트링 제거 (이미 parse_url에서 처리되지만 확실히)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }

        $routes = self::$routes[$method] ?? [];

        // 정규 표현식을 사용하여 경로 매칭
        foreach ($routes as $path => $action) {
            // 경로를 정규식 패턴으로 변환
            $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([a-zA-Z0-9_-]+)', $path);
            $pattern = '/' . ltrim($pattern, '/');
            $pattern = rtrim($pattern, '/') ?: '/';
            // 정규식 패턴 완성
            $pattern = '#^' . preg_quote($pattern, '#') . '$#';
            // 동적 파라미터 부분을 다시 변환 (이스케이프된 부분을 되돌림)
            $pattern = preg_replace('#\\\\([a-zA-Z0-9_-]+)\\\\(\\\\)#', '([a-zA-Z0-9_-]+)', $pattern);
            // 더 간단한 방법: 정적 경로는 정확히 매칭, 동적 경로는 패턴 매칭
            if (strpos($path, '{') === false) {
                // 정적 경로는 정확히 비교
                if ($path === $uri) {
                    $matches = [];
                } else {
                    continue;
                }
            } else {
                // 동적 경로는 정규식으로 매칭
                $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([a-zA-Z0-9_-]+)', $path);
                $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
                if (!preg_match($pattern, $uri, $matches)) {
                    continue;
                }
                array_shift($matches); // 전체 매칭 제거
            }

            if (true) {
                array_shift($matches); // 첫 번째 매칭은 전체 문자열이므로 제거
                if (is_array($action) && class_exists($action[0])) {
                    $controller = new $action[0]();
                    $methodName = $action[1];
                    if (method_exists($controller, $methodName)) {
                        $result = call_user_func_array([$controller, $methodName], $matches);
                        if ($result !== null) {
                            echo $result;
                        }
                    } else {
                        self::show404("Method $methodName not found in " . $action[0]);
                    }
                } elseif (is_callable($action)) {
                    call_user_func_array($action, $matches);
                } else {
                    self::show404("Invalid route handler.");
                }
                return;
            }
        }

        self::show404();
    }
    
    private static function show404(?string $message = null) {
        http_response_code(404);
        $errorView = __DIR__ . '/pages/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "<main class=\"container\"><h1>페이지를 찾을 수 없습니다.</h1>";
            if ($message) {
                echo "<p>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p>";
            }
            echo "</main>";
        }
    }
}
