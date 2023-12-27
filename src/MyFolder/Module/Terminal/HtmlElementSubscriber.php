<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\HtmlElementEvent;

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
        $event->addList('index.terminal', new Template\NavbarListTerminal, array(
            'label' => 'Terminal',
            'url' => '/terminal',
            'offcanvas' => array('name' => 'terminal'),
        ));

    }
}
