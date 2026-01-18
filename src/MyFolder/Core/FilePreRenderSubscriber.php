<?php

namespace IjorTengab\MyFolder\Core;

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
