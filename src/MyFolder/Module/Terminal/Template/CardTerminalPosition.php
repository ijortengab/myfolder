<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class CardTerminalPosition
{
    public function __toString()
    {
        ob_start(); include('templates/terminal/card-terminal-position.html.twig'); return ob_get_clean();
    }
}
