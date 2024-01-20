<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class IndexPreRenderEvent extends Event
{
    const NAME = 'index_pre_render.event';

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
