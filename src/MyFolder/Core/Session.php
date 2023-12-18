<?php

namespace IjorTengab\MyFolder\Core;

class Session extends ParameterBag
{

    protected $prefix;
    protected $is_start = false;
    protected static $instance;

    public static function load()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
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

        $this->is_start = true;
        session_cache_limiter('');
        session_name(Application::SESSION_NAME);
        session_start();
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
    protected function sync()
    {
        if ($this->is_start) {
            $_SESSION[$this->prefix] = array_replace_recursive($_SESSION[$this->prefix], $this->parameters);
        }
    }
}
