<?php

namespace IjorTengab\MyFolder\Module\OfflineMode;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\EventSubscriberInterface;
use IjorTengab\MyFolder\Core\HtmlElementGetResourcesEvent;
use IjorTengab\MyFolder\Core\ConfigHelper;

class HtmlElementGetResourcesSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            // Diatas module index.
            HtmlElementGetResourcesEvent::NAME => array('onHtmlElementGetResourcsEvent', 10),
        );
    }
    public static function onHtmlElementGetResourcsEvent(HtmlElementGetResourcesEvent $event)
    {
        // Jika offline_mode, maka prefix http perlu diganti menjadi local.
        list($base_path,,) = Application::extractUrlInfo();
        $storage = $event->getResources();
        $offline_mode = (bool) ConfigHelper::load()->offline_mode->value();

        foreach ($storage as $id => &$value) {
            if ($offline_mode && fnmatch('https://*', $value)) {
                $value = str_replace('https://', '/root/cdn/', $value);
            }
        }
        $event->setResources($storage);
    }
}
