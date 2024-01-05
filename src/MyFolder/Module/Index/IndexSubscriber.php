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
            'command' => 'index'
        ));
        $event->setCommand(array(
            'command' => 'ajax',
            'options' => array(
                'method' => 'append',
                'html' => 'anu',
            ),
        ));

        // for development only.
        // @todo hapus ini.
        $event->setCommand(array(
            'command' => 'offcanvasRegister',
            'options' => array(
                'name' => 'dashboard',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => true
                ),
                'layout' => array(
                    'fetch' => '/dashboard/body',
                    'title' => 'Dashboard',
                    'body' => 'Loading...',
                    'footer' => '',
                ),
            ),
        ));
    }
}
