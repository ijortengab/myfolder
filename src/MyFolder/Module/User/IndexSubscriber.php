<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Module\Index\IndexEvent;

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
        $config = Config::load('user');
        $name = $config->sysadmin->name->value();
        $pass = $config->sysadmin->pass->value();

        // @todo, jika sebelumny ada session, lalu database di edit manual
        // menghapus user.
        // maka kita perlu destroy session.
        if (empty($pass)) {
            $event->setCommand(UserCreateController::getCommand());
        }
    }
}
