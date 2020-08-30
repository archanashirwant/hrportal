<?php

class Dispatcher
{

    private $request;

    public function dispatch()
    {
        $this->request = new Request();
        Router::parse($this->request->url, $this->request);

        $controller = $this->loadController();

         if ((int)method_exists($controller, $this->request->action)) {
            call_user_func_array(array($controller,$this->request->action),array($this->request->params));
        } else {
            header('Location: /msgraph/User/display');
        }

       
    }

    public function loadController()
    {
        $name = $this->request->controller . "Controller";
        $controller = new $name();
        return $controller;
    }

}
?>