<?php

namespace IjorTengab\MyFolder\Core;

class Session extends ParameterBag
{
    protected static $instance;
    protected $prefix;
    protected $is_start = false;

    /**
     * Reference: http://php.net/manual/en/language.oop5.late-static-bindings.php
     */
    public static function load()
    {
        if (null === static::$instance) {
            static::$instance = new static;
        }
        return static::$instance;
    }
    public function __construct()
    {
        $prefix = Application::SESSION_NAME;
        if (isset($_SESSION)) {
            if (!array_key_exists($prefix, $_SESSION)) {
                $_SESSION[$prefix] = array();
            }
        }
        $this->prefix = $prefix;
        $this->parameters = array();
    }
    public function start()
    {
        if (!$this->is_start) {
            $this->is_start = true;
            session_cache_limiter('');
            session_name(Application::SESSION_NAME);
            session_start();
        }
        if (!array_key_exists($this->prefix, $_SESSION)) {
            $_SESSION[$this->prefix] = array();
        }
        else {
            $this->parameters = array_replace_recursive($this->parameters, $_SESSION[$this->prefix]);
        }
    }
    public function set($key, $value)
    {
        parent::set($key, $value);
        $this->sync();
    }
    public function remove($key)
    {
        parent::remove($key);
        unset($_SESSION[$this->prefix][$key]);
    }
    /**
     * https://stackoverflow.com/a/2241793
     * https://stackoverflow.com/questions/2241769/how-to-destroy-the-session-cookie-correctly-with-php/2241793#2241793
     */
    public function destroy()
    {
        // @todo, gunakan object Cookie.
        setcookie (session_name(), "", array(
            'path' => '/',
        ));
        session_destroy();
        session_write_close();
    }
    protected function sync()
    {
        if ($this->is_start) {
            $_SESSION[$this->prefix] = array_replace_recursive($_SESSION[$this->prefix], $this->parameters);
        }
    }
}
