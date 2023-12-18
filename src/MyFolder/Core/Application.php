<?php

namespace IjorTengab\MyFolder\Core;

/**
 * .module[] index
 * .module[] user
 * .module[] offline_mode
 * .targetDirectory.public /var/www
 */
class Application
{
    const SESSION_NAME = 'IjorTengabWasHere';
    protected static $user_session;
    protected static $http_request;
    protected static $event_dispatcher;
    protected $register = array();
    public function post($pathinfo, $callback)
    {
        $this->register['post'][$pathinfo] = $callback;
    }
    public function get($pathinfo, $callback)
    {
        // @todo, jika ada yang kayak gini:
        // $app->get('/___pseudo/target_directory/{sch|eme}', 'IjorTengab\MyFolder\Controller::pseudoHandle');
        // maka sejak awal sudah dikasih throw exception aja.
        // karena gak valid sebagai placeholder.
        $this->register['get'][$pathinfo] = $callback;
    }
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
        // Contoh: sebelumnya $base_path = /subfolder
        // menjadi $base_path = /subfolder/index.php
        // sehingga PHP built-in web server dapat menerima request sbb:
        // /subfolder/index.php/directory/subdirectory/file.ext
        $filename = basename($http_request->server->get('SCRIPT_FILENAME'));
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
    public function run()
    {
        $this->handle();
        $this->scanModule();
        $this->route();
    }
    protected function handle()
    {
        // Register route.
        $this->get('/', 'IjorTengab\MyFolder\Core\Controller::index');

        // Route dibawah ini secara real, maka diawali oleh `/___pseudo`.
        $this->get('/assets/{module}/{file}', 'IjorTengab\MyFolder\Core\PseudoController::getAssetFile');
        $this->get('/root/{a}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}', 'IjorTengab\MyFolder\Core\PseudoController::getRootFile');
        $this->get('/target_directory/{scheme}', 'IjorTengab\MyFolder\Core\PseudoController::getTargetDirectoryFile');
        $this->get('/dashboard', 'IjorTengab\MyFolder\Core\DashboardController::index');
    }

    protected function scanModule()
    {
        $editor = new ConfigEditor(self::class);
        $config = new Config;
        $config->parse($editor->get());
        $modules = $config->module->list();
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
            throw new Exception('Request Method not found.');
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
        // $debugname = 'callback'; $debugvariable = '|||wakwaw|||'; if (array_key_exists($debugname, get_defined_vars())) { $debugvariable = $$debugname; } elseif (isset($this) && property_exists($this, $debugname)){ $debugvariable = $this->{$debugname}; $debugname = '$this->' . $debugname; } if ($debugvariable !== '|||wakwaw|||') {        echo "\r\n<pre>" . basename(__FILE__ ). ":" . __LINE__ . " (Time: " . date('c') . ", Direktori: " . dirname(__FILE__) . ")\r\n". 'var_dump(' . $debugname . '): '; var_dump($debugvariable); echo "</pre>\r\n"; }
        // die('op');

        if (!empty($args)) {
            $args = array_values($args);
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
