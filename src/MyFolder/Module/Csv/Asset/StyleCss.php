<?php

namespace IjorTengab\MyFolder\Module\Csv\Asset;

class StyleCss
{
    public function __toString()
    {
        ob_start(); include('assets/csv/style.css'); return ob_get_clean();
    }
}
