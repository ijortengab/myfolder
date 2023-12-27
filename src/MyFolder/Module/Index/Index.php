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
        $dispatcher->addSubscriber(new IndexSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        $app->post('/index', 'IjorTengab\MyFolder\Module\Index\IndexController::index');
        $app->get('/dashboard', 'IjorTengab\MyFolder\Module\Index\IndexController::dashboard');
        $app->get('/dashboard/body', 'IjorTengab\MyFolder\Module\Index\IndexController::dashboardBody');
    }
}
