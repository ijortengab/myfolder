<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\TwigFile;

class TerminalDashboardPositionController
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
        /* $commands = array();
        $http_request = Application::getHttpRequest();
        $root = $http_request->request->get('root');

        // @todo.
        // verifikasi is_dir.
        // cannot write. dll.
        // Load.
        $config = ConfigHelper::load();
        // Set.
        $config->root = $root;

        $title = 'Success.';
        $body = 'Saved.';
        $modal_name = 'SuccessSavedRootDirectory';
        try {
            ConfigHelper::save($config);
        }
        catch (WriteException $e) {
            $title = 'Attention.';
            $body = $e->getMessage();
            $modal_name = 'FailedSavedRootDirectory';
        }
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => $modal_name,
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => true
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
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'html',
                'selector' => '#card-root-directory .card-body p.card-text',
                'html' => htmlentities($root),
            ),
        );
        $commands[] = array(
            'command' => 'settings',
            'options' => array(
                'pathInfo' => '/',
                'commands' => array(
                    array(
                        'command' => 'ajax',
                        'options' => array(
                            'method' => 'html',
                            'selector' => '#table-main tbody',
                            'html' => 'Loading...',
                        ),
                    ),
                    array(
                        'command' => 'index',
                        'options' => array(
                            'root' => $root,
                        ),
                    ),
                ),
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        $response->send(); */

    }
    protected static function routeGet()
    {
        /* list($base_path,,) = Application::extractUrlInfo();
        $path_info = '/';
        $url = $base_path.$path_info;
        $response = new RedirectResponse($url);
        return $response->send(); */
    }
    protected static function routeGetAjax()
    {
        $config = ConfigHelper::load('terminal');
        $position =  $config->position->value();
        null !== $position or $position = 'bottom';

        $commands = array();
        $body = (string) TwigFile::process(new Template\CardTerminalPosition, array(
            'title' => 'Terminal Position',
            'label' => array(
                'top' => 'Top',
                'bottom' => 'Bottom',
                'full' => 'Full Screen',
            ),
            'open' => 'Open',
            'value' => array(
                $position => true,
            ),
        ));
        $commands[] = array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'replaceWith',
                'selector' => '#card-terminal-position',
                'html' => $body,
            ),
        );
        /*
        $commands[] = array(
            'command' => 'modalRegister',
            'options' => array(
                'name' => 'IndexDashboardRootDirectory',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => false
                ),
                'layout' => array(
                    'fetch' => '/index/dashboard/root?part[]=body&part[]=footer',
                    'title' => 'Edit',
                    'body' => 'Loading...',
                    'footer' => '...',
                ),
            ),
        );
         */

        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
    protected static function routeGetAjaxPart()
    {

        /* $config = ConfigHelper::load();
        $root = $config->root->value();
        // Jadikan empty string agar user ngeh bahwa belum di set.
        null !== $root or $root = '';

        $commands = array();
        $http_request = Application::getHttpRequest();
        $query_part = (array) $http_request->query->get('part');
        if (in_array('body', $query_part)) {
            $body = (string) TwigFile::process(new Template\RootFormBody, array(
                'root' => htmlentities($root),
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
            $footer = (string) (new Template\RootFormFooter);
            $commands[] = array(
                'command' => 'ajax',
                'options' => array(
                    'method' => 'html',
                    'selector' => '.modal-footer',
                    'html' => $footer,
                ),
            );
        }
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send(); */
    }
}
