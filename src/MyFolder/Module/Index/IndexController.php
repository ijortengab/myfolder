<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Application;
use IjorTengab\MyFolder\Core\JsonResponse;
use IjorTengab\MyFolder\Core\ConfigHelper;
use IjorTengab\MyFolder\Core\TwigFile;
use IjorTengab\MyFolder\Core\Response;
use IjorTengab\MyFolder\Core\BinaryFileResponse;
use IjorTengab\MyFolder\Core\HtmlElementEvent;

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
        $config = ConfigHelper::load();
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
            // @todo: $_POST['directory'] harus diakhiri dengan slash.
            $directory = $http_request->request->get('directory');
            // Bersihkan dari double slash.
            $directory_sanitized = preg_replace('/\/+/','/',$directory);
            // Sanitasi /blog/././ menjadi /blog/
            do {
                $directory_sanitized = str_replace('/./','/',$directory_sanitized);
            }
            while (strstr($directory_sanitized, '/./') !== false);
            // User boleh mengetik double dot: `..` pada input search,
            // namun perlu sanitasi, atau bug yakni user bisa scan dir parent
            // sampai ke direktori root `/`
            $directory_sanitized_array = explode('/',trim($directory_sanitized, '/'));
            $directory_resolved = array();
            while ($each = array_shift($directory_sanitized_array)) {
                if ($each == '..') {
                    array_pop($directory_resolved);
                }
                else {
                    $directory_resolved[] = $each;
                }
            }
            $directory = empty($directory_resolved) ? '/' : '/' . implode('/', $directory_resolved) . '/';
            $current_directory = $root.$directory;

            if (!is_dir($current_directory)) {
                $response = new Response('Not Found.');
                $response->setStatusCode(404);
                return $response->send();
            }
            $action = $http_request->request->get('action');

            // Persiapan cache.
            // @todo, cek security, bgaimana jika user lain menebak cache.
            $post = array(
                'root' => $root,
                'directory' => $directory,
                'action' => $action,
            );
            $home = $http_request->server->get('HOME');
            if(null == $home) {
                $home = $http_request->server->get('DOCUMENT_ROOT');
            }
            $cache_directory = $home.'/.cache/myfolder';
            if (!is_dir($cache_directory)) {
                mkdir($cache_directory, 0775, true);
            }
            // buat event, agar module user bisa mengubah path dari cache
            // menjadi per user per instance.
            $cache_filename = md5(http_build_query($post));
            $cache_path = $cache_directory.'/'.$cache_filename.'.json';
            if (file_exists($cache_path)) {
                $filemtime = filemtime($cache_path);
                // Untuk TTL nya 24 jam.
                // $ttl = $filemtime + (24 * 60 * 60);
                // Untuk TTL nya 5 menit.
                $ttl = $filemtime + (5 * 60);
                // @todo user bisa ngedit.
                if (time() < $ttl) {
                    # @todo, how to set expire? event if user edit or add file.
                    $response = new BinaryFileResponse(new \SplFileInfo($cache_path));
                    return $response->send();
                }
                else {
                    // hapus file cache.
                }
            }

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

                    // Set cache.
                    // @todo: what's next?
                    file_put_contents($cache_path, json_encode($list_directory));

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

                    // Set cache.
                    // @todo: what's next?
                    file_put_contents($cache_path, json_encode($ls_la));

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
        );
        // Pada kasus terdapat `___pseudo`, maka hapus.
        if (strpos($settings['pathInfo'], '/___pseudo/') !== false) {
            $settings['pathInfo'] = preg_replace('/___pseudo.*/','',$settings['pathInfo']);
        }
        $event = HtmlElementEvent::load();
        $dispatcher->dispatch($event, HtmlElementEvent::NAME);
        $core_js = $event->getResources('core/js/*');
        $core_css = $event->getResources('core/css/*');
        $core_logo = $event->getResources('core/logo/myfolder/svg');
        $core_favicon = $event->getResources('core/favicon/myfolder/svg');
        $js = $event->getResources('index/js/*');
        $css = $event->getResources('index/css/*');
        $nav_items = $event->getTemplates('index/navbar/item/*');
        $content = TwigFile::process(new Template\Index, array(
            'js' => array_merge($core_js, $js),
            'css' => array_merge($core_css, $css),
            'logo' => array_shift($core_logo),
            'favicon' => array_shift($core_favicon),
            'list' => $nav_items,
            'settings' => array(
                'global' => json_encode($settings),
                'basePath' => $settings['basePath'],
            ),
        ));
        $response = new Response($content);
        return $response->send();
    }
}
