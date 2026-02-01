<?php

namespace IjorTengab\MyFolder\Module\CtrlE\Asset;

class WaterCss
{
    public function __toString()
    {
        ob_start(); include('assets/ctrl-e/water.css'); return ob_get_clean();
    }
}
