<?php

use Core\Request\Request;
use Core\Response\Response;
use Core\Router\Route;
use Core\Router\Router;

class Kernel
{
    private $config;
    private $connection;
    public function __construct()
    {
        $this->config = require __DIR__ . '/config/config.php';
        $this->container = \Core\ServiceContainer::getInstance($this->config);
    }

    public function getConnection()
    {
        return $this->connection;
    }

    private function getRoute(Request $request): Route
    {
        require_once __DIR__.'/config/routes.php';
        $route = Router::findRoute($request);
        if ($route === null) {
            throw new Exception('route not found');
        }
        return $route;
    }

    public function createResponse(Request $request):Response
    {
        $route = $this->getRoute($request);
//        echo $route->getControllerClass();
//        echo $route->getMethod();
        $controller = $this->getController($route);
        $params = $route->getPathValues($request->getPath());
//        echo json_encode($params);
        array_unshift($params, $request);
        return call_user_func_array([$controller, $route->getMethod()], $params);
    }

    private function getController(Route $route)
    {
        return $this->container->get($route->getControllerClass());
    }
}
