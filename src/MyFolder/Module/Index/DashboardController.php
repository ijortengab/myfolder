<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;

class DashboardController
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
        $event = IndexPreRenderEvent::load();
        $event->setCommand(array(
            'command' => 'fetch',
            'options' => array(
                'url' => '/dashboard',
            ),
        ));
        return IndexController::route();
    }
    protected static function routeGetAjax()
    {
        $commands = array();
        $title = 'Dashboard';
        $commands[] = array(
            'command' => 'offcanvas',
            'options' => array(
                'name' => 'dashboard',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => true
                ),
                'layout' => array(
                    'fetch' => '/dashboard/body',
                    'title' => 'Dashboard',
                    'body' => 'Loading...',
                    'footer' => '',
                ),
                // 'layout' => array(
                    // 'size' => 'Fullscreen',
                    // 'title' => $title,
                    // 'body' => array(
                        // 'html' => $body,
                    // ),
                    // 'footer' => array(
                        // 'html' => $footer,
                    // ),
                    // 'ajax' => array(
                        // 'method' => 'addClass',
                        // 'selector' => '.modal-dialog',
                        // 'value' => 'modal-fullscreen',
                    // ),
                // ),
            ),
        );
        // $commands[] = array(
            // 'command' => 'ajax',
            // 'options' => array(
            // ),
        // );

        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
