<?php

namespace IjorTengab\MyFolder\Core;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\RedirectResponse;
use IjorTengab\MyFolder\Core\BinaryFileResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;

class FilePreRenderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FilePreRenderEvent::NAME => 'onFilePreRenderEvent',
        );
    }
    public static function onFilePreRenderEvent(FilePreRenderEvent $event)
    {
        $fullpath = $event->getInfo()->getPathname();
        $response = new BinaryFileResponse($fullpath);
        return $response->send();
    }
}
