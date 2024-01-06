<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;

class TerminalController
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
                    // self::routeGet();
                }
                elseif ($has_query_part) {
                    // self::routeGetAjaxPart();
                }
                else {
                    self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routeGetAjax()
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
