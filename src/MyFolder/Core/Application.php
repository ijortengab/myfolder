<?php

namespace IjorTengab\MyFolder\Core;

class Application
{
    const SESSION_NAME = 'IjorTengabWasHere';

    public static $cwd;
    public static $script_filename;
    protected static $user_session;
    protected static $http_request;
    protected static $event_dispatcher;
    protected $register = array();

    public static function extractUrlInfo()
    {
        if (null === self::$http_request) {
            self::$http_request = new Request;
        }
        $http_request = self::$http_request;
        $path_info = $http_request->getPathInfo();
        $base_path = $http_request->getBasePath();
        $rewrite_url = true;

        // Beri dukungan terhadap PHP built-in web server.
        //
        // Contoh 1:
        // ```
        // php -S 127.0.0.1:9090
        // ```
        // dengan request sbb:
        // ```
        // curl http://127.0.0.1:9090/index.php
        // curl http://127.0.0.1:9090/subfolder/index.php
        // ```
        // Nilai variable `$base_path` adalah '' atau
        // '/subfolder'. Kita perlu mengubahnya menjadi '/index.php' atau
        // '/subfolder/index.php' sehingga PHP built-in web server dapat
        // menerima request sbb:
        // /index.php/directory/subdirectory/file.ext
        // atau
        // /subfolder/index.php/directory/subdirectory/file.ext
        //
        // Contoh 2:
        // ```
        // php -S 127.0.0.1:9090 /path/to/index.php
        // ```
        // dengan request sbb:
        // ```
        // curl http://127.0.0.1:9090/favicon.ico
        // ```
        // menghasilkan nilai $_SERVER['SCRIPT_FILENAME']
        // adalah '/home/ijortengab/repositories/ijortengab/myfolder/favicon.ico'.
        // Kita perlu mengubahnya kembali menjadi
        // '/home/ijortengab/repositories/ahmadkemal/myfolder/index.php'
        // Solusi untuk hal ini adalah mengecek dengan nilai Application::$script_filename
        $filename = basename($http_request->server->get('SCRIPT_FILENAME'));
        if (realpath($http_request->server->get('SCRIPT_FILENAME')) !== Application::$script_filename) {
            // Koreksi.
            $filename = basename(Application::$script_filename);
            // Nilai dari $path_info seharusnya bukan '/', melainkan
            // '/favicon.ico'.
            if ($path_info == '/') {
                $path_info .= basename($http_request->server->get('SCRIPT_FILENAME'));
            }
        }
        $base_url = $http_request->getBaseUrl();
        if (str_ends_with($base_url, $filename)) {
            $base_path = $base_url;
            $rewrite_url = false;
        }
        return array($base_path, $path_info, $rewrite_url);
    }
    public static function currentUser()
    {
        if (null === self::$user_session) {
            self::$user_session = new UserSession;
        }
        return self::$user_session;
    }
    public static function getHttpRequest()
    {
        if (null === self::$http_request) {
            self::$http_request = new Request;
        }
        return self::$http_request;
    }
    public static function getEventDispatcher()
    {
        if (null === self::$event_dispatcher) {
            self::$event_dispatcher = new EventDispatcher;
        }
        return self::$event_dispatcher;
    }

    public function __construct($directory, $file)
    {
        self::$cwd = $directory;
        self::$script_filename = realpath($file);
    }

