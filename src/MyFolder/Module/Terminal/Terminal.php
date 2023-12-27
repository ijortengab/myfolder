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
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        // $app->post('/index', 'IjorTengab\MyFolder\Module\Index\IndexController::index');
        $app->get('/terminal', 'IjorTengab\MyFolder\Module\Terminal\TerminalController::terminal');
        // $app->get('/dashboard/body', 'IjorTengab\MyFolder\Module\Index\IndexController::dashboardBody');
    }
}
