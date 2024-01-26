<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Module\Index\IndexInvokeCommandEvent;

class IndexInvokeCommandSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexInvokeCommandEvent::NAME => 'onIndexInvokeCommandEvent',
        );
    }
    public static function onIndexInvokeCommandEvent(IndexInvokeCommandEvent $event)
    {
        $config = ConfigHelper::load('user');
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
