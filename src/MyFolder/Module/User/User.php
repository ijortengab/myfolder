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
        $dispatcher->addSubscriber(new IndexPreRenderSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());
        $dispatcher->addSubscriber(new DashboardBodySubscriber());

        // Register route.
        $app->get('/user/create', 'IjorTengab\MyFolder\Module\User\UserCreateController::route');
        $app->post('/user/create', 'IjorTengab\MyFolder\Module\User\UserCreateController::route');
        $app->get('/user/login', 'IjorTengab\MyFolder\Module\User\UserLoginController::route');
        $app->post('/user/login', 'IjorTengab\MyFolder\Module\User\UserLoginController::route');
        $app->get('/user/dashboard/session', 'IjorTengab\MyFolder\Module\User\UserDashboardSessionController::route');
        $app->post('/user/logout', 'IjorTengab\MyFolder\Module\User\UserLogoutController::route');
    }
}
