<?php

namespace IjorTengab\MyFolder\Module\IdleBlur;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlWrapperHtmlElementEvent;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlWrapperHtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlWrapperHtmlElementEvent::NAME => array('onHtmlWrapperHtmlElementEvent'),
        );
    }
    public static function onHtmlWrapperHtmlElementEvent(HtmlElementEvent $event)
    {
        $event->registerResource('core/js/html-wrapper/idle-blur', '/assets/idle-blur/app.js');
        $event->registerResource('core/css/html-wrapper/idle-blur', '/assets/idle-blur/style.css');
    }
}
