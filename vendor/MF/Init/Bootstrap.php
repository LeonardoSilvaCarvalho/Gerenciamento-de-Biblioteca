<?php

namespace MF\Init;

abstract class Bootstrap {

    private $routes;

    abstract protected function initRoutes();

    public function __construct(){
        $this->initRoutes();
        $this->run($this->getUrl());
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function setRoutes(array $routes) {
        $this->routes = $routes;
    }

    protected function run($url){
        foreach ($this->getRoutes() as $path => $route) {
            // Converte o padrão com placeholders para regex
            $pattern = preg_replace('/\{[a-zA-Z]+\}/', '(\d+)', $route['route']);
            $pattern = str_replace('/', '\/', $pattern);

            // Verifica se a URL corresponde ao padrão da rota
            if (preg_match('/^' . $pattern . '$/', $url, $params)) {
                // Define o controlador e a ação
                $class = "App\\Controller\\" . ucfirst($route['controller']);
                $controller = new $class;
                $action = $route['action'];

                // Remove o primeiro elemento de $params (a URL completa)
                array_shift($params);

                // Chama o método do controlador com todos os parâmetros capturados
                call_user_func_array([$controller, $action], $params);
                return;
            }
        }
    }

    protected function getUrl(){
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
}
