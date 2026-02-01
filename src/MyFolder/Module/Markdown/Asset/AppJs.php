<?php

namespace IjorTengab\MyFolder\Module\Markdown\Asset;

class AppJs
{
    public function __toString()
    {
        ob_start(); include('assets/markdown/app.js'); return ob_get_clean();
    }
}
