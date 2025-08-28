<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementGetResourcesEvent;

class HtmlElementGetResourcesSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            HtmlElementGetResourcesEvent::NAME => 'onHtmlElementGetResourcsEvent',
        );
    }
    public static function onHtmlElementGetResourcsEvent(HtmlElementGetResourcesEvent $event)
    {
        // Tambahkan `___pseudo`.
        list($base_path,,) = Application::extractUrlInfo();
        $storage = $event->getResources();
        foreach ($storage as $id => &$value) {
            if (fnmatch('/*', $value)) {
                $value = $base_path.'/___pseudo' . $value;
            }
        }
        $event->setResources($storage);
    }
}
