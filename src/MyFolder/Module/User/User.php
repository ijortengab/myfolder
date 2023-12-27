<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class User implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new IndexSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        $app->get('/user/create', 'IjorTengab\MyFolder\Module\User\UserController::create');
        $app->post('/user/create', 'IjorTengab\MyFolder\Module\User\UserController::create');
        $app->get('/user/login', 'IjorTengab\MyFolder\Module\User\UserController::login');
        $app->post('/user/login', 'IjorTengab\MyFolder\Module\User\UserController::login');
        $app->get('/user/dashboard/session', 'IjorTengab\MyFolder\Module\User\UserDashboardController::session');
        $app->post('/user/logout', 'IjorTengab\MyFolder\Module\User\UserController::logout');
    }
}
