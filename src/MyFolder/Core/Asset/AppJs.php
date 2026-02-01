<?php

namespace IjorTengab\MyFolder\Core\Asset;

class AppJs
{
    public function __toString()
    {
        ob_start(); include('assets/core/app.js'); return ob_get_clean();
    }
}
