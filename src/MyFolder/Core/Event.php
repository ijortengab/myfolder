<?php

namespace IjorTengab\MyFolder\Core;

class Event
{
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
