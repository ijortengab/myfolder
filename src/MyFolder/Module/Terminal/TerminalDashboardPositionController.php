<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\ConfigLoader;
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
        $http_request = Application::getHttpRequest();
        $position = $http_request->request->get('position');

        $commands = array();
        // @todo: verifikasi input user.
        $config = ConfigLoader::module('terminal');
        $config->position = $position;
        // @todo, saving harus ada lock file.
        // cek lagi function lock yang ada di versi 0.1
        $config->save();
        // Kasih sleep aja deh, agar user tidak click
        // berkali-kali.
        sleep(1);
        $response = new JsonResponse(array(
            'commands' => $commands,
            'position' => $position,
        ));
        return $response->send();
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
        $config = ConfigLoader::module('terminal');
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

        /* $config = ConfigLoader::core();
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
