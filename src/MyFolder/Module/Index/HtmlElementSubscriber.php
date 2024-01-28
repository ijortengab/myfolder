<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ConfigHelper;
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
        $event->registerTemplate('index/navbar/item/dashboard', new Template\NavbarListDashboard, array(
            'label' => 'Dashboard',
            'url' => '/dashboard',
            'offcanvas' => array('name' => 'dashboard'),
        ));
        $event->registerResource('index/js/jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->registerResource('index/js/jquery/once', 'https://cdn.jsdelivr.net/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->registerResource('index/js/popper', 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->registerResource('index/js/bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->registerResource('index/css/bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->registerResource('index/css/bootstrap/icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        $event->registerResource('index/js/local/date/format', '/assets/index/date-format.js');
        $event->registerResource('index/js/local/app', '/assets/index/app.js');
        $event->registerResource('index/js/local/index/class', '/assets/index/index-class.js');
        $event->registerResource('index/js/local/index/static', '/assets/index/index.js');
        $event->registerResource('index/js/local/ajax/class', '/assets/index/ajax-class.js');
        $event->registerResource('index/js/local/ajax/static', '/assets/index/ajax.js');
        $event->registerResource('index/js/local/modal/class', '/assets/index/modal-class.js');
        $event->registerResource('index/js/local/modal/static', '/assets/index/modal.js');
        $event->registerResource('index/js/local/offcanvas/class', '/assets/index/offcanvas-class.js');
        $event->registerResource('index/js/local/offcanvas/static', '/assets/index/offcanvas.js');
    }
}
