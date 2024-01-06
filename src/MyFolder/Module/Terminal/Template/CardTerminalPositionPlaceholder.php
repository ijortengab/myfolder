<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class CardTerminalPositionPlaceholder
{
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/terminal/card-terminal-position-placeholder.html.twig');
    }
}
