<?php

namespace IjorTengab\MyFolder\Module\CtrlE;

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
        $event->registerResource('core/js/html-wrapper/ctrl-e', '/assets/ctrl-e/app.js');
    }
}
