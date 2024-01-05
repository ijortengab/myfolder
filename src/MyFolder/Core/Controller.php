<?php

namespace IjorTengab\MyFolder\Core;

class Controller
{
    /**
     * Default controller for `index.php`.
     * We don't do anything here, just run the boot event.
     */
    public static function get()
    {
        // Start boot event.
        $dispatcher = Application::getEventDispatcher();
        $event = new BootEvent();
        $dispatcher->dispatch($event, BootEvent::NAME);
    }
    public static function post()
    {
        // @todo, harusnya adalah event.
        $commands = array();
        $commands[] = array(
            'command' => 'modal',
            'options' => array(
                'name' => 'error',
                'bootstrapOptions' => array(
                    'backdrop' => 'static',
                    'keyboard' => false
                ),
                'layout' => array(
                    'title' => 'Error',
                    'body' => 'Callback not defined.',
                    'footer' => '...',
                ),
            ),
        );
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
