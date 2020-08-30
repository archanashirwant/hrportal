<?php

class Router
{

    static public function parse($url, $request)
    {
        $url = trim($url);
        
        $urlArray = array();
        $urlPart = explode("?",$url);
        $queryString =  parse_url($url,PHP_URL_QUERY);
        parse_str($queryString, $param);
        $urlArray = explode("/",$urlPart[0]);
        

        $controller = !empty($urlArray[2]) ? $urlArray[2] : 'User';
            array_shift($urlArray);
        $action = !empty($urlArray[2]) ? $urlArray[2] : 'display';


        $tokenCache = new TokenCache();
		$accessToken = $tokenCache->getAccessToken();

        $defaultController = '';
        $defaultAction ='';
		if(empty($accessToken) ){
            $controller = 'Auth';
            $action = 'sign';
            if($urlArray[2]=='callback')
                $action= 'callback';
        }

        $request->controller = ucwords($controller);
        $request->action = $action;       
        $request->params = $param;
    }
}
?>