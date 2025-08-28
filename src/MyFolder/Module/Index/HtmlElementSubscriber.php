<?php

namespace IjorTengab\MyFolder\Module\Index;

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
        $event->registerResource('index/js/local/index/class', '/assets/index/index-class.js');
        $event->registerResource('index/js/local/index/static', '/assets/index/index.js');
        $event->registerResource('index/js/local/index/filter', '/assets/index/index-filter.js');
    }
}
