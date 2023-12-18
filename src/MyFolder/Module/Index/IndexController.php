<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigEditor;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Response;

class IndexController
{
    public static function index()
    {
        $http_request = Application::getHttpRequest();
        $http_request_method = strtolower($http_request->server->get('REQUEST_METHOD'));
        switch ($http_request_method) {
            case 'post':
                self::routeIndexPost();
                break;
            case 'get':
                self::routeIndexGet();
                break;
        }
    }

    protected static function routeIndexPost()
    {
        $editor = new ConfigEditor;
        $editor->setClassName('Application', 'IjorTengab\MyFolder\Core');
        $config = new Config;
        $config->parse($editor->get());
        $target_directory = $config->targetDirectory->public->value();
        if (empty($target_directory)) {
            $target_directory = getcwd();
        }
        $http_request = Application::getHttpRequest();
        if ($http_request->request->has('action')) {
            // @todo: Jika tidak ada $_POST['directory'], maka throw error.
            $current_directory = $target_directory.$http_request->request->get('directory');
            if (!is_dir($current_directory)) {
                $response = new JsonResponse();
                $response->setData(array());
                return $response->send();
            }
            $action = $http_request->request->get('action');
            $list_directory = scandir($current_directory);
            $list_directory = array_diff($list_directory, array('.','..'));
            switch ($action) {
                case 'ls':
                    // Direktori diatas
                    $old_pwd = getcwd();
                    chdir($current_directory);
                    $dotdir_only = glob('.*', GLOB_ONLYDIR);
                    $dotdir_only = array_diff($dotdir_only, array('.','..'));
                    $dir_only = glob('*', GLOB_ONLYDIR);
                    $dir_only = array_merge($dotdir_only, $dir_only);
                    chdir($old_pwd);
                    $file_only = array_diff($list_directory, $dir_only);
                    // sort($dir_only);
                    // natcasesort($dir_only);
                    $list_directory = array_merge($dir_only, $file_only);
                    // Sorting Folder like files.
                    // $list_directory = array_values($list_directory);
                    $response = new JsonResponse();
                    $response->setData($list_directory);
                    return $response->send();
                case 'ls -la':
                    // Do something.
                    $ls_la = array();
                    foreach ($list_directory as $each) {
                        $_ls_la = array(
                            'name' => $each,
                            'mtime' => '',
                            'size' => '',
                            'type' => '.', // dot means directory
                        );
                        if (is_file($current_directory.$each)) {
                            $file = $current_directory.$each;
                            $_ls_la['mtime'] = filemtime($file);
                            $_ls_la['size'] = filesize($file);
                            $_ls_la['type'] = pathinfo($file, PATHINFO_EXTENSION);
                        }
                        $ls_la[] = $_ls_la;
                    }
                    $response = new JsonResponse();
                    $response->setData($ls_la);
                    return $response->send();
                default:
                    // Do something.
                    break;
            }
        }
    }

    protected static function routeIndexGet()
    {
        $dispatcher = Application::getEventDispatcher();
        $event = IndexEvent::load();
        $dispatcher->dispatch($event, IndexEvent::NAME);
        list($base_path, $path_info, $rewrite_url) = Application::extractUrlInfo();

        $settings = array(
            'pathInfo' => $path_info,
            'basePath' => $base_path,
            'rewriteUrl' => $rewrite_url,
            'commands' => $event->getCommands(),
        );
        // Pada kasus terdapat `___pseudo`, maka hapus.
        if (strpos($settings['pathInfo'], '/___pseudo/') !== false) {
            $settings['pathInfo'] = preg_replace('/___pseudo.*/','',$settings['pathInfo']);
        }

        $event = new HtmlElementEvent();
        $dispatcher->dispatch($event, HtmlElementEvent::NAME);
        $js = $event->getAllJavascript();
        $css = $event->getAllCascadingStyleSheets();
        $rendered_list = array();
        foreach ($event->getAllList() as $each) {
            list($template, $array) = $each;
            $rendered_list[] = TwigFile::process($template, $array);
        }
        $content = TwigFile::process(new Template\Index, array(
            'js' => $js,
            'css' => $css,
            'list' => $rendered_list,
            'settings' => array(
                'global' => json_encode($settings),
                'basePath' => $settings['basePath'],
            ),
        ));

        $response = new Response($content);
        return $response->send();
    }
}
