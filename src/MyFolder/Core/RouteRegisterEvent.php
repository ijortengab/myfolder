<?php

namespace IjorTengab\MyFolder\Core;

class RouteRegisterEvent extends Event
{
    const NAME = 'core.route_register.event';

    protected static $instance;

    protected $method;

    protected $pathinfo;

    protected $callback;

    /**
     *
     */
    public function __construct($method, $pathinfo)
    {
        $this->method = $method;
        $this->pathinfo = $pathinfo;
        return $this;
    }

    /**
     *
     */
    public function setPathInfo($pathinfo)
    {
        $this->pathinfo = $pathinfo;
        return $this;
    }

    /**
     *
     */
    public function getPathInfo()
    {
        return $this->pathinfo;
    }
}
