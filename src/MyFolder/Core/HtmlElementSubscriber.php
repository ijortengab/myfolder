<?php

namespace IjorTengab\MyFolder\Core;

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
        $event->registerResource('core/js/jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->registerResource('core/js/jquery/once', 'https://cdn.jsdelivr.net/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->registerResource('core/js/popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->registerResource('core/js/bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->registerResource('core/css/bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->registerResource('core/css/bootstrap/icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        $event->registerResource('core/font/bootstrap/icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/fonts/bootstrap-icons.woff2');
        $event->registerResource('core/logo/myfolder/svg', '/assets/core/favicon.svg');
        $event->registerResource('core/favicon/myfolder/svg', '/assets/core/favicon.svg');
        $event->registerResource('core/js/local/date/format', '/assets/core/date-format.js');
        $event->registerResource('core/js/local/app', '/assets/core/app.js');
        $event->registerResource('core/js/local/ajax/class', '/assets/core/ajax-class.js');
        $event->registerResource('core/js/local/ajax/static', '/assets/core/ajax.js');
        $event->registerResource('core/js/local/modal/class', '/assets/core/modal-class.js');
        $event->registerResource('core/js/local/modal/static', '/assets/core/modal.js');
        $event->registerResource('core/js/local/offcanvas/class', '/assets/core/offcanvas-class.js');
        $event->registerResource('core/js/local/offcanvas/static', '/assets/core/offcanvas.js');
    }
}
