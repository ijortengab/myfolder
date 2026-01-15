<?php

namespace IjorTengab\MyFolder\Core;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\BinaryFileResponse;

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
        $response = new BinaryFileResponse($event->getInfo());
        $event->setResponse($response);
    }
}
