<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;

class IndexSubscriber implements EventSubscriberInterface 
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexEvent::NAME => 'onIndexEvent',
        );
    }
    public static function onIndexEvent(IndexEvent $event)
    {
        $event->setCommand(array(
            'command' => 'index',
            'options' => array(
            ),
        ));
    }
}
