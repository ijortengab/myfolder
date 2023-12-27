<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;

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
