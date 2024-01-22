<?php

namespace IjorTengab\MyFolder\Module\Terminal;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class Terminal implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new IndexInvokeHtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        $app->get('/terminal', 'IjorTengab\MyFolder\Module\Terminal\TerminalController::route');
        $app->get('/terminal/dashboard/position', 'IjorTengab\MyFolder\Module\Terminal\TerminalDashboardPositionController::route');
    }
}
