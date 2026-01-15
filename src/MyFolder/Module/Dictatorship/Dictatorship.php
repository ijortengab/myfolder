<?php

namespace IjorTengab\MyFolder\Module\Dictatorship;

use IjorTengab\MyFolder\Core\ModuleInterface;
use IjorTengab\MyFolder\Core\Application;

class Dictatorship implements ModuleInterface
{
    public static function handle($app)
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new EmergencyPolicySubscriber());
    }
}
