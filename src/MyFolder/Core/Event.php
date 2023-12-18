<?php

namespace IjorTengab\MyFolder\Core;

class Event
{
    protected $commands = array();

    /**
     * Module yang ingin menggunakan setCommand, wajib arraynya ada dua key.
     * `command` dan `options`.
     */
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
