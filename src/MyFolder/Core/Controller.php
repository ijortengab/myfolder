<?php

namespace IjorTengab\MyFolder\Core;

class Controller
{
    public static function route()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = !(null === $http_request->query->get('is_ajax'));
        $has_query_part = !(null === $http_request->query->get('part'));
        switch ($method) {
            case 'post':
                self::post();
                break;
            case 'get':
                if (!$is_ajax) {
                    self::get();
                }
                elseif ($has_query_part) {
                    // self::getAjaxPart();
                }
                else {
                    self::getAjax();
                }
                break;
        }
    }
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
            $response = $event->getResponse();
            return $response->send();
        }
        elseif (is_dir($fullpath)) {
            $dispatcher = Application::getEventDispatcher();
            $event = DirectoryPreRenderEvent::load();
            $event->setInfo(new \SplFileInfo($fullpath));
            $dispatcher->dispatch($event, DirectoryPreRenderEvent::NAME);
            // Direktori listing sepenuhnya di handle oleh module.
            // Core tidak memberikan respond.
        }
        elseif (substr($path_info, -1) == '/') {
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
        $config = ConfigHelper::load();
        $root = $config->root->value();
        null !== $root or $root = Application::$cwd;
        list($base_path, $path_info,) = Application::extractUrlInfo();
        // Decode karena dijadikan sebagai path file system.
        $path_info = urldecode($path_info);
        $fullpath = $root.$path_info;
        $http_request = Application::getHttpRequest();
        $is_html = !(null === $http_request->query->get('html'));
        $contents = (string) $http_request->request->get('contents');
        file_put_contents($fullpath, $contents);
    }
    public static function getAjax()
    {
        $dispatcher = Application::getEventDispatcher();
        $event = new ReloadBrowserEvent;
        $dispatcher->dispatch($event, ReloadBrowserEvent::NAME);
        $commands = $event->getCommands();
        $response = new JsonResponse(array(
            'commands' => $commands,
        ));
        return $response->send();
    }
}
