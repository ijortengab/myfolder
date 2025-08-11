<?php

namespace IjorTengab\MyFolder\Core;

class Event
{
    private $propagationStopped = false;

    protected static $instance;

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

    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }
}
