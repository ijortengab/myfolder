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
        // Javascript pada module index, mengubah semua link dengan memberi
        // prefix '/___pseudo' dengan tujuan agar membedakan dengan path
        // dari file/direktori.
        // Callback ini akan mengembalikan pathinfo ke posisi nya semula,
        // sehingga route yang didapat hasilnya sempurna.
        $pathinfo = $event->getPathInfo();
        if (str_starts_with($pathinfo, '/___pseudo')) {
            $pathinfo = substr($pathinfo,strlen('/___pseudo'));
            $event->setPathInfo($pathinfo);
        }
    }
}
