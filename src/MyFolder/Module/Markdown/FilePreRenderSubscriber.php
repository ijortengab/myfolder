<?php

namespace IjorTengab\MyFolder\Module\Markdown;

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
        $info = $event->getInfo();
        // @todo configurable.
        if ($info->isFile() && in_array($info->getExtension(), array('md', 'markdown', 'MD', 'MARKDOWN'))) {
            return MarkdownController::onFilePreRenderEvent();
        }
    }
}
