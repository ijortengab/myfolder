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
        if (class_exists($class)) {
            switch ($extension) {
                case 'js':
                    header('Content-Type: text/javascript; charset=utf-8');
                    break;
                case 'svg':
                    header('Content-Type: image/svg+xml');
                    break;
            }
            $response = new Response(new $class);
            return $response->send();
        }
        else {
            $response = new Response('Not Found.');
            $response->setStatusCode(404);
            return $response->send();
        }
    }

    public static function getRootFile($a, $b = null, $c = null, $d = null, $e = null, $f = null, $g = null)
    {
        $target_directory = getcwd();
        $fullpath = $target_directory.'/'.$a;
        $b === null or $fullpath .= '/'.$b;
        $c === null or $fullpath .= '/'.$c;
        $d === null or $fullpath .= '/'.$d;
        $e === null or $fullpath .= '/'.$e;
        $f === null or $fullpath .= '/'.$f;
        $g === null or $fullpath .= '/'.$g;
        if (file_exists($fullpath)) {
            // @todo, lakukan ini di module.
            $basename = basename($fullpath);
            switch ($basename) {
                case 'jquery.min.js':
                case 'jquery.once.min.js':
                case 'jquery.once.min.js.map':
                case 'popper.min.js':
                case 'popper.min.js.map':
                case 'bootstrap.min.js':
                case 'bootstrap.min.js.map':
                case 'bootstrap.min.css':
                case 'bootstrap.min.css.map':
                case 'bootstrap-icons.css':
                case 'bootstrap-icons.woff2':
                    header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');
                    break;
            }
            $response = new BinaryFileResponse($fullpath);
            return $response->send();
        }
        $response = new Response('Not Found.');
        $response->setStatusCode(404);
        return $response->send();
    }

    public static function getTargetDirectoryFile($scheme)
    {
        $editor = new ConfigEditor;
        $editor->setClassName('Application', 'IjorTengab\MyFolder\Core');
        $config = new Config;
        $config->parse($editor->get());
        $target_directory = $config->targetDirectory->public->value();
        if (empty($target_directory)) {
            $target_directory = getcwd();
        }

        switch ($scheme) {
            case 'public':
                $request = Application::getHttpRequest();
                $path = $request->query->get('path');
                $fullpath = $target_directory.$path;
                if (is_file($fullpath)) {
                    $response = new BinaryFileResponse($fullpath);
                    return $response->send();
                }
                break;
            case '':
                // Do something.
                break;
            default:
                $response = new Response('Not Found.');
                $response->setStatusCode(404);
                return $response->send();
                break;
        }
    }
}
