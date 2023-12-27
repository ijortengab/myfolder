<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class NavbarListTerminal {
    public function __toString()
    {
        return file_get_contents(getcwd().'/templates/terminal/navbar-list-terminal.html.twig');
    }
}
