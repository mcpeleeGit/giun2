<?php
class Router {
    protected static $routes = [];
    public static function get($path, $action) { 
        self::$routes['GET'][$path] = $action; 
        error_log("Router::get() - Registered route: GET $path => " . (is_array($action) ? $action[0] . '::' . $action[1] : 'callable'));
    }
    public static function post($path, $action) { 
        self::$routes['POST'][$path] = $action; 
        error_log("Router::post() - Registered route: POST $path => " . (is_array($action) ? $action[0] . '::' . $action[1] : 'callable'));
    }
    public static function dispatch() {
        // 로깅 시작
        $log = [];
        $log[] = "=== Router Dispatch Start ===";
        $log[] = "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET');
        $log[] = "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET');
        $log[] = "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'NOT SET');
        
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $log[] = "Parsed URI (initial): " . $uri;
        
        // 스크립트 이름 처리 (서브디렉토리 배포 환경 대응)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $scriptDir = dirname($scriptName);
        $log[] = "Script Name: " . $scriptName;
        $log[] = "Script Dir: " . $scriptDir;
        
        // 서브디렉토리에서 실행되는 경우를 고려
        if ($scriptDir !== '/' && $scriptDir !== '.') {
            // URI에서 스크립트 디렉토리 경로 제거
            if (strpos($uri, $scriptDir) === 0) {
                $uri = substr($uri, strlen($scriptDir));
                $log[] = "URI after removing script dir: " . $uri;
            }
        }
        
        // URI 정규화
        $uri = '/' . ltrim($uri, '/');
        $uri = rtrim($uri, '/') ?: '/';
        $log[] = "URI after normalization: " . $uri;
        
        // 쿼리 스트링 제거 (이미 parse_url에서 처리되지만 확실히)
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
            $log[] = "URI after removing query string: " . $uri;
        }

        $routes = self::$routes[$method] ?? [];
        $log[] = "Routes count for method '$method': " . count($routes);
        $log[] = "Registered routes: " . implode(', ', array_keys($routes));

        // 경로 매칭
        $matched = false;
        foreach ($routes as $path => $action) {
            $matches = [];
            $log[] = "Checking route: '$path' against URI: '$uri'";
            
            // 정적 경로는 정확히 비교
            if (strpos($path, '{') === false) {
                if ($path === $uri) {
                    $matches = [];
                    $log[] = "✓ Static route matched: '$path'";
                    $matched = true;
                } else {
                    $log[] = "✗ Static route mismatch: '$path' !== '$uri'";
                    continue;
                }
            } else {
                // 동적 경로는 정규식으로 매칭
                $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([a-zA-Z0-9_-]+)', $path);
                $pattern = '#^' . str_replace('/', '\/', $pattern) . '$#';
                $log[] = "Pattern for dynamic route: " . $pattern;
                if (preg_match($pattern, $uri, $matches)) {
                    array_shift($matches); // 전체 매칭 제거, 파라미터만 남김
                    $log[] = "✓ Dynamic route matched: '$path' with params: " . json_encode($matches);
                    $matched = true;
                } else {
                    $log[] = "✗ Dynamic route mismatch: '$path'";
                    continue;
                }
            }

            // 라우트 핸들러 실행
            $log[] = "Action type: " . (is_array($action) ? 'array' : (is_callable($action) ? 'callable' : 'unknown'));
            if (is_array($action)) {
                $log[] = "Controller class: " . $action[0];
                $log[] = "Controller method: " . $action[1];
            }
            
            if (is_array($action) && class_exists($action[0])) {
                $controller = new $action[0]();
                $methodName = $action[1];
                if (method_exists($controller, $methodName)) {
                    $log[] = "✓ Calling controller method: " . $action[0] . "::" . $methodName;
                    $result = call_user_func_array([$controller, $methodName], $matches);
                    if ($result !== null) {
                        echo $result;
                    }
                } else {
                    $log[] = "✗ Method not found: " . $methodName;
                    self::show404("Method $methodName not found in " . $action[0], $log);
                }
            } elseif (is_callable($action)) {
                $log[] = "✓ Calling callable";
                call_user_func_array($action, $matches);
            } else {
                $log[] = "✗ Invalid route handler";
                self::show404("Invalid route handler.", $log);
            }
            
            // 로그 출력
            self::outputLog($log);
            return;
        }

        $log[] = "✗ No route matched";
        $log[] = "=== Router Dispatch End ===";
        self::show404(null, $log);
    }
    
    private static function show404(?string $message = null, ?array $log = null) {
        http_response_code(404);
        
        // 로그 출력
        if ($log !== null) {
            self::outputLog($log);
        }
        
        // view 함수를 사용하여 레이아웃 포함
        if (function_exists('view')) {
            view('errors/404', ['message' => $message]);
        } else {
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
    
    private static function outputLog(array $log) {
        echo "\n<!-- Router Debug Log -->\n";
        echo "<!-- " . implode("\n<!-- ", array_map(function($line) {
            return htmlspecialchars($line, ENT_QUOTES, 'UTF-8');
        }, $log)) . " -->\n";
        echo "<!-- End Router Debug Log -->\n";
        
        // 로그 파일에도 기록 (선택사항)
        $logFile = __DIR__ . '/router_debug.log';
        if (is_writable(__DIR__) || (file_exists($logFile) && is_writable($logFile))) {
            file_put_contents($logFile, date('Y-m-d H:i:s') . "\n" . implode("\n", $log) . "\n\n", FILE_APPEND);
        }
    }
}
