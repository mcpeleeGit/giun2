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
        $action = self::$routes[$method][$uri] ?? null;
        if (!$action) { http_response_code(404); echo "404 Not Found"; return; }
        if (is_array($action) && class_exists($action[0])) {
            $controller = new $action[0]();
            $method = $action[1];
            if (method_exists($controller, $method)) {
                echo call_user_func([$controller, $method]);
            } else echo "Method $method not found.";
        } elseif (is_callable($action)) {
            call_user_func($action);
        } else echo "Invalid route handler.";
    }
}
