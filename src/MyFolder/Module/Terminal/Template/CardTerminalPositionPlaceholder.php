<?php

namespace IjorTengab\MyFolder\Module\Terminal\Template;

class CardTerminalPositionPlaceholder
{
    public function __toString()
    {
        ob_start(); include('templates/terminal/card-terminal-position-placeholder.html.twig'); return ob_get_clean();
    }
}
