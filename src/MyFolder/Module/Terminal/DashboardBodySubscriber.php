<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\DashboardBodyEvent;

class DashboardBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            DashboardBodyEvent::NAME => 'onDashboardBodyEvent',
        );
    }
    public static function onDashboardBodyEvent(DashboardBodyEvent $event)
    {

        // $event->registerCard();
    }
}
