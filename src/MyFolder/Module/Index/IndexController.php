<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\Config;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Response;

class IndexController
{
    public static function route()
    {
        $http_request = Application::getHttpRequest();
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $is_ajax = !(null === $http_request->query->get('is_ajax'));
        $has_query_part = !(null === $http_request->query->get('part'));
        switch ($method) {
            case 'post':
                self::routePost();
                break;
            case 'get':
                if (!$is_ajax) {
                    self::routeGet();
                }
                elseif ($has_query_part) {
                    // self::routeGetAjaxPart();
                }
                else {
                    // self::routeGetAjax();
                }
                break;
        }
    }
    protected static function routePost()
    {
        $config = Config::load();
        $root = $config->root->value();
        null !== $root or $root = Application::$cwd;

        // @todo.
        // jika user adalah sysadmin, maka boleh menggunakan argument root_request.
        // selebihnya kita ignore.
        $http_request = Application::getHttpRequest();
        $root_request = $http_request->request->get('root');
        if (!empty($root_request)) {
            $root = $root_request;
        }
        if ($http_request->request->has('action')) {
            // @todo: Jika tidak ada $_POST['directory'], maka throw error.
            $current_directory = $root.$http_request->request->get('directory');
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
                    $old_pwd = Application::$cwd;
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
    protected static function routeGet()
    {
        $dispatcher = Application::getEventDispatcher();
        $event = IndexInvokeCommandEvent::load();
        $dispatcher->dispatch($event, IndexInvokeCommandEvent::NAME);
        list($base_path, $path_info, $rewrite_url) = Application::extractUrlInfo();

        $settings = array(
            'pathInfo' => $path_info,
            'basePath' => $base_path,
            'rewriteUrl' => $rewrite_url,
            'commands' => $event->getCommands(),
            'commandsExecuted' => [],
        );
        // Pada kasus terdapat `___pseudo`, maka hapus.
        if (strpos($settings['pathInfo'], '/___pseudo/') !== false) {
            $settings['pathInfo'] = preg_replace('/___pseudo.*/','',$settings['pathInfo']);
        }

        $event = new IndexInvokeHtmlElementEvent();
        $dispatcher->dispatch($event, IndexInvokeHtmlElementEvent::NAME);
        $html_element = $event->dump();
        $event = new IndexHtmlElementPreRenderEvent();
        $event->restore($html_element);
        $dispatcher->dispatch($event, IndexHtmlElementPreRenderEvent::NAME);
        $html_element = $event->dump();
        $js = $html_element['js'];
        $css = $html_element['css'];
        $rendered_list = array();
        foreach ($html_element['list'] as $each) {
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
