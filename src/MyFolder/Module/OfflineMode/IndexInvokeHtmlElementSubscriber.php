<?php

namespace IjorTengab\MyFolder\Module\OfflineMode;

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
        $event->overrideJs('jquery', '{{ settings.basePath }}/___pseudo/root/assets/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->overrideJs('jquery.once', '{{ settings.basePath }}/___pseudo/root/assets/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->overrideJs('popper', '{{ settings.basePath }}/___pseudo/root/assets/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->overrideJs('bootstrap', '{{ settings.basePath }}/___pseudo/root/assets/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->overrideCss('bootstrap', '{{ settings.basePath }}/___pseudo/root/assets/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->overrideCss('bootstrap-icons', '{{ settings.basePath }}/___pseudo/root/assets/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        // @todo: beri info kalo mendownload css bootstrap-icons, maka juga perlu mendownload fonts nya.
        // @todo: beri info kalo mendownload file `bootstrap.min.css`, maka juga perlu mendownload file `bootstrap.min.css.map`.
        // begitu juga dengan asset lainnya.
    }
}
