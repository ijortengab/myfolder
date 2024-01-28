<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\DashboardBodyEvent;

class DashboardBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            DashboardBodyEvent::NAME => array('onDashboardBodyEvent', 100),
        );
    }
    public static function onDashboardBodyEvent(DashboardBodyEvent $event)
    {
        $user = new UserSession;
        if ($user->isAuthenticated()) {
            $event->registerCard(new CardUserSession);
        }

    }
}
