<?php

namespace IjorTengab\MyFolder\Module\Csv;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class Csv implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new FilePreRenderSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new HtmlElementIndexSubscriber());
    }
}
