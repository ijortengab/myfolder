<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\RouteRegisterEvent;

class RouteRegisterSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            RouteRegisterEvent::NAME => 'onRouteRegisterEvent',
        );
    }
    public static function onRouteRegisterEvent(RouteRegisterEvent $event)
    {
        $pathinfo = $event->getPathInfo();
        $callback = $event->getCallback();
        if ($pathinfo === '/') {
            return;
        }
        if (str_starts_with($pathinfo, '/___pseudo')) {
            return;
        }
        $pathinfo = '/___pseudo'.$pathinfo;
        $event->setPathInfo($pathinfo);
    }
}
