<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;

class TerminalController
{
    public static function terminal()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = null === $http_request->query->get('is_ajax') ? false : true ;
        switch ($method) {
            case 'post':
                self::routeTerminalPost();
                break;
            case 'get':
                $is_ajax ? self::routeTerminalGetAjax() : self::routeTerminalGet();
                break;
        }
    }
    protected static function routeTerminalPost()
    {
    }
    protected static function routeTerminalGet()
    {
        echo __FUNCTION__;
    }
    protected static function routeTerminalGetAjax()
    {

        $commands = array();
        $title = 'Dashboard';
        $footer = '';//(string) (new Template\UserLoginFormFooter);
        $commands[] = array(
            'command' => 'offcanvas',
            'options' => array(
                'name' => 'terminal',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => true
                ),
                'layout' => array(
                    'placement' => 'bottom',
                    // 'fetch' => '/dashboard/body',
                    'title' => 'Terminal',
                    'body' => 'Loading...',
                ),
            ),
        );

        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
