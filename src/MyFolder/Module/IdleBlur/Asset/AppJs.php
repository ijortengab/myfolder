<?php

namespace IjorTengab\MyFolder\Module\IdleBlur\Asset;

class AppJs
{
    public function __toString()
    {
        ob_start(); include('assets/idle-blur/app.js'); return ob_get_clean();
    }
}
