<?php


namespace Core\Libraries;


class Route
{
    public $request_uri;

    protected $routes;
    protected $params = [];

    public function __construct()
    {
        $this->routes = include(RESOURCES_PATH . 'routes.php');
        $this->request_uri = $this->urlFilter($_SERVER['REQUEST_URI']);
    }

    public function findRoute()
    {
        // sub-folder1/sub-folder2/{param}/sub-folder3/{param}

        $url = explode('/', $this->request_uri);

        foreach ($this->routes as $route => $routeOptions) {
            $params = $this->matchRoutes($route, $url);

            if($params!==false){
                $this->params = $params;

                return $routeOptions;
            }
        }

        abort_404('path not exist');
    }

    public function getParams(){
        return $this->params;
    }

    private function matchRoutes($route, array $urlArray)
    {
        $route = explode('/', $this->urlFilter($route));

        $params = [];

        if(count($route) == count($urlArray)){

            for($i = 0; $i<count($route); $i++){
                if(preg_match('/^\{[^\{\}]+\}$/', $route[$i])){
                    $params[] = $urlArray[$i];
                }
                elseif($route[$i]!=$urlArray[$i]) {
                    return false;
                }
            }
            return $params;
        }

        return false;
    }

    private function urlFilter($url)
    {
        $url = explode('?', $url, 2)[0];
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);

        return $url;
    }
}