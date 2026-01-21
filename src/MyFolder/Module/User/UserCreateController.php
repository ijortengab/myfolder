<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigLoader;
use IjorTengab\MyFolder\Core\WriteException;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Module\Index\IndexController;
use IjorTengab\MyFolder\Module\Index\IndexInvokeCommandEvent;

/**
 * * Route for `/user/create`.
 */
class UserCreateController
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
                    self::routeGet();
                }
                elseif ($has_query_part) {
                    self::routeGetAjaxPart();
                }
                else {
                    self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routePost()
    {
        $http_request = Application::getHttpRequest();
        $name = $http_request->request->get('name');
        $pass = $http_request->request->get('pass');

        $config = ConfigLoader::module('user');
        $config->sysadmin->name = $name;
        $config->sysadmin->pass = $pass;

        $result = array();
        $commands = array();
        $title = 'Success.';
        $body = 'You can login now.';
        $modal_name = 'SuccessCreate';
        try {
            $config->save();
        }
        catch (WriteException $e) {
            $title = 'Attention.';
            $body = $e->getMessage();
            $modal_name = 'FailedCreate';
        }
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => $modal_name,
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => false
                ),
                'layout' => array(
                    'title' => $title,
                    'body' => $body,
                ),
            ),
        );
        $result['commands'] = $commands;
        $response = new JsonResponse($result);
        $response->send();
    }
    protected static function routeGet()
    {
        $event = IndexInvokeCommandEvent::load();
        $event->setCommand(array(
            'command' => 'fetch',
            'options' => array(
                'url' => '/user/create',
            ),
        ));
        return IndexController::route();
    }
    protected static function routeGetAjax()
    {
        $commands = array();
        $commands[] = self::getCommandUserCreateSysadmin();
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
    protected static function routeGetAjaxPart()
    {
        $commands = array();
        $http_request = Application::getHttpRequest();
        $query_part = (array) $http_request->query->get('part');
        if (in_array('body', $query_part)) {
            $body = (string) TwigFile::process(new Template\UserCreateFormBody, array(
                'url' => '/user/create',
            ));
            $commands[] = array(
                'command' => 'ajax',
                'options' => array(
                    'method' => 'html',
                    'selector' => '.modal-body',
                    'html' => $body,
                ),
            );
        }
        if (in_array('footer', $query_part)) {
            $footer = (string) (new Template\UserCreateFormFooter);
            $commands[] = array(
                'command' => 'ajax',
                'options' => array(
                    'method' => 'append',
                    'selector' => '.modal-footer',
                    'html' => $footer,
                ),
            );
        }
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
