<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Core\Response;

class RawController
{
    public static function route()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = !(null === $http_request->query->get('is_ajax'));
        $has_query_part = !(null === $http_request->query->get('part'));
        switch ($method) {
            case 'post':
                // self::routePost();
                break;
            case 'get':
                if (!$is_ajax) {
                    self::routeGet();
                }
                elseif ($has_query_part) {
                    // self::routeGetAjaxPart();
                }
                else {
                    // self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routeGet()
    {
        $config = Config::load('index');
        $root = $config->root->value();
        null !== $root or $root = Application::$cwd;

        $request = Application::getHttpRequest();
        $path = $request->query->get('path');
        $fullpath = $root.$path;
        if (is_file($fullpath)) {
            // @todo, gunakan object Response.
            header("Content-Type: text/plain");
            readfile($fullpath);
        }
        else {
            $response = new Response('Not Found.');
            $response->setStatusCode(404);
            return $response->send();
        }
    }
}
