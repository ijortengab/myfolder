<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

use IjorTengab\MyFolder\Core\Application;

class CardTerminalPosition
{
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/terminal/card-terminal-position.html.twig');
    }
}
