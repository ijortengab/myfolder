<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\ReloadBrowserEvent;

class ReloadBrowserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            ReloadBrowserEvent::NAME => 'onReloadBrowserEvent',
        );
    }
    public static function onReloadBrowserEvent(ReloadBrowserEvent $event)
    {
        $event->setCommand(array(
            'command' => 'index'
        ));
    }
}
