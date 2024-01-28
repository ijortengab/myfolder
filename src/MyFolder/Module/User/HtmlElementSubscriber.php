<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ConfigHelper;
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
        $user = new UserSession;
        if ($user->isAnonymous()) {
            $event->registerTemplate('index/navbar/item/user/login', new Template\NavbarListLogin, array(
                'label' => 'Log in',
            ));
        }
    }
}
