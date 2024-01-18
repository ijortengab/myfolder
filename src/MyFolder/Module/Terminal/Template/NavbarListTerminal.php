<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

use IjorTengab\MyFolder\Core\Application;

class NavbarListTerminal {
    public function __toString()
    {
        return file_get_contents(Application::$cwd.'/templates/terminal/navbar-list-terminal.html.twig');
    }
}
