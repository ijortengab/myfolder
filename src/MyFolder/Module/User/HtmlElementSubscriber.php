<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\HtmlElementEvent;

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
            $event->addList('user.login', new Template\NavbarListLogin, array(
                'label' => 'Log in',
            ));
        }
    }
}
