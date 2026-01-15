<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\Session;
use IjorTengab\MyFolder\Module\Index\IndexController;
use IjorTengab\MyFolder\Module\Index\IndexInvokeCommandEvent;

class UserLoginController
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
                    // self::routeGetAjaxPart();
                }
                else {
                    self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routePost()
    {
        $commands = array();
        $user = new UserSession;
        do {
            if ($user->isAuthenticated()) {
                $title = 'Sorry';
                $body = 'You are already logged in.';
                $footer = '';
                $modal_name = 'UserHaveLoggedIn';
                break;
            }
            // User input.
            $http_request = Application::getHttpRequest();
            $input_name = $http_request->request->get('name');
            $input_pass = $http_request->request->get('pass');

            // Database.
            $config = ConfigHelper::load('user');
            $name = $config->sysadmin->name->value();
            $pass = $config->sysadmin->pass->value();
            // Verify.
            if ($input_name == $name && password_verify($input_pass, $pass)) {
                $title = 'Success';
                $body = 'You are login now.';
                $footer = '';
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
            $footer = array(
                'button' => array(
                    array(
                        'text' => 'Try Again',
                        'class' => 'btn-primary',
                        'bind' => array(
                            array(
                                'event' => 'click',
                                'component' => 'modal',
                                'method' => 'toggle',
                                'name' => 'loginForm',
                            ),
                        ),
                    ),
                )
            );
            // @todo, beri flood per IP dan atau per user.
            // jika sudah lebih dari 5 kali gagal login.
            $modal_name = 'FailedLogin';
        }
        while (false);
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => $modal_name,
                'bootstrapOptions' => array(
                    // 'backdrop' => 'static',
                    // 'keyboard' => false
                ),
                'layout' => array(
                    'title' => $title,
                    'body' => $body,
                    'footer' => $footer,
                ),
            ),
        );
        $commands[] = array(
            'command' => 'fetch',
            'options' => array(
                'url' => '/',
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        $response->send();
    }
    protected static function routeGet()
    {
        $event = IndexInvokeCommandEvent::load();
        $event->setCommand(array(
            'command' => 'fetch',
            'options' => array(
                'url' => '/user/login',
            ),
        ));
        return IndexController::route();
    }
    protected static function routeGetAjax()
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
