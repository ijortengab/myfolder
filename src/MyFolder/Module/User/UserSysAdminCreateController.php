<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\WriteException;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Module\Index\IndexController;
use IjorTengab\MyFolder\Module\Index\IndexInvokeCommandEvent;

/**
 * * Route for `/user/sysadmin/create`.
 */
class UserSysAdminCreateController
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
        $action = $http_request->request->get('action');

        $config = ConfigHelper::load('user');
        $config->sysadmin->name = $name;
        $config->sysadmin->pass = password_hash($pass, PASSWORD_DEFAULT);

        $result = array();
        $commands = array();
        try {
            ConfigHelper::save($config);
            $title = 'Success.';
            $body = 'You can login now.';
            $modal_name = 'SuccessCreate';
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
                'url' => '/user/sysadmin/create',
            ),
        ));
        return IndexController::route();
    }
    protected static function routeGetAjax()
    {
        $commands = array();
        $body = (string) TwigFile::process(new Template\CardUserSysAdminCreate, array(
            'sysadmin_default' => 'SysAdmin',
            'description' => 'Create account for yourself before continue.',
            'url' => '/user/sysadmin/create',
        ));
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'replaceWith',
                'selector' => '#card-user-sysadmin-create',
                'html' => $body,
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
    protected static function routeGetAjaxPart()
    {
    }
}
