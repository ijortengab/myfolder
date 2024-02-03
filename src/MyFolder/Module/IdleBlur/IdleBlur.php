<?php

namespace IjorTengab\MyFolder\Module\IdleBlur;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ModuleInterface;

class IdleBlur implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new HtmlWrapperHtmlElementSubscriber());
    }
}
