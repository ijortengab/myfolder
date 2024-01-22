<?php

namespace IjorTengab\MyFolder\Module\OfflineMode;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class OfflineMode implements ModuleInterface
{
    public static function handle($app)
    {
        $dispatcher = Application::getEventDispatcher();

        // Register event.
        $subscriber = new IndexHtmlElementPreRenderSubscriber();
        $dispatcher->addSubscriber($subscriber);
    }
}
