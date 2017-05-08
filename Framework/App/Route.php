<?php

namespace Framework\App;

use Framework\Http\Routing\Router;

class Route {

    protected $router;

    public function __construct(Router $router){
        $this->router = $router;
    }

    public function __call(string $name, $arguments) {

        list($pattern, $callback) = $arguments;

        if( is_callable($callback) )
            return $this->router->$name($pattern, $callback);

        

        $arguments = explode('@', $callback);

        if(count($arguments) === 2){

            list($controller_name, $controller_method) = $arguments;

            $path = __DIR__ . '/../../app/Controllers/' . $controller_name . '.php';
        }
        else if(count($arguments) === 3){

            list($controller_folder, $controller_name, $controller_method) = $arguments;

            $path = __DIR__ . '/../../app/Controllers/' . $controller_folder . '/' . $controller_name . '.php';
        }
        else
            throw new \InvalidArgumentException;


        if(file_exists($path))
            require_once $path;
        else
            throw new \Exception('Controller not exist! File: <b>' . $path . '</b>');

        $controller = new $controller_name();

        if($controller->enabled()) {
            return $this->router->$name($pattern, [  $controller, $controller_method ]);
        }

        return false;
    }

    protected function getControllerMethod(string $param) {

        list($controllerArea, $controllerName, $controllerMethod) = explode('@', $param);

        return [
            'folder' => strtolower($controllerArea),
            'name' => $controllerName,
            'method' => $controllerMethod
        ];
    }

}