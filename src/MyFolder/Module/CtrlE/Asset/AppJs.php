<?php

namespace IjorTengab\MyFolder\Module\CtrlE\Asset;

class AppJs
{
    public function __toString()
    {
        ob_start(); include('assets/ctrl-e/app.js'); return ob_get_clean();
    }
}