    public function post($pathinfo, $callback)
    {
        // Allow module to override pathinfo and callback.
        $dispatcher = self::getEventDispatcher();
        $event = new RouteRegisterEvent('post', $pathinfo, $callback);
        $dispatcher->dispatch($event, RouteRegisterEvent::NAME);
        $pathinfo = $event->getPathInfo();
        $callback = $event->getCallback();
        $this->register['post'][$pathinfo] = $callback;
    }
    public function get($pathinfo, $callback)
    {
        // @todo, jika ada yang kayak gini:
        // $app->get('/target_directory/{sch|eme}', 'IjorTengab\MyFolder\Controller::pseudoHandle');
        // maka sejak awal sudah dikasih throw exception aja.
        // karena gak valid sebagai placeholder.

        // Allow module to override pathinfo and callback.
        $dispatcher = self::getEventDispatcher();
        $event = new RouteRegisterEvent('get', $pathinfo, $callback);
        $dispatcher->dispatch($event, RouteRegisterEvent::NAME);
        $pathinfo = $event->getPathInfo();
        $callback = $event->getCallback();
        $this->register['get'][$pathinfo] = $callback;
    }
    public function run()
    {
        try {
            $this->handle();
            $this->scanModule();
            $this->route();
        }
        catch (\Exception $e) {
            // @todo, gunakan toasts.
            $commands = array();
            $commands[] = array(
                'command' => 'modal',
                'options' => array(
                    'name' => 'exception',
                    'bootstrapOptions' => array(
                        'backdrop' => 'static',
                        'keyboard' => true
                    ),
                    'layout' => array(
                        'title' => 'Exception',
                        'body' => $e->getMessage(),
                    ),
                ),
            );
            $response = new JsonResponse(array(
                'commands' => $commands,
            ));
            return $response->send();
        }
    }
    protected function handle()
    {
        // Register event.
        $dispatcher = Application::getEventDispatcher();
        $dispatcher->addSubscriber(new FilePreRenderSubscriber());

        // Register route.
        $this->get('/', 'IjorTengab\MyFolder\Core\Controller::get');
        $this->post('/', 'IjorTengab\MyFolder\Core\Controller::post');

        // Route dibawah ini secara real, maka diawali oleh `/___pseudo`.
        $this->get('/assets/{module}/{file}', 'IjorTengab\MyFolder\Core\PseudoController::getAssetFile');
        $this->get('/root/{a}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
    }
    protected function scanModule()
    {
        $config = ConfigHelper::load();
        $modules = $config->module->value();
        if (null === $modules) {
            return;
        }
        foreach ($modules as $module) {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            if (!class_exists("\\IjorTengab\\MyFolder\\Module\\$class\\$class")) {
                throw new ModuleException("Module $module is not exists.");
            }
            $class_implements = class_implements("\\IjorTengab\\MyFolder\\Module\\$class\\$class");
            if (in_array('IjorTengab\MyFolder\Core\ModuleInterface', $class_implements)) {
                $callback = array(
                    "\\IjorTengab\\MyFolder\\Module\\$class\\$class",
                    'handle'
                );
                $args = array($this);
                call_user_func_array($callback, $args);
            }
        }
    }
    protected function route()
    {
        $http_request = self::getHttpRequest();
        $this->loadSession($http_request);
        $method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $path_info = $http_request->getPathInfo();
        if (!isset($this->register[$method])) {
            throw new RouteException('Request Method not found.');
        }
        $register = $this->register[$method];
        if (str_starts_with($path_info, '/___pseudo')) {
            // Hapus prefix '/___pseudo' pada path.
            $path_info = substr($path_info, strlen('/___pseudo'));
            do {
                // Filter berdasarkan fix string.
                $register_filtered = array_filter($register, function ($key) use ($path_info) {
                    return $path_info === $key;
                }, ARRAY_FILTER_USE_KEY);
                if ($register_filtered) {
                    $key = key($register_filtered);
                    $callback = $register_filtered[$key];
                    $args = array();
                    break;
                }
                // Filter berdasarkan regex.
                $register_filtered = array_filter($register, function ($key) use ($path_info) {
                    $pattern_parts = preg_split('/\{[^}]+\}/', $key);
                    $pattern_quoted_parts = array_map(function ($value) {
                        return preg_quote($value,'/');
                    }, $pattern_parts);
                    $pattern_quoted = implode('[^\/]+', $pattern_quoted_parts);
                    return preg_match('/^'.$pattern_quoted.'$/', $path_info);
                }, ARRAY_FILTER_USE_KEY);
                if ($register_filtered) {
                    $key = key($register_filtered);
                    $save = array();
                    $pattern = preg_replace_callback('/\{[^}]+\}/', function ($matches) use (&$save) {
                        $value = array_shift($matches);
                        $name = trim($value, '{}');
                        $save[] = $name;
                        return '(?P<'.$name.'>.+)';
                    }, $key);
                    $pattern_parts = preg_split('/\(\?P\<[a-z]+\>\.\+\)/', $pattern);
                    $pattern_quoted_parts = array_map(function ($value, $name) {
                        if (!empty($name)) {
                            $name = '(?P<'.$name.'>.+)';
                        }
                        return preg_quote($value,'/').$name;
                    }, $pattern_parts, $save);
                    $pattern_quoted = implode('', $pattern_quoted_parts);
                    preg_match('/^'.$pattern_quoted.'/', $path_info, $matches);
                    $args = array_filter($matches, function ($key) {
                        return !is_numeric($key);
                    }, ARRAY_FILTER_USE_KEY);
                    $callback = $register_filtered[$key];
                    break;
                }
            }
            while (false);
            if (empty($callback)) {
                $callback = $register['/'];
                $args = array();
            }
        }
        else {
            $callback = $register['/'];
            $args = array();
        }

        if (!empty($args)) {
            $args = array_values($args);
        }
        if (!is_callable($callback)) {
            throw new RouteException('Callback is not exists: '.$callback.'.');
        }
        call_user_func_array($callback, $args);
    }
    protected function loadSession(Request $http_request)
    {
        $session_name = $http_request->cookies->get(Application::SESSION_NAME);
        if ($session_name !== null) {
            $session = Session::load();
            $session->start();
        }
    }
}
