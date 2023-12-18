<?php

namespace IjorTengab\MyFolder\Core;

class Controller
{
    /**
     * Default controller for `index.php`.
     * We don't do anything here, just run the boot event.
     */
    public static function index()
    {
        // Start boot event.
        $dispatcher = Application::getEventDispatcher();
        $event = new BootEvent();
        $dispatcher->dispatch($event, BootEvent::NAME);
    }
}
