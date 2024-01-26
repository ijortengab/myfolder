<?php

namespace IjorTengab\MyFolder\Module\OfflineMode;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Module\Index\IndexHtmlElementPreRenderEvent;

class IndexHtmlElementPreRenderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            IndexHtmlElementPreRenderEvent::NAME => 'onIndexHtmlElementPreRenderEvent',
        );
    }
    public static function onIndexHtmlElementPreRenderEvent(IndexHtmlElementPreRenderEvent $event)
    {
        list($base_path,,) = Application::extractUrlInfo();
        $event->overrideJs('cdn_jquery', $base_path.'/___pseudo/root/assets/npm/jquery@3.7.0/dist/jquery.min.js');
        $event->overrideJs('cdn_jquery_once', $base_path.'/___pseudo/root/assets/npm/jquery-once@2.2.3/jquery.once.min.js');
        $event->overrideJs('cdn_popper', $base_path.'/___pseudo/root/assets/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js');
        $event->overrideJs('cdn_bootstrap', $base_path.'/___pseudo/root/assets/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js');
        $event->overrideCss('cdn_bootstrap', $base_path.'/___pseudo/root/assets/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
        $event->overrideCss('cdn_bootstrap_icons', $base_path.'/___pseudo/root/assets/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css');
        // @todo: beri info kalo mendownload css bootstrap-icons, maka juga perlu mendownload fonts nya.
        // @todo: beri info kalo mendownload file `bootstrap.min.css`, maka juga perlu mendownload file `bootstrap.min.css.map`.
        // begitu juga dengan asset lainnya.
    }
}
