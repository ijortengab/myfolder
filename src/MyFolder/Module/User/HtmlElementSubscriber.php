<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\ConfigLoader;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

class HtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementEvent::NAME => 'onHtmlElementEvent',
        );
    }
    public static function onHtmlElementEvent(HtmlElementEvent $event)
    {
        $config = ConfigLoader::module('user');
        $name = $config->sysadmin->name->value();
        $pass = $config->sysadmin->pass->value();

        $user = new UserSession;
        if ($user->isAnonymous() && !(empty($name) || empty($pass))) {
            $event->registerTemplate('index/navbar/item/user/login', new Template\NavbarListLogin, array(
                'label' => 'Log in',
            ));
        }
    }
}
