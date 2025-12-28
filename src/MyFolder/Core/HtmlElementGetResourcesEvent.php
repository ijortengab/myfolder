<?php

namespace IjorTengab\MyFolder\Core;

class HtmlElementGetResourcesEvent extends Event
{
    const NAME = 'core.html_element_get_resources.event';

    protected static $instance;

    protected $storage;

    /**
     *
     */
    public function __construct($storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     *
     */
    public function getResources()
    {
        return $this->storage;
    }

    /**
     *
     */
    public function setResources($storage)
    {
        $this->storage = $storage;
    }
}
