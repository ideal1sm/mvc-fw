<?php

namespace application\core;

use mysql_xdevapi\Exception;

class Router
{

    protected array $routes = [];
    protected array $params = [];

    public function __construct()
    {
        $arr = require 'application/config/routes.php';
        foreach ($arr as $key => $value) {
            $this->add($key, $value);
        }
    }

    public function add($route, $params): void
    {
        $route = '#^' . $route . '$#';
        $this->routes[$route] = $params;
    }

    public function match(): bool
    {
        foreach ($this->routes as $route => $params) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            if (preg_match($route, $url, $matches)) {
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function run()
    {
        if ($this->match()) {
            $path = 'application\controllers\\' . ucfirst($this->params['controller']) . 'Controller';
            if (class_exists($path)) {
                $controller = new $path();
                $action = $this->params['action'] . 'Action';
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    echo 'Action: ' . '<b>' . $action . '</b>' . ' not found';
                }
            } else {
                echo 'Controller: ' . '<b>' . $path . '</b>' . ' not found';
            }
        } else {
            echo '404';
        }
    }
}