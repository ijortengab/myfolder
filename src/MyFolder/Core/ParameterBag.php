<?php

namespace IjorTengab\MyFolder\Core;

/**
 * Based on Symfony ParameterBag version 2.8.18.
 */
class ParameterBag
{
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
        if ($this->has($key)) {
            return $this->parameters[$key];
        }
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
