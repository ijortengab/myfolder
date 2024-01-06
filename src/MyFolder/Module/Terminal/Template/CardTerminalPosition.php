<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class CardTerminalPosition
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/terminal/card-terminal-position.html.twig');
    }
}
