<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\IndexInvokeHtmlElementEvent;

class IndexInvokeHtmlElementSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexInvokeHtmlElementEvent::NAME => 'onIndexInvokeHtmlElementEvent',
        );
    }
    public static function onIndexInvokeHtmlElementEvent(IndexInvokeHtmlElementEvent $event)
    {
        $user = new UserSession;
        if ($user->isAnonymous()) {
            $event->addList('user.login', new Template\NavbarListLogin, array(
                'label' => 'Log in',
            ));
        }
    }
}
