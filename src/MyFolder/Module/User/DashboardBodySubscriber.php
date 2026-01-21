<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\ConfigLoader;
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
        else {
            $config = ConfigLoader::module('user');
            $name = $config->sysadmin->name->value();
            $pass = $config->sysadmin->pass->value();
            if (empty($name) || empty($pass)) {
                $event->registerCard(new CardUserSysAdminCreate);
            }

        }

    }
}
