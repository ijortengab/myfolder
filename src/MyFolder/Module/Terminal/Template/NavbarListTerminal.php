<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class NavbarListTerminal
{
    public function __toString()
    {
        ob_start(); include('templates/terminal/navbar-list-terminal.html.twig'); return ob_get_clean();
    }
}
