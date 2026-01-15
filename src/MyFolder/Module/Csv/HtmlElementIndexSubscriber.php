<?php

namespace IjorTengab\MyFolder\Module\Csv;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementIndexSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => array('onHtmlElementEvent', -10),
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        $event->registerResource('index/js/local/csv', '/assets/csv/index.js');
    }
}
