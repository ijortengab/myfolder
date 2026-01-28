<?php

namespace IjorTengab\MyFolder\Module\Markdown;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\FilePreRenderEvent;

class FilePreRenderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FilePreRenderEvent::NAME => array('onFilePreRenderEvent', -10),
        );
    }
    public static function onFilePreRenderEvent(FilePreRenderEvent $event)
    {
        $http_request = Application::getHttpRequest();
        $html = !(null === $http_request->query->get('html'));
        $info = $event->getInfo();
        // @todo configurable.
        if ($html && $info->isFile() && in_array(strtolower($info->getExtension()), array('md', 'markdown'))) {
            return MarkdownController::onFilePreRenderEvent();
        }
    }
}
