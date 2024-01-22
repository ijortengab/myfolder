<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class Index implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new BootSubscriber());
        $dispatcher->addSubscriber(new IndexInvokeCommandSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        $app->post('/index', 'IjorTengab\MyFolder\Module\Index\IndexController::route');
        $app->get('/dashboard', 'IjorTengab\MyFolder\Module\Index\DashboardController::route');
        $app->get('/dashboard/body', 'IjorTengab\MyFolder\Module\Index\DashboardBodyController::route');
        $app->get('/index/dashboard/root', 'IjorTengab\MyFolder\Module\Index\IndexDashboardRootController::route');
        $app->post('/index/dashboard/root', 'IjorTengab\MyFolder\Module\Index\IndexDashboardRootController::route');
        $app->get('/raw', 'IjorTengab\MyFolder\Module\Index\RawController::route');
    }
}
