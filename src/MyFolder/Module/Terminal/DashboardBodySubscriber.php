<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\DashboardBodyEvent;
use IjorTengab\MyFolder\Module\User\UserSession;

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
        // @ todo, is user sysadmin.
        $user = new UserSession;
        if ($user->isAuthenticated()) {
            $event->registerCard(new CardTerminalPosition);
        }
    }
}
