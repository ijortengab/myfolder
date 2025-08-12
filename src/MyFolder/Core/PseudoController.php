<?php

namespace IjorTengab\MyFolder\Core;

class PseudoController extends Controller
{
    public static function getAssetFile($module, $basename)
    {
        $pathinfo = pathinfo($basename);
        $filename = $pathinfo['filename'];
        $extension = $pathinfo['extension'];
        $part_module = str_replace(' ', '', ucwords(str_replace(array('-','_'), ' ', $module)));
        $part_file = str_replace(' ', '', ucwords(str_replace(array('-','_'), ' ', $filename)));
        $class = 'IjorTengab\\MyFolder\\Module\\'.$part_module.'\\Asset\\'.$part_file;
        if (!empty($extension)) {
            $part_extension = str_replace(' ', '', ucwords(str_replace(array('-','_'), ' ', $extension)));
            $class .= $part_extension;
        }
        if (class_exists($class)) {
            // $basename tidak harus exists.
            // dan karena tidak exist, jadi perlu kita kasih content via method
            // ::setContent(). Basename diperlukan untuk mengecek extension.
            $response = new BinaryFileResponse(new \SplFileInfo($basename));
            $response->setContent((string) new $class);
            return $response->send();
        }
        else {
            $response = new Response('Not Found.');
            $response->setStatusCode(404);
            return $response->send();
        }
    }
    public static function getRootFile()
    {
        $args = func_get_args();
        if ($args[0] == 'cdn') {
            // Kasih header agar di cache selamanya.
            array_shift($args);
            $fullpath = implode('/', $args);
            $dispatcher = Application::getEventDispatcher();
            $event = HtmlElementEvent::load();
            $dispatcher->dispatch($event, HtmlElementEvent::NAME);
            $remote_files = $event->getResources('*', 'https://'.$fullpath);
            if (count($remote_files)) {
                header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');
            }
            $fullpath = '/cdn/'.$fullpath;
        }
        else {
            $fullpath = '/'.implode('/', $args);
        }
        $target_directory = Application::$cwd;
        $fullpath = $target_directory.$fullpath;
        if (file_exists($fullpath)) {
            $response = new BinaryFileResponse(new \SplFileInfo($fullpath));
            return $response->send();
        }
        $response = new Response('Not Found.');
        $response->setStatusCode(404);
        return $response->send();
    }
}
