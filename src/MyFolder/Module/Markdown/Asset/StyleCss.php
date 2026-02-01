<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

class StyleCss
{
    public function __toString()
    {
        ob_start(); include('assets/markdown/style.css'); return ob_get_clean();
    }
}
