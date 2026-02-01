<?php

namespace IjorTengab\MyFolder\Core\Asset;

class FaviconSvg
{
    public function __toString()
    {
        ob_start(); include('assets/core/favicon.svg'); return ob_get_clean();
    }
}
