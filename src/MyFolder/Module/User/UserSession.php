<?php

namespace IjorTengab\MyFolder\Module\User;

use IjorTengab\MyFolder\Core\Session;

class UserSession
{

    protected $is_authenticated;
    protected $is_anonymous;
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
        return $this;
    }

    /**
     *
     */
    public function isAuthenticated()
    {
        if (null === $this->is_authenticated) {
            $this->is_authenticated = Session::load()->get('logged') === true;
        }
        return $this->is_authenticated;
    }
    /**
     *
     */
    public function isAnonymous()
    {
        if (null === $this->is_anonymous) {
            $this->is_anonymous = Session::load()->get('logged') !== true;
        }
        return $this->is_anonymous;
    }
}
