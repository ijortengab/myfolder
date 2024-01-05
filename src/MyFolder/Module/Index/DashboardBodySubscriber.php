<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
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
        // if (AccessControl::judge('a|b|(c&d)', new UserAccessControl) {
        // }
        // @ todo, is user sysadmin.
        $user = new UserSession;
        if ($user->isAuthenticated()) {
            $event->registerCard(new CardRootDirectory);
        }
    }
}
