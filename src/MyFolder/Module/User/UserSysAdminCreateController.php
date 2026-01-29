<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigLoader;
use IjorTengab\MyFolder\Core\WriteException;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\ConfigReplaceTemplate;
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
                self::routePost($http_request);
                break;
            case 'get':
                if (!$is_ajax) {
                    self::routeGet();
                }
                elseif ($has_query_part) {
                    self::routeGetAjaxPart($http_request);
                }
                else {
                    self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routePost($http_request)
    {
        // @todo csrf token.
        $name = $http_request->request->get('name');
        $pass = $http_request->request->get('pass');
        $action = $http_request->request->get('action');
        $commands = array();
        $result = array();
        $config = ConfigLoader::module('user');
        $config->sysadmin->name = $name;
        $config->sysadmin->pass = password_hash($pass, PASSWORD_DEFAULT);

        try {
            $config->save();
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
                    // 'backdrop' => 'static',
                    // 'keyboard' => false
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
            'urlAsk' => '/user/sysadmin/create?part=ask-confirmation',
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
    protected static function routeGetAjaxPart($http_request)
    {
        $part = $http_request->query->get('part');
        switch ($part) {
            case 'ask-confirmation':
                $title = 'Confirmation of creation file config-replace.php';
                $body = (string) TwigFile::process(new Template\UserSysAdminCreateConfirmationFormBody);
                $footer = (string) TwigFile::process(new Template\UserSysAdminCreateConfirmationFormFooter, array(
                    'urlyes' => '/user/sysadmin/create?part=confirmation-yes',
                    'urlno' => '/user/sysadmin/create?part=confirmation-no',
                    'yes' => 'Yes',
                    'no' => 'No',
                ));
                $modal_name = 'UserSysAdminCreateConfirmationForm';
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
                            'body' => array('html' => $body),
                            'footer' => array('html' => $footer),
                        ),
                    ),
                );
                break;

            case 'confirmation-yes':
                ConfigReplaceTemplate::init('user');
                $commands[] = array(
                    'command' => 'ajax',
                    'options' => array(
                        'method' => 'submit',
                        'selector' => '#form-create',
                    ),
                );
                break;

            case 'confirmation-no':
                $commands[] = array(
                    'command' => 'ajax',
                    'options' => array(
                        'method' => 'submit',
                        'selector' => '#form-create',
                    ),
                );
                break;

            default:
                $commands = array();
                break;
        }

        $result['commands'] = $commands;
        $response = new JsonResponse($result);
        $response->send();
    }
}
