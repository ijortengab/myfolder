<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Core\WriteException;
use IjorTengab\MyFolder\Core\ConfigEditor;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\Session;
use IjorTengab\MyFolder\Module\Index\IndexController;
use IjorTengab\MyFolder\Module\Index\IndexEvent;

class UserDashboardController
{
    public static function session()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = null === $http_request->query->get('is_ajax') ? false : true ;
        switch ($method) {
            case 'get':
                $is_ajax ? self::routeSessionGetAjax() : self::routeSessionGet();
                break;
        }
    }

    protected static function routeSessionGet()
    {
        // $user = new UserSession;
        // if ($user->isAuthenticated()) {
            list($base_path,,) = Application::extractUrlInfo();
            $path_info = '/';
            $url = $base_path.$path_info;
            $response = new RedirectResponse($url);
            return $response->send();
        // }

    }

    protected static function routeSessionGetAjax()
    {
        // @todo, for devel only. hapus ini.
        // sleep(10); 
        $commands = array();
        $body = (string) (new Template\CardUserSession);
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'replace',
                'selector' => '#card-user-session',
                'html' => $body,
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
