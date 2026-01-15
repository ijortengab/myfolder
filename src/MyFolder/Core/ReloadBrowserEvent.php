<?php

namespace IjorTengab\MyFolder\Core;

class ReloadBrowserEvent extends Event
{
    const NAME = 'core.reload_browser.event';

    protected static $instance;

    protected $commands = array();

    public function setCommand($array)
    {
        $this->commands[] = $array;
        return $this;
    }

    public function getCommands()
    {
        return $this->commands;
    }
}
