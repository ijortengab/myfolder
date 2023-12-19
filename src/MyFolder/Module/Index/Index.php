<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class Index implements ModuleInterface
{
    public static function handle($app)
    {
        $dispatcher = Application::getEventDispatcher();

        // Register event.
        $subscriber = new BootSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $subscriber = new IndexSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $subscriber = new HtmlElementSubscriber();
        $dispatcher->addSubscriber($subscriber);

        // Register route.
        $app->post('/index', 'IjorTengab\MyFolder\Module\Index\IndexController::index');
        $app->get('/dashboard', 'IjorTengab\MyFolder\Module\Index\IndexController::dashboard');
    }
}
