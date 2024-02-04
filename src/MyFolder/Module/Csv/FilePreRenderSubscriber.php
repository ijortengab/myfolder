<?php

namespace IjorTengab\MyFolder\Module\Csv;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;
use IjorTengab\MyFolder\Core\EventDispatcher;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class FilePreRenderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FilePreRenderEvent::NAME => ['onFilePreRenderEvent', -10],
        );
    }
    public static function onFilePreRenderEvent(FilePreRenderEvent $event)
    {
        $http_request = Application::getHttpRequest();
        $html = !(null === $http_request->query->get('html'));
        $info = $event->getInfo();
        // @todo configurable.
        if ($html && $info->isFile() && in_array(strtolower($info->getExtension()), array('tsv', 'csv'))) {
            return CsvController::onFilePreRenderEvent();
        }
    }
}
