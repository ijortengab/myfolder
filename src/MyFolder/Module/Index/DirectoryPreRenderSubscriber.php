<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\DirectoryPreRenderEvent;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\BinaryFileResponse;
use IjorTengab\MyFolder\Core\Config;

class DirectoryPreRenderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            DirectoryPreRenderEvent::NAME => 'onDirectoryPreRenderEvent',
        );
    }
    public static function onDirectoryPreRenderEvent(DirectoryPreRenderEvent $event)
    {
        list($base_path, $path_info,) = Application::extractUrlInfo();
        if (substr($path_info, -1) != '/') {
            $url = $base_path.$path_info.'/';
            $response = new RedirectResponse($url);
            return $response->send();
        }
        else {
            return IndexController::route();
        }
    }
}
