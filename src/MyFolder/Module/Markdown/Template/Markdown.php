<?php

namespace IjorTengab\MyFolder\Module\Markdown\Template;

class Markdown
{
    public function __toString()
    {
        ob_start(); include('templates/markdown/markdown.html.twig'); return ob_get_clean();
    }
}
