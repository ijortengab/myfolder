<?php

namespace IjorTengab\MyFolder\Module\CtrlE;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlWrapperHtmlElementEvent;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => array('onHtmlElementEvent'),
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        // Override backdrop of <dialog> if watercss exist.
        $event->registerResource('ctrl_e/css/water-css', '/assets/ctrl-e/water.css', array('after' => 'markdown/css/water-css'));
    }
}
