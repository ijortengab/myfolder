<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\IndexInvokeHtmlElementEvent;

class IndexInvokeHtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexInvokeHtmlElementEvent::NAME => 'onIndexInvokeHtmlElementEvent',
        );
    }
    public static function onIndexInvokeHtmlElementEvent(IndexInvokeHtmlElementEvent $event)
    {
        $event->addList('index.terminal', new Template\NavbarListTerminal, array(
            'label' => 'Terminal',
            'url' => '/terminal',
            'offcanvas' => array('name' => 'terminal'),
        ));

    }
}
