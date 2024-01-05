<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\RedirectResponse;

class DashboardBodyController
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
                    self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routeGet()
    {
        // Harusnya user tidak perlu ada disini karena tidak ada link
        // visible untuk di-click, jadi redirect saja ke home.
        list($base_path,,) = Application::extractUrlInfo();
        $path_info = '/';
        $url = $base_path.$path_info;
        $response = new RedirectResponse($url);
        return $response->send();
    }
    protected static function routeGetAjax()
    {
        $dispatcher = Application::getEventDispatcher();
        $event = DashboardBodyEvent::load();
        $dispatcher->dispatch($event, DashboardBodyEvent::NAME);
        $cards = $event->getCards();
        $placeholders = array_map(function ($each) {
            return (string) $each->getPlaceholders();
        }, $cards);
        $routes = array_map(function ($each) {
            return (string) $each->getRoute();
        }, $cards);
        $commands = array();
        // $commands[] = array(
            // 'command' => 'cards',
            // 'options' => array(
                // 'placeholders' => $placeholders,
                // 'routes' => $routes,
            // ),
        // );
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'html',
                'selector' => '.offcanvas-body',
                'html' => $placeholders,
            ),
        );
        foreach ($routes as $url) {
            $commands[] = array(
                'command' => 'ajax',
                'options' => array(
                    'method' => 'fetch',
                    'url' => $url,
                ),
            );
        }

        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
