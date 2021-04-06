<?php

namespace Core;

use Core\Abstracts\Controller;
use Core\Interfaces\Middleware;
use Core\Libraries\Config;
use Core\Libraries\Request;
use Core\Libraries\Route;
use Core\Libraries\View;

/**
 * Class Core
 * @package Core
 *
 * @property Route $route
 * @property Request $request
 * @property Config $config
 */
class Core
{
    public $route;
    public $request;
    public $config;

    public function __construct()
    {
        $this->route = new Route();
        $this->request = new Request();
        $this->config = new Config();
    }

    public function fireController()
    {
        $routeOptions = $this->route->findRoute();

        foreach ($routeOptions['middlewares'] ?? [] as $middleware) {
            if (!$this->handleMiddlewares(new $middleware)) {
                abort_404('request not pass middleware (' . $middleware . ')');
            }
        }

        $response = $this->runControllerMethod($this->initController($routeOptions['method'][0]), $routeOptions['method'][1]);

        $this->renderResponse($response);
    }

    private function handleMiddlewares(Middleware $middleware)
    {
        return $middleware->handle($this->request);
    }

    private function initController($controllerClass)
    {
        return new $controllerClass($this->request, $this->config);
    }


    private function runControllerMethod(Controller $controller, string $method)
    {
        $params = $this->route->getParams();
        return $controller->$method(...$params);
    }

    private function renderResponse($response)
    {
        if ($response instanceof View) {
            $this->viewResponse($response);
        } elseif (is_array($response) || is_object($response)) {
            $this->jsonResponse($response);
        } else {
            echo strval($response);
        }

        exit(0);
    }

    private function jsonResponse($response)
    {
        header('Content-Type: application/json');
        echo json_encode($response);

    }

    private function viewResponse(View $response)
    {
        echo $response->render();
    }


}