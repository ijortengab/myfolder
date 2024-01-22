<?php

namespace IjorTengab\MyFolder\Module\Index;

use IjorTengab\MyFolder\Core\Event;

class IndexInvokeCommandEvent extends Event
{
    const NAME = 'index.invoke_command.event';

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
