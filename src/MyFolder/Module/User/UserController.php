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

class UserController
{
    public static function create()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = null === $http_request->query->get('is_ajax') ? false : true ;
        switch ($method) {
            case 'post':
                self::routeCreatePost();
                break;
            case 'get':
                $is_ajax ? self::routeCreateGetAjax() : self::routeCreateGet();
                break;
        }
    }

    protected static function routeCreatePost()
    {
        $http_request = Application::getHttpRequest();
        $name = $http_request->request->get('name');
        $pass = $http_request->request->get('pass');

        $config = new Config;
        $config->sysadmin->name = $name;
        $config->sysadmin->pass = $pass;

        $editor = new ConfigEditor;
        $editor->setClassName('Config', 'IjorTengab\MyFolder\Module\User');
        $result = array();
        $commands = array();
        $title = 'Success.';
        $body = 'You can login now.';
        $modal_name = 'SuccessCreate';
        try {
            $editor->set($config);
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

    protected static function routeCreateGet()
    {
        return IndexController::index();
    }

    protected static function routeCreateGetAjax()
    {
        $commands = array();
        $http_request = Application::getHttpRequest();
        $query_part = $http_request->query->get('part');
        if (is_array($query_part)) {
            if (in_array('body', $query_part)) {
                $body = (string) TwigFile::process(new Template\UserCreateFormBody, array(
                    'url' => '/user/create',
                ));
                $commands[] = array(
                    'command' => 'ajax',
                    'options' => array(
                        'method' => 'replace',
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
            if (count($query_part)) {
                $commands[] = array(
                    'command' => 'ajax',
                    'options' => array(
                        'method' => 'script',
                        'ajax' => array(
                            'method' => 'get',
                            'url' => '/assets/user/modal-create-user.js',
                        ),
                    ),
                );
            }
            $response = new JsonResponse(array(
                'commands' => $commands,
            ));
            return $response->send();
        }

        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => 'sysadminCreateAccountForm',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => false
                ),
                'layout' => array(
                    // 'ajax' => array(
                        // 'method' => 'get',
                        // 'url' => '/user/create',
                    // ),
                    'title' => 'Create Account',
                    'body' => 'Loading...',
                    'footer' => '...',
                ),
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();

        return;
        $body = (string) (new Template\UserCreateFormBody);
        $footer = (string) (new Template\UserCreateFormFooter);
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'replace',
                'selector' => '.modal-body',
                'html' => $body,
            ),
        );
    }

    public static function login()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = null === $http_request->query->get('is_ajax') ? false : true ;
        switch ($method) {
            case 'post':
                self::routeLoginPost();
                break;
            case 'get':
                $is_ajax ? self::routeLoginGetAjax() : self::routeLoginGet();
                break;
        }
    }

    protected static function routeLoginPost()
    {
        $commands = array();
        $user = new UserSession;
        do {
            if ($user->isAuthenticated()) {
                $title = 'Sorry';
                $body = 'You are already logged in.';
                $modal_name = 'userHaveLoggedIn';
                break;
            }
            // User input.
            $http_request = Application::getHttpRequest();
            $input_name = $http_request->request->get('name');
            $input_pass = $http_request->request->get('pass');
            // Database.
            $editor = new ConfigEditor;
            $editor->setClassName('Config', 'IjorTengab\MyFolder\Module\User');
            $config = new Config;
            $config->parse($editor->get());
            $name = $config->sysadmin->name->value();
            $pass = $config->sysadmin->pass->value();
            // Verify.
            if ($input_name == $name && $input_pass == $pass) {
                $title = 'Success';
                $body = 'You are login now.';
                $modal_name = 'SuccessLogin';
                $session = Session::load();
                $session->start();
                $session->set('logged', true);
                $session->set('username', $name);
                $commands[] = array(
                    'command' => 'ajax',
                    'options' => array(
                        'method' => 'remove',
                        'selector' => 'li.nav-item > a[data-myfolder-modal-name=loginForm]',
                    ),
                );
                break;
            }
            // Failed.
            $title = 'Failed';
            $body = 'Either username or password is not match.';
            $modal_name = 'FailedLogin';
        }
        while (false);
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
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        $response->send();
    }

    protected static function routeLoginGet()
    {
        // $user = new UserSession;
        // if ($user->isAuthenticated()) {
            // list($base_path,,) = Application::extractUrlInfo();
            // $path_info = '/';
            // $url = $base_path.$path_info;
            // $response = new RedirectResponse($url);
            // return $response->send();
        // }
        $event = IndexEvent::load();
        $event->setCommand(array(
            'command' => 'fetch',
            'options' => array(
                'url' => '/user/login',
            ),
        ));
        return IndexController::index();
    }

    protected static function routeLoginGetAjax()
    {
        $commands = array();
        $title = 'Login';
        $body = (string) (new Template\UserLoginFormBody);
        $footer = (string) (new Template\UserLoginFormFooter);
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => 'loginForm',
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
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
