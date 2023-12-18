<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class IndexEvent extends Event
{
    const NAME = 'index.event';

    protected static $instance;

    public static function load()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}
