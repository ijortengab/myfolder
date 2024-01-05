<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Session;

class UserLogoutController
{
    public static function route()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = !(null === $http_request->query->get('is_ajax'));
        $has_query_part = !(null === $http_request->query->get('part'));
        switch ($method) {
            case 'post':
                self::routePost();
                break;
            case 'get':
                if (!$is_ajax) {
                    // self::routeGet();
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
    public static function routePost()
    {
        $session = Session::load();
        $session->destroy();
        $title = 'Logout successful.';
        $body = 'Logout successful.';
        $footer = 'Logout successful.';
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => 'logoutSuccess',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => false
                ),
                'layout' => array(
                    'title' => $title,
                    'body' => array(
                        'html' => $body,
                    ),
                    'footer' => array(
                        'html' => $footer,
                    ),
                ),
            ),
        );
        $commands[] = array(
            'command' => 'offcanvasHide',
        );
        $list = (string) TwigFile::process(new Template\NavbarListLogin, array(
            'label' => 'Log in',
        ));
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'prepend',
                'selector' => 'ul.navbar-nav',
                'html' => $list,
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
