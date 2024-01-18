<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\BootEvent;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\BinaryFileResponse;
use IjorTengab\MyFolder\Core\Config;

class BootSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            BootEvent::NAME => 'onBootEvent',
        );
    }
    public static function onBootEvent(BootEvent $event)
    {
        $config = Config::load('index');
        $root = $config->root->value();
        null !== $root or $root = Application::$cwd;

        list($base_path, $path_info,) = Application::extractUrlInfo();
        $fullpath = $root.$path_info;
        if (is_dir($fullpath)) {
            if (substr($path_info, -1) != '/') {
                $url = $base_path.$path_info.'/';
                $response = new RedirectResponse($url);
                return $response->send();
            }
            else {
                return IndexController::route();
            }
        }
        if (is_file($fullpath)) {
            $response = new BinaryFileResponse($fullpath);
            return $response->send();
        }
        $response = new Response('Not Found.');
        $response->setStatusCode(404);
        return $response->send();
    }
}
