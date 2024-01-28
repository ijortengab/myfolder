<?php

namespace IjorTengab\MyFolder\Module\WaterCss;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class WaterCss implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
    }
}
