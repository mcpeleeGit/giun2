<?php
class Router {
    protected static $routes = [];
    public static function get($path, $action) { self::$routes['GET'][$path] = $action; }
    public static function post($path, $action) { self::$routes['POST'][$path] = $action; }
    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/') $uri = substr($uri, strlen($scriptName));

        $routes = self::$routes[$method] ?? [];

        // 정규 표현식을 사용하여 경로 매칭
        foreach ($routes as $path => $action) {
            $pattern = preg_replace('#\{[a-zA-Z0-9_]+\}#', '([a-zA-Z0-9_]+)', $path);
            if (preg_match('#^' . $pattern . '$#', $uri, $matches)) {
                array_shift($matches); // 첫 번째 매칭은 전체 문자열이므로 제거
                if (is_array($action) && class_exists($action[0])) {
                    $controller = new $action[0]();
                    $method = $action[1];
                    if (method_exists($controller, $method)) {
                        echo call_user_func_array([$controller, $method], $matches);
                    } else echo "Method $method not found.";
                } elseif (is_callable($action)) {
                    call_user_func_array($action, $matches);
                } else echo "Invalid route handler.";
                return;
            }
        }

        http_response_code(404);
        echo "404 Not Found";
    }
}
