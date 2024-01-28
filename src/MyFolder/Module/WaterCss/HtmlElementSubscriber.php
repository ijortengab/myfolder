<?php

namespace IjorTengab\MyFolder\Module\WaterCss;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => 'onHtmlElementEvent',
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        $event->registerResource('markdown/css/water', 'https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css');
    }
}
