<?php
namespace IjorTengab\MyFolder;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Credit: https://github.com/symfony/polyfill-php80/blob/1.x/Php80.php
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return false !== strpos($haystack, $needle);
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        $needleLength = \strlen($needle);
        return 0 === substr_compare($haystack, $needle, -$needleLength);
    }
}
interface EventSubscriberInterface {
    public static function getSubscribedEvents();
}
class BootEvent {
    const NAME = 'boot.event';
    public $sysadmin;
}
class ConfigFile {
    public static $target_directory = __DIR__;
    public static function load()
    {
        return <<<'EOF'
password=
EOF;
    }
    protected $doc;
    protected $filename;
    protected $namespace;
    protected $class;
    protected $method;
    protected $has_final_keyword;
    protected $what_doc;
    protected $identifier;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    public function hasFinalKeyword($bool)
    {
        $this->has_final_keyword = $bool;
    }
    public function nowDocIdentifier($identifier)
    {
        $this->what_doc = 'now';
        $this->identifier = $identifier;
    }
    public function hereDocIdentifier($identifier)
    {
        $this->what_doc = 'here';
        $this->identifier = $identifier;
    }
    public function setClassName($class, $namespace = null)
    {
        $this->namespace = $namespace;
        $this->class = $class;
    }
    public function setStaticMethod($method)
    {
        $this->method = $method;
    }
    public function get()
    {
        if (null === $this->doc) {
            $this->populateDoc();
        }
        return $this->doc;
    }
    protected function populateDoc()
    {
        $this->doc = '';
        $reading = fopen($this->filename,'r');
        $find = (null === $this->namespace) ? 'class_opening' : 'namespace';
        while (!feof($reading)) {
            $line = fgets($reading);
            switch ($find) {
                case 'namespace':
                    $string = 'namespace '.$this->namespace.';';
                    if (str_starts_with($line, $string)) {
                        $find = 'class_opening';
                    }
                    break;
                case 'class_opening':
                    $final = $this->has_final_keyword ? 'final ':'';
                    if (str_starts_with($line, $final.'class '.$this->class.' {')) {
                        $find = 'method_opening';
                    }
                    break;
                case 'method_opening':
                    if (str_contains($line, 'public static function '.$this->method)) {
                        $find = 'heredoc_opening';
                    }
                    break;
                case 'heredoc_opening':
                    switch ($this->what_doc) {
                        case 'now':
                            $string = "<<<'".$this->identifier."'";
                            break;
                        case 'here':
                            $string = '<<<'.$this->identifier;
                            break;
                    }
                    if (str_contains($line, $string)) {
                        $find = 'heredoc_closing';
                    }
                    break;
                case 'heredoc_closing':
                    $string = $this->identifier.';';
                    if (str_starts_with($line, $string)) {
                        $find = 'method_closing';
                    }
                    else {
                        $this->doc .= $line;
                    }
                    break;
                case 'method_closing':
                    if (str_contains($line, '}')) {
                        $find = 'class_closing';
                    }
                    break;
                case 'class_closing':
                    if (str_starts_with($line, '}')) {
                        $find = '';
                    }
                    break;
                default:
                    break;
            }
        }
        fclose($reading);
    }
    public function set($data)
    {
        if (!is_writable($this->filename)) {
            throw new WriteException('File is not writable.');
        }
        $reading = fopen($this->filename,'r');
        $temp_file = tempnam(sys_get_temp_dir(), 'MyFolder');
        $writing = fopen($temp_file,'w');
        $find = (null === $this->namespace) ? 'class_opening' : 'namespace';
        while (!feof($reading)) {
            $line = fgets($reading);
            switch ($find) {
                case 'namespace':
                    fputs($writing, $line);
                    $string = 'namespace '.$this->namespace.';';
                    if (str_starts_with($line, $string)) {
                        $find = 'class_opening';
                    }
                    break;
                case 'class_opening':
                    fputs($writing, $line);
                    $final = $this->has_final_keyword ? 'final ':'';
                    if (str_starts_with($line, $final.'class '.$this->class.' {')) {
                        $find = 'method_opening';
                    }
                    break;
                case 'method_opening':
                    fputs($writing, $line);
                    if (str_contains($line, 'public static function '.$this->method)) {
                        $find = 'heredoc_opening';
                    }
                    break;
                case 'heredoc_opening':
                    fputs($writing, $line);
                    switch ($this->what_doc) {
                        case 'now':
                            $string = "<<<'".$this->identifier."'";
                            break;
                        case 'here':
                            $string = '<<<'.$this->identifier;
                            break;
                    }
                    if (str_contains($line, $string)) {
                        $find = 'heredoc_closing';
                    }
                    break;
                case 'heredoc_closing':
                    if (str_starts_with($line, $this->identifier.';')) {
                        $find = 'class_closing';
                        fputs($writing, $data.PHP_EOL);
                        fputs($writing, $line);
                    }
                    break;
                case 'class_closing':
                    fputs($writing, $line);
                    if (str_starts_with($line, '}')) {
                        $find = '';
                    }
                    break;
                default:
                    fputs($writing, $line);
                    break;
            }
        }
        fclose($reading);
        fclose($writing);
        $oldgroup = filegroup($this->filename);
        rename($temp_file, $this->filename);
        // Bring back the group, so we still editable this code.
        chmod($this->filename, 0664);
        chgrp($this->filename,$oldgroup);
    }
}
class Config {
    private $clear;
    protected $shortcut;
    protected $array_storage = array();
    protected $current_storage;
    protected $dump_key_storage = array();
    protected $dump_lines = array();
    protected $dump_is_indexed_array = false;
    protected $dump_is_indexed_array_sorted = false;
    /**
     * Port of php.net/str_ends_with.
     * Based on https://github.com/symfony/polyfill-php80/blob/1.x/Php80.php
     */
    public function __construct($shortcut = false)
    {
        $this->shortcut = $shortcut;
    }
    public function __set($a, $b)
    {
        if (null === $this->current_storage) {
            $this->current_storage = &$this->array_storage;
        }
        if ($this->shortcut) {
            if (str_ends_with($a, '__')) {
                $a = substr($a, 0, -2).'[]';
            }
            elseif (str_ends_with($a, '_')) {
                if (preg_match('/^(.*)_(\d+)_$/',$a, $m)) {
                    $a = $m[1];
                    $i = $m[2];
                    $replacement = '['.$i.']';
                    $replacement_length = strlen($replacement);
                    $a = substr($a, 0, -$replacement_length).$replacement;
                };
            }
        }
        if (str_ends_with($a, '[]')) {
            // Berarti append.
            $a = substr($a, 0, -2);
            $this->current_storage[$a][] = $b;
        }
        elseif (str_ends_with($a, ']')) {
            // Kemungkinan fill indexed array.
            if (preg_match('/^(.*)\[(\d+)\]$/',$a, $m)) {
                $a = $m[1];
                $i = $m[2];
                $this->current_storage[$a][$i] = $b;
            };
        }
        else {
            if (!array_key_exists($a, $this->current_storage)) {
                $this->current_storage[$a] = array();
            }
            $this->current_storage[$a] = $b;
        }
        $this->current_storage = &$this->clear;
    }
    public function __get($a)
    {
        if (null === $this->current_storage) {
            $this->current_storage = &$this->array_storage;
        }
        if ($this->shortcut) {
            if (str_ends_with($a, '_')) {
                if (preg_match('/^(.*)_(\d+)_$/',$a, $m)) {
                    $a = $m[1];
                    $i = $m[2];
                    $replacement = '['.$i.']';
                    $replacement_length = strlen($replacement);
                    $a = substr($a, 0, -$replacement_length).$replacement;
                };
            }
        }
        if (str_ends_with($a, ']')) {
            // Kemungkinan fill indexed array.
            if (preg_match('/^(.*)\[(\d+)\]$/',$a, $m)) {
                $a = $m[1];
                $i = $m[2];
                if (!array_key_exists($a, $this->current_storage)) {
                    $this->current_storage[$a] = array();
                }
                if (!array_key_exists($i, $this->current_storage[$a])) {
                    $this->current_storage[$a][$i] = array();
                }
                $this->current_storage = &$this->current_storage[$a][$i];
            };
        }
        else {
            if (!array_key_exists($a, $this->current_storage)) {
                $this->current_storage[$a] = array();
            }
            $this->current_storage = &$this->current_storage[$a];
        }
        return $this;
    }
    public function __toString()
    {
        $current = $this->current_storage;
        $this->current_storage = &$this->clear;
        if (is_string($current)) {
            return $current;
        }
        if (null === $current) {
            $array = $this->array_storage;
            $this->dumpArray($array);
            return implode(PHP_EOL, $this->dump_lines);
        }
        return '';
    }
    public function value()
    {
        return (string) $this;
    }
    public function parse($string)
    {
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '.')) {
                $line = substr($line, 1);
                $segment = explode(' ', $line);
                if (count($segment) == 2) {
                    $address = $segment[0];
                    $value = $segment[1];
                    $keys = explode('.', $address);
                    $last = array_pop($keys);
                    foreach ($keys as $key) {
                        $this->$key;
                    }
                    $this->$last = $value;
                }
            }
        }
    }
    protected function isIndexedArray($array)
    {
        $keys = array_keys($array);
        $filtered = array_filter($keys, 'is_numeric');
        $return = array_diff($keys, $filtered);
        if (empty($return)) {
            $i = 0;
            do {
                $aa = current($keys);
                $bb = $i;
                if (current($keys) === $i++) {
                    next($keys);
                    if (current($keys) === false) {
                        break;
                    }
                    $this->dump_is_indexed_array_sorted = true;
                    continue;
                }
                else{
                    $this->dump_is_indexed_array_sorted = false;
                    break;
                }
            }
            while (true);
            $this->dump_is_indexed_array = true;
        }
        else {
            $this->dump_is_indexed_array = false;
        }
        return $this->dump_is_indexed_array;
    }
    protected function dumpArray($array)
    {
        foreach ($array as $key => $value) {
            $this->dump_key_storage[] = $key;
            if (is_array($value)) {
                if ($this->isIndexedArray($value)) {
                    $last = array_pop($this->dump_key_storage);
                    $new_array = array();
                    $new_indexed_array_sorted = array();
                    foreach ($value as $key2 => $value2) {
                        if ($this->dump_is_indexed_array_sorted) {
                            $new_indexed_array_sorted[] = $value2;
                        }
                        else {
                            $new_array["$last".'['."$key2".']'] = $value2;
                        }
                    }
                    if ($new_array) {
                        $this->dumpArray($new_array);
                    }
                    if ($new_indexed_array_sorted) {
                        while ($value3 = array_shift($new_indexed_array_sorted)) {
                            $new_array = array();
                            $new_array["$last".'[]'] = $value3;
                            $this->dumpArray($new_array);
                        }
                    }
                }
                else {
                    $this->dumpArray($value);
                }
            }
            else {
                $this->dump_lines[] = '.'.implode('.', $this->dump_key_storage).' '.$value;
            }
            array_pop($this->dump_key_storage);
        }
    }
}
class WriteException extends \Exception {}
// Based on Symfony ParameterBag version 2.8.18.
class ParameterBag {
    protected $parameters;
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }
    public function set($key, $value)
    {
        $this->parameters[$key] = $value;
    }
    public function get($key, $default = null, $deep = false)
    {
        return $this->parameters[$key];
    }
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }
    public function remove($key)
    {
        unset($this->parameters[$key]);
    }
}
// Based on Symfony Request version 2.8.18.
class Request {
    public $server;
    public $request;
    public $query;
    protected $requestUri;
    protected $basePath;
    protected $pathInfo;
    protected $baseUrl;
    public function __construct()
    {
        $this->server = new ParameterBag($_SERVER);
        $this->request = new ParameterBag($_POST);
        $this->query = new ParameterBag($_GET);
    }
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }
        return $this->pathInfo;
    }
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }
        return $this->basePath;
    }
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }
        return $this->baseUrl;
    }
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }
    public function getPort()
    {
        if (!$host = $this->server->has('HTTP_HOST') ? $this->server->get('HTTP_HOST') : null) {
            return $this->server->get('SERVER_PORT');
        }
        if ($host[0] === '[') {
            $pos = strpos($host, ':', strrpos($host, ']'));
        } else {
            $pos = strrpos($host, ':');
        }
        if (false !== $pos) {
            return (int) substr($host, $pos + 1);
        }
        return 'https' === $this->getScheme() ? 443 : 80;
    }
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443)) {
            return $this->getHost();
        }
        return $this->getHost().':'.$port;
    }
    public function getRequestUri()
    {
        if (null === $this->requestUri) {
            $this->requestUri = $this->prepareRequestUri();
        }
        return $this->requestUri;
    }
    public function getSchemeAndHttpHost()
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }
    public function isSecure()
    {
        $https = $this->server->has('HTTPS') ? $this->server->get('HTTPS') : null;
        return !empty($https) && 'off' !== strtolower($https);
    }
    public function getHost()
    {
        if (!$host = $this->server->has('HTTP_HOST') ? $this->server->get('HTTP_HOST') : null) {
            if (!$host = $this->server->has('SERVER_NAME') ? $this->server->get('SERVER_NAME') : null) {
                $host = $this->server->has('SERVER_ADDR') ? $this->server->get('SERVER_ADDR') : null;
            }
        }
        // trim and remove port number from host
        // host is lowercase as per RFC 952/2181
        $host = strtolower(preg_replace('/:\d+$/', '', trim($host)));
        // as the host can come from the user (HTTP_HOST and depending on the configuration, SERVER_NAME too can come from the user)
        // check that it does not contain forbidden characters (see RFC 952 and RFC 2181)
        // use preg_replace() instead of preg_match() to prevent DoS attacks with long host names
        if ($host && '' !== preg_replace('/(?:^\[)?[a-zA-Z0-9-:\]_]+\.?/', '', $host)) {
            return '';
        }
        return $host;
    }
    protected function prepareRequestUri()
    {
        $requestUri = '';
        if ($this->server->has('HTTP_X_ORIGINAL_URL')) {
            // IIS with Microsoft Rewrite Module
            $requestUri = $this->server->get('HTTP_X_ORIGINAL_URL');
            $this->server->remove('HTTP_X_ORIGINAL_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('HTTP_X_REWRITE_URL')) {
            // IIS with ISAPI_Rewrite
            $requestUri = $this->server->get('HTTP_X_REWRITE_URL');
            $this->server->remove('HTTP_X_REWRITE_URL');
        } elseif (
            $this->server->has('IIS_WasUrlRewritten') &&
            $this->server->get('IIS_WasUrlRewritten') == '1' &&
            $this->server->has('UNENCODED_URL') &&
            $this->server->get('UNENCODED_URL') != ''
        ) {
            // IIS7 with URL Rewrite: make sure we get the unencoded URL (double slash problem)
            $requestUri = $this->server->get('UNENCODED_URL');
            $this->server->remove('UNENCODED_URL');
            $this->server->remove('IIS_WasUrlRewritten');
        } elseif ($this->server->has('REQUEST_URI')) {
            $requestUri = $this->server->get('REQUEST_URI');
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif ($this->server->has('ORIG_PATH_INFO')) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->server->get('ORIG_PATH_INFO');
            if ($this->server->has('QUERY_STRING') && '' != $this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
            $this->server->remove('ORIG_PATH_INFO');
        }
        // normalize the request URI to ease creating sub-requests from this request
        $this->server->set('REQUEST_URI', $requestUri);
        return $requestUri;
    }
    protected function prepareBaseUrl()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        if (basename($this->server->get('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $filename) {
            $baseUrl = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->server->get('ORIG_SCRIPT_NAME'); // 1and1 shared hosting compatibility
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $this->server->has('PHP_SELF') ? $this->server->get('PHP_SELF') : '';
            $file = $this->server->has('SCRIPT_FILENAME') ? $this->server->get('SCRIPT_FILENAME') : '';
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }
        // Does the baseUrl have anything in common with the request_uri?
        $requestUri = $this->getRequestUri();
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return $prefix;
        }
        if ($baseUrl && false !== $prefix = $this->getUrlencodedPrefix($requestUri, rtrim(dirname($baseUrl), '/'.DIRECTORY_SEPARATOR).'/')) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/'.DIRECTORY_SEPARATOR);
        }
        $truncatedRequestUri = $requestUri;
        if (false !== $pos = strpos($requestUri, '?')) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }
        $basename = basename($baseUrl);
        if (empty($basename) || !strpos(rawurldecode($truncatedRequestUri), $basename)) {
            // no match whatsoever; set it blank
            return '';
        }
        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of baseUrl. $pos !== 0 makes sure it is not matching a value
        // from PATH_INFO or QUERY_STRING
        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        return rtrim($baseUrl, '/'.DIRECTORY_SEPARATOR);
    }
    protected function prepareBasePath()
    {
        $filename = basename($this->server->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }
        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }
        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }
        return rtrim($basePath, '/');
    }
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();
        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }
        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        $pathInfo = substr($requestUri, strlen($baseUrl));
        if (null !== $baseUrl && (false === $pathInfo || '' === $pathInfo)) {
            // If substr() returns false then PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }
        return (string) $pathInfo;
    }
    private function getUrlencodedPrefix($string, $prefix)
    {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }
        $len = strlen($prefix);
        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $string, $match)) {
            return $match[0];
        }
        return false;
    }
}
// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#sending-the-response
class Response {
    protected $content;
    protected $statusCode;
    public function __construct($content = '', $status = 200, $headers = array())
    {
        $this->content = $content;
        $this->statusCode = $status;
    }
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    public function setStatusCode($code, $text = null)
    {
        $this->statusCode = $code;
    }
    public function send()
    {
        if ($this->statusCode == 404) {
            header('HTTP/1.1 404 Not Found');
        }
        echo $this->content;
    }
}
// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#redirecting-the-user
class RedirectResponse extends Response {
    protected $targetUrl;
    public function __construct($url)
    {
        $this->targetUrl = $url;
    }
    public function send()
    {
        header('Location: ' . $this->targetUrl);
    }
}
// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#serving-files
class BinaryFileResponse extends Response {
    protected $file;
    public function __construct($file)
    {
        $this->file = $file;
    }
    public function send()
    {
        header('Content-Type: ' . mime_content_type($this->file));
        readfile($this->file);
    }
}
// Credit: https://symfony.com/doc/2.8/components/http_foundation.html#creating-a-json-response
class JsonResponse extends Response {
    protected $data;
    public function __construct($data = null)
    {
        $this->data = $data;
    }
    public function setData($data = null)
    {
        $this->data = $data;
        return $this;
    }
    public function send()
    {
        header("Content-Type: application/json");
        echo json_encode($this->data);
    }
}
// Credit: https://symfony.com/doc/2.8/components/event_dispatcher.html
class EventDispatcher {
    protected $storage;
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $event_name => $method) {
            $this->storage[$event_name][] = array($subscriber, $method);
        }
    }
    public function dispatch($event, $event_name)
    {
        foreach ($this->storage[$event_name] as $each) {
            call_user_func_array($each, array($event));
        }
    }
}
class Application {
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
    /**
     * @todo, bagaimana jika path_info terdapat karakter %20. apakah perlu urldecode?.
     */
    public function run()
    {
        $http_request = self::getHttpRequest();
        $http_request_method = strtolower($http_request->server->get('REQUEST_METHOD'));
        $path_info = $http_request->getPathInfo();
        if (!isset($this->register[$http_request_method])) {
            throw new Exception('Request Method not found.');
        }
        $register = $this->register[$http_request_method];
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
        call_user_func_array($callback, $args);
    }
}
class Controller {
    public static function index()
    {
        $target_directory = ConfigFile::$target_directory;
        $request = Application::getHttpRequest();
        $path_info = $request->getPathInfo();
        $base_path = $request->getBasePath();
        $fullpath = $target_directory.$path_info;
        if (is_dir($fullpath)) {
            if (substr($path_info, -1) != '/') {
                $url = $base_path.$path_info.'/';
                $response = new RedirectResponse($url);
                return $response->send();
            }
            else {
                $dispatcher = Application::getEventDispatcher();
                $event = new BootEvent();
                $dispatcher->dispatch($event, BootEvent::NAME);
                $config = array(
                    'path_info' => $request->getPathInfo(),
                    'base_path' => $request->getBasePath(),
                );
                if ($event->sysadmin == 'register') {
                    $config['register'] = true;
                }
                $config_json = json_encode($config);
                $content = strtr(TemplateFile::indexHtml(), array(
                    '{{ config.base }}' => $config_json,
                    '{{ config.base_path }}' => $base_path,
                    ));
                $response = new Response($content);
                return $response->send();
            }
        }
        if (is_file($fullpath)) {
            $response = new BinaryFileResponse($fullpath);
            return $response->send();
        }
        $response = new Response;
        $response->setStatusCode(404);
        return $response->send();
    }
    public static function ajax()
    {
        $target_directory = ConfigFile::$target_directory;
        $request = Application::getHttpRequest();
        if ($request->request->has('action')) {
            // @todo: Jika tidak ada $_POST['directory'], maka throw error.
            $current_directory = $target_directory.$request->request->get('directory');
            $action = $request->request->get('action');
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
}
class PseudoController extends Controller {
    public static function getFile($file)
    {
        switch ($file) {
            case 'script.js':
                header('Content-Type: application/javascript; charset=utf-8');
                $content = TemplateFile::scriptJs();
                $response = new Response($content);
                $response->send();
                break;
            case '':
                // Do something.
                break;
            default:
                // Do something.
                break;
        }
    }
    public static function getTargetDirectoryFile($scheme)
    {
        switch ($scheme) {
            case 'public':
                $target_directory = ConfigFile::$target_directory;
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
                // Do something.
                break;
        }
    }
}
class TemplateFile {
    public static function indexHtml()
    {
        return <<<'EOF'
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
<!-- https://icons.getbootstrap.com/#usage -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="sticky-top">
  <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">MyFolder</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <!-- <li class="nav-item"> -->
            <!-- <a class="nav-link active" aria-current="page" href="#"><i class="bi bi-box-arrow-in-right"></i> Login</a> -->
          <!-- </li> -->
          <li class="nav-item">
            <a class="nav-link" href="#">Dashboard</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              View
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">List</a></li>
              <li><a class="dropdown-item" href="#">Details</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="#">Custom</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled">Switch</a>
          </li>
        </ul>
        <form class="d-flex" role="search">
          <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container-fluid bg-body-secondary">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="bi bi-house-door"></i></a></li>
      </ol>
    </nav>
  </div>
</div>
<table id="table-main" class="table" data-toggle="table" data-search="true">
  <thead>
    <tr>
      <th scope="col" data-field="id">#</th>
      <th scope="col" data-field="name">Name</th>
      <th scope="col" data-field="date-modified">Date Modified</th>
      <th scope="col" data-field="date-modified">Type</th>
      <th scope="col" data-field="date-modified">Size</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>
    <tr>
      <th scope="row">&nbsp;</th>
      <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>
  </tbody>
</table>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Create Account</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Welcome, SysAdmin.</p>
        <p>Create account for yourself before continue.</p>
        <form>
          <!-- <div class="mb-3"> -->
            <!-- <label for="recipient-name" class="col-form-label">Username:</label> -->
            <!-- <input type="text" class="form-control" name="sysadmin.name" id="recipient-name"> -->
          <!-- </div> -->
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Username</span>
            <input required name="sysadminName" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <div class="input-group mb-3">
            <span class="input-group-text" id="inputGroup-sizing-default">Password</span>
            <input required name="sysadminPassword" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
          </div>
          <!-- <div class="mb-3"> -->
            <!-- <label for="message-text" class="col-form-label">Password:</label> -->
            <!-- <input type="text" class="form-control" name="sysadmin.password" id="recipient-password"> -->
          <!-- </div> -->
        </form>
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
        <button id="create-account-next" type="submit" class="btn btn-primary">Next</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js" integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous"></script>
<script>
var MyFolder = MyFolder || {config:{}}
MyFolder.config = JSON.parse('{{ config.base }}')
</script>
<script src="{{ config.base_path }}/___pseudo/script.js">
</script>
</body>
</html>
EOF;
    }
    public static function scriptJs()
    {
        return <<<'EOF'
console.log(MyFolder);
url=MyFolder.config.base_path+MyFolder.config.path_info
function gotoLink(event) {
    $this = $(this);
    if ($this.data('type') == '.') {
        event.preventDefault();
        var info = $this.data('info')
        if (typeof info.directory !== 'undefined') {
            MyFolder.config.path_info = info.directory
            history.pushState({path_info: MyFolder.config.path_info}, "", MyFolder.config.base_path + info.directory);
        }
        else {
            var name = $this.data('info').name
            MyFolder.config.path_info = MyFolder.config.path_info + name + '/'
            history.pushState({path_info: MyFolder.config.path_info}, "", name + '/');
        }
        refreshDirectory();
    }
}
function getClassByType(type) {
    switch (type) {
        case 'sh':
        case 'gitignore':
            return 'bi bi-file-earmark-code'
        case 'md':
            return 'bi bi-file-earmark-richtext'
        default:
            return 'bi bi-file-earmark-text'
    }
}
function drawColumnName(data) {
    console.log('drawColumnName()');
    var defer = $.Deferred();
    var $table = $('#table-main');
    var $tbody = $table.find('tbody').empty();
    for (i in data) {
        var $tr = $('<tr></tr>').data('info',data[i]).html('<th scope="row"></th>').appendTo($tbody);
        var $td = $('<td></td>').appendTo($tr);
        var href = MyFolder.config.base_path + MyFolder.config.path_info+data[i];
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .on('click',gotoLink)
            .text(data[i]).attr('href',href).appendTo($td);
        $('<td class="mtime"></td><td class="type"></td><td class="size"></td>').appendTo($tr);
    }
    // console.log('sleep 2');
    // setTimeout(function () {
        defer.resolve();
    // }, 2000);
    return defer;
}
// Credit:
// https://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable-string/20732091#20732091
function humanFileSize(size) {
    var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
}
// Credit:
// - https://gist.github.com/kubiqsk/c60207a3075104df7cc1822a95053ecd
(function(){
	var replaceChars = {
		// day
		d: function(){ return ( '0' + this.getDate() ).slice(-2) },
		D: function( locale ){ return new Intl.DateTimeFormat( locale, { weekday: 'short' } ).format( this ) },
		j: function(){ return this.getDate() },
		l: function( locale ){ return new Intl.DateTimeFormat( locale, { weekday: 'long' } ).format( this ) },
		N: function(){
			let day = this.getDay();
			return day === 0 ? 7 : day;
		},
		S: function(){
			let date = this.getDate();
			return date % 10 === 1 && date !== 11 ? 'st' : ( date % 10 === 2 && date !== 12 ? 'nd' : ( date % 10 === 3 && date !== 13 ? 'rd' : 'th' ) );
		},
		w: function(){ return this.getDay() },
		z: function(){ return Math.floor( ( this - new Date( this.getFullYear(), 0, 1 ) ) / 86400000 ) },
		// week
		W: function(){
			let target = new Date( this.valueOf() );
			let dayNr = ( this.getDay() + 6 ) % 7;
			target.setDate( target.getDate() - dayNr + 3 );
			let firstThursday = target.valueOf();
			target.setMonth( 0, 1 );
			if( target.getDay() !== 4 ){
				target.setMonth( 0, 1 + ( ( 4 - target.getDay() ) + 7 ) % 7 );
			}
			return Math.ceil( ( firstThursday - target ) / 604800000 ) + 1;
		},
		// month
		F: function( locale ){ return new Intl.DateTimeFormat( locale, { month: 'long' } ).format( this ) },
		m: function(){ return ( '0' + ( this.getMonth() + 1 ) ).slice(-2) },
		M: function( locale ){ return new Intl.DateTimeFormat( locale, { month: 'short' } ).format( this ) },
		n: function(){ return this.getMonth() + 1 },
		t: function(){
			let year = this.getFullYear();
			let nextMonth = this.getMonth() + 1;
			if( nextMonth === 12 ){
				year = year++;
				nextMonth = 0;
			}
			return new Date( year, nextMonth, 0 ).getDate();
		},
		// year
		L: function(){
			let year = this.getFullYear();
			return year % 400 === 0 || ( year % 100 !== 0 && year % 4 === 0 ) ? 1 : 0;
		},
		o: function(){
			let date = new Date( this.valueOf() );
			date.setDate( date.getDate() - ( ( this.getDay() + 6 ) % 7 ) + 3 );
			return date.getFullYear();
		},
		Y: function(){ return this.getFullYear() },
		y: function(){ return ( '' + this.getFullYear() ).slice(-2) },
		// time
		a: function(){ return this.getHours() < 12 ? 'am' : 'pm' },
		A: function(){ return this.getHours() < 12 ? 'AM' : 'PM' },
		B: function(){
			return ( '00' + Math.floor( ( ( ( this.getUTCHours() + 1 ) % 24 ) + this.getUTCMinutes() / 60 + this.getUTCSeconds() / 3600 ) * 1000 / 24 ) ).slice(-3);
		},
		g: function(){ return this.getHours() % 12 || 12 },
		G: function(){ return this.getHours() },
		h: function(){ return ( '0' + ( this.getHours() % 12 || 12 ) ).slice(-2) },
		H: function(){ return ( '0' + this.getHours() ).slice(-2) },
		i: function(){ return ( '0' + this.getMinutes() ).slice(-2) },
		s: function(){ return ( '0' + this.getSeconds() ).slice(-2) },
		v: function(){ return ( '00' + this.getMilliseconds() ).slice(-3) },
		// Timezone
		e: function(){ return Intl.DateTimeFormat().resolvedOptions().timeZone },
		I: function(){
			let DST = null;
			for( let i = 0; i < 12; ++i ){
				let d = new Date( this.getFullYear(), i, 1 );
				let offset = d.getTimezoneOffset();
				if( DST === null ){
					DST = offset;
				}else if( offset < DST ){
					DST = offset;
					break;
				}else if( offset > DST ){
					break;
				}
			}
			return ( this.getTimezoneOffset() === DST ) | 0;
		},
		O: function(){
			let timezoneOffset = this.getTimezoneOffset();
			return ( -timezoneOffset < 0 ? '-' : '+' ) + ( '0' + Math.floor( Math.abs( timezoneOffset / 60 ) ) ).slice(-2) + ( '0' + Math.abs( timezoneOffset % 60 ) ).slice(-2);
		},
		P: function(){
			let timezoneOffset = this.getTimezoneOffset();
			return ( -timezoneOffset < 0 ? '-' : '+' ) + ( '0' + Math.floor( Math.abs( timezoneOffset / 60 ) ) ).slice(-2) + ':' + ( '0' + Math.abs( timezoneOffset % 60 ) ).slice(-2);
		},
		T: function( locale ){
			let timeString = this.toLocaleTimeString( locale, { timeZoneName: 'short' } ).split(' ');
			let abbr = timeString[ timeString.length - 1 ];
			return abbr == 'GMT+1' ? 'CET' : ( abbr == 'GMT+2' ? 'CEST' : abbr );
		},
		Z: function(){ return -this.getTimezoneOffset() * 60 },
		// Full Date/Time
		c: function(){ return this.format('Y-m-d\\TH:i:sP') },
		r: function(){ return this.format('D, d M Y H:i:s O') },
		U: function(){ return Math.floor( this.getTime() / 1000 ) }
	}
	Date.prototype.format = function( formatStr, locale = navigator.language ){
		var date = this;
		return formatStr.replace( /(\\?)(.)/g, function( _, esc, chr ){
			return esc === '' && replaceChars[ chr ] ? replaceChars[ chr ].call( date, locale ) : chr
		})
	}
}).call( this );
function drawColumnOther(data) {
    console.log('drawColumnOther()');
    var $table = $('#table-main');
    var $tbody = $table.find('tbody');
    for (i in data) {
        var info = data[i]
        $tbody.find('tr').filter(function (i) {
            var $this = $(this);
            if ($this.data('info') == info.name) {
                if (info.type == '.') {
                    $this.find("td.type").text('File folder')
                    var $a = $this.find("td > a");
                    $a.before('<i class="bi bi-folder"></i> ');
                    var href = $a.attr('href');
                    $a.attr('href', href+'/');
                    $a.data('info',info);
                    $a.data('type',info.type);
                }
                else {
                    let ms = info.mtime * 1000
                    let d = new Date(ms)
                    $this.find("td.mtime").text(d.format('Y-m-d H:i:s'))
                    $this.find("td.size").text(humanFileSize(info.size))
                    var $a = $this.find("td > a");
                    var biclass = getClassByType(info.type)
                    if (biclass != '') {
                        $a.before('<i class="'+biclass+'"></i> ');
                    }
                    else {
                        $a.before('<i class="bi bi-filetype-'+info.type+'"></i> ');
                    }
                    $this.find("td.type").text(info.type.toUpperCase()+' File')
                    // Array.prototype.includes() not support for old browser.
                    var extensionReadByPHP = ['php', 'htaccess'];
                    if (extensionReadByPHP.includes(info.type.toLowerCase())) {
                        $a.attr('href', MyFolder.config.base_path+'/___pseudo/target_directory/public?path='+MyFolder.config.path_info+info.name);
                    }
                }
            }
        });
    }
}
url=MyFolder.config.base_path+MyFolder.config.path_info
function refreshDirectory() {
    var ls = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls',
        directory: MyFolder.config.path_info
      }
    });
    var ls_la = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'ls -la',
        directory: MyFolder.config.path_info
      }
    });
    ls.done(function (data) {
        drawColumnName(data).then(function () {
            ls_la.done(function (data) {
                drawColumnOther(data)
            })
        })
    })
    console.log('mantab');
    console.log(MyFolder);
    var array = MyFolder.config.path_info.split('/').slice(1,-1);
    var $ol = $('ol.breadcrumb').empty();
    var $li = $('<li></li>').addClass('breadcrumb-item');
    var url = MyFolder.config.base_path;
    var directory = '';
    var info = {type: '.', name: '', directory: directory+'/'}
    var $a = $('<a></a>')
        .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
        .attr('href',url+'/')
        .on('click',gotoLink)
        .data('info',info)
        .data('type',info.type)
        .text(info.name).appendTo($li);
    $('<i class="bi bi-house-door"></i>').appendTo($a);
    $li.appendTo($ol);
    for (i in array) {
        url+='/'+array[i]
        directory+='/'+array[i]
        var $li = $('<li></li>').addClass('breadcrumb-item');
        var info = {type: '.', name: array[i], directory: directory+'/'}
        var $a = $('<a></a>')
            .addClass('link-primary link-offset-2 link-underline-opacity-0 link-underline-opacity-100-hover')
            .attr('href',url+'/')
            .on('click',gotoLink)
            .data('info',info)
            .data('type',info.type)
            .text(info.name).appendTo($li);
        $li.appendTo($ol);
    }
    // $a.before('<i class="bi bi-house-door-fill"></i> ');
    //
    //house-door-fill$a.attr('href', href+'/');
    //
}
$('a.navbar-brand').attr('href',MyFolder.config.base_path);
refreshDirectory()
history.replaceState({path_info: MyFolder.config.path_info}, "", "");
window.onpopstate = (event) => {
    console.log('onpopstate Trigger()');
    var path_info = event.state.path_info;
    MyFolder.config.path_info = path_info
    refreshDirectory()
};
var options= {
    backdrop: 'static',
    keyboard: false
};
const myModal = new bootstrap.Modal(document.getElementById('exampleModal'), options)
const myModalEl = document.getElementById('exampleModal')
console.log(myModal);
if (typeof MyFolder.config.register !== 'undefined') {
    console.log('mantabkoh');
    myModal.show();
    myModalEl.addEventListener('shown.bs.modal', event => {
      // do something...
      console.log(this);
      $('#create-account-next').on('click', function (e) {
          // console.log(e);
          // console.log('abc');
          this.disabled = true
          this.innerText = 'Waiting'
          //$.post().
          let sysadminName = $('#exampleModal').find('[name=sysadminName]')[0].value;
          let sysadminPassword = $('#exampleModal').find('[name=sysadminPassword]')[0].value;
          console.log(sysadminName);
          console.log(sysadminPassword);
          // myModal.hide();
          // $('#exampleModal').find('form').submit();
          senddonk(sysadminName, sysadminPassword);
          // todo, tidka boleh kosong.
      })
    })
}
function senddonk(n,p) {
    url=MyFolder.config.base_path+'/___pseudo/user/create'
    var sed = $.ajax({
      type: "POST",
      url: url,
      data: {
        action: 'sed',
        name: n,
        pass: p
      }
    });
    sed.done(function (data) {
        console.log(data);
        if (data.success) {
            myModal.hide();
        }
    });
}
EOF;
    }
}
class FileOperations {
    protected $path;
    protected $base_name;
    public function __construct($path = null)
    {
        if (null === $path) {
            $path = __FILE__;
        }
        $this->path = $path;
        return $this;
    }
    public function getOwner()
    {
        if (!file_exists($this->path)) {
            $this->autoCreate();
        }
        $owner = fileowner($file);
        $owner_info = posix_getpwuid($fileowner);
        $owner_name = '';
        if (is_array($owner_info)) {
            $owner_name = $owner_info['name'];
        }
        return $owner_name;
    }
    public function getBaseName()
    {
        if (null === $this->base_name) {
            $this->base_name = basename($this->path);
        }
        return $this->base_name;
    }
    /**
     *
     */
    public function autoCreate()
    {
        // @todo.
        // mkdir -p dirname(path)
        // touch path
    }
    /**
     *
     */
    public static function createTemporary()
    {
        // return $this;
    }
}
class UserController {
    /**
     *
     */
    public function __construct()
    {
        // return $this;
    }
    /**
     *
     */
    public static function create()
    {
        $http_request = Application::getHttpRequest();
        $sysadmin_name = $http_request->request->get('name');
        $sysadmin_pass = $http_request->request->get('pass');
        $config = new Config;
        $config->sysadmin->name = $sysadmin_name;
        $config->sysadmin->pass = $sysadmin_pass;
        $editor = new ConfigFile(__FILE__);
        $editor->setClassName('SysAdmin', 'IjorTengab\MyFolder\Module\SysAdmin');
        $editor->hasFinalKeyword(true);
        $editor->nowDocIdentifier('EOF');
        $editor->setStaticMethod('credentials');
        $editor->set($config);
        $response = new JsonResponse(array(
            'success' => true,
            'log' => 'Success'
        ));
        $response->send();
    }
}
namespace IjorTengab\MyFolder\Module\SysAdmin;
use IjorTengab\MyFolder\Application;
use IjorTengab\MyFolder\EventSubscriberInterface;
use IjorTengab\MyFolder\BootEvent;
use IjorTengab\MyFolder\Config;
use IjorTengab\MyFolder\WriteException;
use IjorTengab\MyFolder\ConfigFile;
final class SysAdmin {
    public static function credentials()
    {
        return <<<'EOF'
.sysadmin.name
.sysadmin.pass
EOF;
    }
}
class BootSubscriber implements EventSubscriberInterface {
    public static function getSubscribedEvents()
    {
        return array(
            BootEvent::NAME => 'onBootEvent',
        );
    }
    public static function onBootEvent(BootEvent $event)
    {
        $editor = new ConfigFile(__FILE__);
        $editor->setClassName('SysAdmin', 'IjorTengab\MyFolder\Module\SysAdmin');
        $editor->hasFinalKeyword(true);
        $editor->nowDocIdentifier('EOF');
        $editor->setStaticMethod('credentials');
        $config = new Config;
        $config->parse($editor->get());
        $name = $config->sysadmin->name->value();
        $pass = $config->sysadmin->pass->value();
        if (empty($pass)) {
            $event->sysadmin = 'register';
        }
    }
}
$dispatcher = Application::getEventDispatcher();
$subscriber = new BootSubscriber();
$dispatcher->addSubscriber($subscriber);
namespace IjorTengab\MyFolder;
// $process_user = posix_getpwuid(posix_geteuid());
// $process_user_name = $process_user['name'];
// $file = new FileOperations;
// echo sprintf('The PHP process run as user %s. This file: %s, owned by %s.', $process_user_name, $file->getBaseName(), $file->getOwner());
// $temp = FileOperations::createTemporary();
// phpinfo();
// @todo, jika user tidak bisa write, dan mau tetap menjadikan sebagai browsing
// directory listing, maka buat kode di local storage.
// $file = __FILE__;
// $basename = basename($file);
// $fileowner = fileowner($file);
// $fileownername = posix_getpwuid($fileowner)['name'];
// $request = Application::getHttpRequest();
// $user = $request->server->get('USER');
$app = new Application;
$app->get('/', 'IjorTengab\MyFolder\Controller::index');
$app->post('/', 'IjorTengab\MyFolder\Controller::ajax');
$app->get('/___pseudo/{file}', 'IjorTengab\MyFolder\PseudoController::getFile');
$app->get('/___pseudo/target_directory/{scheme}', 'IjorTengab\MyFolder\PseudoController::getTargetDirectoryFile');
$app->post('/___pseudo/user/create', 'IjorTengab\MyFolder\UserController::create');
$app->run();
