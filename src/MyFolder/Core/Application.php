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
        $this->register['post'][$pathinfo] = $callback;
    }
    public function get($pathinfo, $callback)
    {
        // @todo, jika ada yang kayak gini:
        // $app->get('/target_directory/{sch|eme}', 'IjorTengab\MyFolder\Controller::pseudoHandle');
        // maka sejak awal sudah dikasih throw exception aja.
        // karena gak valid sebagai placeholder.

        $this->register['get'][$pathinfo] = $callback;
    }
    public function run()
    {
        spl_autoload_register(array($this, 'autoload'));
        try {
            $this->scanModule();
            $this->handle();
            $this->route();
        }
        catch (AccessException $e) {
            $response = $e->getResponse();
            return $response->send();
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage());
            $response->setStatusCode(500);
            return $response->send();
        }
    }
    public function autoload($class_name)
    {
        static $valid;
        $config_replace_php = self::$cwd.'/'.Template\ConfigReplace::BASENAME;
        if (!file_exists($config_replace_php)) {
            return;
        }
        if ($valid === null) {
            if (ConfigLoader::isConfigReplaceValid($config_replace_php)) {
                $valid = true;
            }
            else {
                $valid = false;
            }
        }
        if ($valid === false) {
            return;
        }

        switch ($class_name) {
            // Frequent.
            case 'IjorTengab\MyFolder\Core\ConfigReplace':
            case 'IjorTengab\MyFolder\Module\Index\ConfigReplace':
            case 'IjorTengab\MyFolder\Module\User\ConfigReplace':
                require_once($config_replace_php);
                break;

            default:
                if (str_starts_with($class_name, 'IjorTengab\MyFolder\Module') && str_ends_with($class_name, 'ConfigReplace')) {
                    require_once($config_replace_php);
                }
                break;
        }
    }
    protected function handle()
    {
        // Register event.
        $dispatcher = self::getEventDispatcher();
        $dispatcher->addSubscriber(new FilePreRenderSubscriber());
        $dispatcher->addSubscriber(new HtmlElementSubscriber());

        // Register route.
        $this->get('/', 'IjorTengab\MyFolder\Core\Controller::route');
        $this->post('/', 'IjorTengab\MyFolder\Core\Controller::route');

        $pseudo_getasset = 'IjorTengab\MyFolder\Core\PseudoController::getAssetFile';
        $pseudo_getroot = 'IjorTengab\MyFolder\Core\PseudoController::getRootFile';
        $this->get('/assets/{module}/{file}', $pseudo_getasset);
        $this->get('/root/{a}', $pseudo_getroot);
        $this->get('/root/{a}/{b}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}/{n}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}/{n}/{o}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}/{n}/{o}/{p}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}/{n}/{o}/{p}/{q}', $pseudo_getroot);
        $this->get('/root/{a}/{b}/{c}/{d}/{e}/{f}/{g}/{h}/{i}/{j}/{k}/{l}/{m}/{n}/{o}/{p}/{q}/{r}', $pseudo_getroot);
    }
    protected function scanModule()
    {
        $config = ConfigLoader::core();
        $modules = $config->module->value();
        if (null === $modules) {
            return;
        }
        $modules = array_filter($modules, function ($a) {
            // String value = 1.
            return $a === '1';
        });
        $modules = array_keys($modules);
        foreach ($modules as $module) {
            $class = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
            if (!class_exists("IjorTengab\\MyFolder\\Module\\$class\\$class")) {
                throw new ModuleException("Module $module is not exists.");
            }
            $class_implements = class_implements("\\IjorTengab\\MyFolder\\Module\\$class\\$class");
            if (in_array('IjorTengab\MyFolder\Core\ModuleInterface', $class_implements)) {
                $callback = array(
                    "IjorTengab\\MyFolder\\Module\\$class\\$class",
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
        $pathinfo = $http_request->getPathInfo();
        if (!isset($this->register[$method])) {
            throw new RouteException('Request Method not found.');
        }

        // Allow module to override pathinfo.
        $dispatcher = self::getEventDispatcher();
        $event = new RouteRegisterEvent($method, $pathinfo);
        $dispatcher->dispatch($event, RouteRegisterEvent::NAME);
        $pathinfo = $event->getPathInfo();
        $register = $this->register[$method];

        do {
            // Filter berdasarkan fix string.
            $register_filtered = array_filter($register, function ($key) use ($pathinfo) {
                return $pathinfo === $key;
            }, ARRAY_FILTER_USE_KEY);
            if ($register_filtered) {
                $key = key($register_filtered);
                $callback = $register_filtered[$key];
                $args = array();
                break;
            }
            // Filter berdasarkan regex.
            $register_filtered = array_filter($register, function ($key) use ($pathinfo) {
                $pattern_parts = preg_split('/\{[^}]+\}/', $key);
                $pattern_quoted_parts = array_map(function ($value) {
                    return preg_quote($value,'/');
                }, $pattern_parts);
                $pattern_quoted = implode('[^\/]+', $pattern_quoted_parts);
                return preg_match('/^'.$pattern_quoted.'$/', $pathinfo);
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
                preg_match('/^'.$pattern_quoted.'/', $pathinfo, $matches);
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
