<?php

namespace App;

class Router
{
    private $GET_ROUTES = [];
    private $POST_ROUTES = [];
    private $PATCH_ROUTES = [];
    private $PUT_ROUTES = [];
    private $DELETE_ROUTES = [];

    public function __construct()
    {
        global $ROOT;
        $this->GET_ROUTES    = include $ROOT . '/src/Routes/GET.php';
        $this->POST_ROUTES   = include $ROOT . '/src/Routes/POST.php';
        $this->PATCH_ROUTES  = include $ROOT . '/src/Routes/PATCH.php';
        $this->PUT_ROUTES    = include $ROOT . '/src/Routes/PUT.php';
        $this->DELETE_ROUTES = include $ROOT . '/src/Routes/DELETE.php';
    }

    public function init()
    {
        global $ENV;
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($uri, PHP_URL_PATH);
        $query = $_GET;

        try {
            switch ($method) {
                case 'HEAD':
                case 'GET':
                    $this->handle($this->GET_ROUTES, $path, $query);
                    break;
                case 'POST':
                    $this->handle($this->POST_ROUTES, $path, $query);
                    break;
                case 'PATCH':
                    $this->handle($this->PATCH_ROUTES, $path, $query);
                    break;
                case 'PUT':
                    $this->handle($this->PUT_ROUTES, $path, $query);
                    break;
                case 'DELETE':
                    $this->handle($this->DELETE_ROUTES, $path, $query);
                    break;
                default:
                    http_response_code(405);
                    throw new \Exception('405: 方法不存在.');
            };
        } catch (\Exception $err) {
            if ($ENV === 'develop') {
                print_err($err->getMessage());
            };
        };
    }

    private function handle($routes, $path, $query)
    {
        foreach ($routes as $route => $controller) {
            $pattern = preg_replace('/:[\w\-\_\.]+/', '([\w\-\_\.]+)', $route);
            $pattern = str_replace('/', '\/', $pattern);
            if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
                array_shift($matches);
                // Extract keys from the route
                preg_match_all('/:([\w\-\_\.]+)/', $route, $keys);
                $params = array_combine($keys[1], $matches);
                $this->get($controller, $query, $params);
                return;
            }
        }

        http_response_code(404);
        throw new \Exception($path . ' 404: 路由不存在.');
    }

    private function get($controller, $query, $params = [])
    {
        if (!class_exists($controller)) {
            http_response_code(500);
            throw new \Exception('500: 尚未設定控制器.');
        };

        $instance = new $controller();

        if (!method_exists($instance, 'init')) {
            http_response_code(500);
            throw new \Exception('500: 控制器不存在 init().');
        };

        $instance->init($params);
    }
}
