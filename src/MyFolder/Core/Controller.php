<?php

namespace IjorTengab\MyFolder\Core;

class Controller
{
    /**
     * Default controller for `index.php`.
     */
    public static function get()
    {
        // Start boot event.
        $dispatcher = Application::getEventDispatcher();
        $event = new BootEvent();
        $dispatcher->dispatch($event, BootEvent::NAME);

        $config = ConfigHelper::load();
        $root = $config->root->value();
        null !== $root or $root = Application::$cwd;

        list($base_path, $path_info,) = Application::extractUrlInfo();

        // Decode karena dijadikan sebagai path file system.
        $path_info = urldecode($path_info);
        $fullpath = $root.$path_info;

        if (is_file($fullpath)) {
            $dispatcher = Application::getEventDispatcher();
            $event = FilePreRenderEvent::load();
            $event->setInfo(new \SplFileInfo($fullpath));
            $dispatcher->dispatch($event, FilePreRenderEvent::NAME);
        }
        elseif (is_dir($fullpath)) {
            $dispatcher = Application::getEventDispatcher();
            $event = DirectoryPreRenderEvent::load();
            $event->setInfo(new \SplFileInfo($fullpath));
            $dispatcher->dispatch($event, DirectoryPreRenderEvent::NAME);
        }
        else {
            $response = new Response('Not Found.');
            $response->setStatusCode(404);
            return $response->send();
        }
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
