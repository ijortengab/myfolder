<?php

namespace IjorTengab\MyFolder\Module\IdleBlur\Asset;

class StyleCss
{
    public function __toString()
    {
        ob_start(); include('assets/idle-blur/style.css'); return ob_get_clean();
    }
}
