<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class User implements ModuleInterface
{
    public static function handle($app)
    {
        $dispatcher = Application::getEventDispatcher();

        // Register event.
        $subscriber = new IndexSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $subscriber = new HtmlElementSubscriber();
        $dispatcher->addSubscriber($subscriber);

        // Register route.
        $app->get('/user/create', 'IjorTengab\MyFolder\Module\User\UserController::create');
        $app->post('/user/create', 'IjorTengab\MyFolder\Module\User\UserController::create');
        $app->get('/user/login', 'IjorTengab\MyFolder\Module\User\UserController::login');
        $app->post('/user/login', 'IjorTengab\MyFolder\Module\User\UserController::login');
    }
}
