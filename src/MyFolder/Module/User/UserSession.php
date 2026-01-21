<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Session;
use IjorTengab\MyFolder\Core\ConfigLoader;

class UserSession
{
    protected static $instance;
    protected $is_authenticated;
    protected $is_anonymous;

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
        return $this;
    }
    public function isAuthenticated()
    {
        if (null === $this->is_authenticated) {
            $this->is_authenticated = Session::load()->get('logged') === true;
        }
        return $this->is_authenticated;
    }
    public function isAnonymous()
    {
        if (null === $this->is_anonymous) {
            $this->is_anonymous = Session::load()->get('logged') !== true;
        }
        return $this->is_anonymous;
    }

    /**
     *
     */
    public function isSysAdmin()
    {
        if ($this->isAnonymous()) {
            return false;
        }
        $config = ConfigLoader::module('user');
        $name_config = $config->sysadmin->name->value();
        $name_session = Session::load()->get('username');
        return $name_config === $name_session;
    }
}
