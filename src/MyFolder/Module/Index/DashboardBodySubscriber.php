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
        if (class_exists('IjorTengab\MyFolder\Module\User\UserSession')) {
            $user = new UserSession;
            if ($user->isSysAdmin()) {
                $event->registerCard(new CardRootDirectory);
            }
        }
    }
}
