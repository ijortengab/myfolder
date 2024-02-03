<?php

namespace IjorTengab\MyFolder\Module\CtrlE;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\ModuleInterface;

class CtrlE implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new HtmlWrapperHtmlElementSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
    }
}
