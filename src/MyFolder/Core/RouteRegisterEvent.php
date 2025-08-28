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
    public function __construct($method, $pathinfo, $callback)
    {
        $this->method = $method;
        $this->pathinfo = $pathinfo;
        $this->callback = $callback;
        return $this;
    }

    /**
     *
     */
    public function getMethod()
    {
        return $this->method;
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

    /**
     *
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
        return $this;
    }

    /**
     *
     */
    public function getCallback()
    {
        return $this->callback;
    }
}
